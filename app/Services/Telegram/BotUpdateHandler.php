<?php

namespace App\Services\Telegram;

use App\Models\Admission;
use App\Models\AdmissionApplication;
use App\Models\Announcement;
use App\Models\BotSession;
use App\Models\Olympiad;
use App\Models\OlympiadRegistration;
use App\Models\SchoolBot;
use App\Models\TelegramUser;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\Update;

class BotUpdateHandler
{
    public function __construct(
        private readonly TelegramBotService $telegramBotService,
        private readonly MandatorySubscriptionService $mandatorySubscriptionService,
        private readonly LanguageService $languageService,
    ) {}

    public function handle(Update $update, SchoolBot $schoolBot): void
    {
        if ($update->isType('callback_query')) {
            $this->handleCallback($update, $schoolBot);

            return;
        }

        if (! $update->isType('message')) {
            return;
        }

        $message = $update->getMessage();
        $from = $message->from;
        $chat = $message->chat;

        $user = TelegramUser::query()->updateOrCreate(
            [
                'school_id' => $schoolBot->school_id,
                'telegram_id' => (string) $from->id,
            ],
            [
                'chat_id' => (string) $chat->id,
                'username' => $from->username,
                'first_name' => $from->firstName,
                'last_name' => $from->lastName,
                'language_code' => $from->languageCode,
                'last_seen_at' => Carbon::now(),
                'is_active' => true,
            ],
        );

        $session = BotSession::query()->updateOrCreate(
            [
                'school_id' => $schoolBot->school_id,
                'telegram_user_id' => (int) $from->id,
            ],
            [
                'telegram_username' => $from->username,
                'telegram_first_name' => $from->firstName,
                'telegram_last_name' => $from->lastName,
                'last_message_at' => Carbon::now(),
            ],
        );

        $text = trim((string) $message->text);

        // Set user language early (before any message processing)
        $lang = $this->languageService->getUserLanguage($user);
        app()->setLocale($lang);

        if ($text === '/start') {
            $session->update(['state' => 'idle', 'data' => []]);
            $this->showStart($user, $schoolBot);

            return;
        }

        $missingChannels = $this->mandatorySubscriptionService->missingChannels($user, $schoolBot);

        if ($missingChannels->isNotEmpty()) {
            $this->sendSubscriptionPrompt($user->chat_id, $missingChannels, $schoolBot);

            return;
        }

        $user->forceFill([
            'is_subscribed' => true,
            'subscribed_at' => $user->subscribed_at ?? Carbon::now(),
        ])->save();

        // Handle file uploads for specific states
        if ($this->handleFileUpload($message, $session, $user, $schoolBot)) {
            return;
        }

        // Handle contact sharing for phone number states
        if ($this->handleContactShare($message, $session, $user, $schoolBot)) {
            return;
        }

        if ($this->handleState($session, $user, $text, $schoolBot)) {
            return;
        }

        match ($text) {
            __('bot.school_info') => $this->showSchoolInfo($user, $schoolBot),
            __('bot.vacancies') => $this->sendVacancies($user->chat_id, $schoolBot),
            __('bot.olympiads') => $this->sendOlympiads($user->chat_id, $schoolBot),
            __('bot.admissions') => $this->sendAdmissions($user->chat_id, $schoolBot),
            __('bot.announcements') => $this->sendAnnouncements($user->chat_id, $schoolBot),
            __('bot.settings') => $this->showSettings($user, $schoolBot),
            default => $this->sendMainMenu($user->chat_id, $schoolBot),
        };
    }

    private function handleCallback(Update $update, SchoolBot $schoolBot): void
    {
        $callback = $update->callbackQuery;
        $user = TelegramUser::query()
            ->where('school_id', $schoolBot->school_id)
            ->where('telegram_id', (string) $callback->from->id)
            ->first();

        $session = BotSession::query()
            ->where('school_id', $schoolBot->school_id)
            ->where('telegram_user_id', (int) $callback->from->id)
            ->first();

        if (! $user || ! $session) {
            return;
        }

        // Set user language for callbacks
        $lang = $this->languageService->getUserLanguage($user);
        app()->setLocale($lang);

        if ($callback->data === 'check-subscription') {
            $missingChannels = $this->mandatorySubscriptionService->missingChannels($user, $schoolBot);

            if ($missingChannels->isNotEmpty()) {
                $this->sendSubscriptionPrompt($user->chat_id, $missingChannels, $schoolBot);

                return;
            }

            $user->forceFill([
                'is_subscribed' => true,
                'subscribed_at' => $user->subscribed_at ?? Carbon::now(),
            ])->save();

            $session->update(['state' => 'idle', 'data' => []]);
            $this->telegramBotService->sendMessage($user->chat_id, $schoolBot->welcome_text ?: 'Xush kelibsiz.', $this->mainMenu($schoolBot), $schoolBot);

            return;
        }

        if (str_starts_with((string) $callback->data, 'vacancy:')) {
            $vacancyId = (int) str_replace('vacancy:', '', (string) $callback->data);
            $vacancy = Vacancy::query()->where('school_id', $schoolBot->school_id)->find($vacancyId);

            if (! $vacancy) {
                return;
            }

            $session->update([
                'state' => 'idle',
                'data' => ['vacancy_id' => $vacancy->id],
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                $this->formatVacancyDetails($vacancy),
                [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Ariza topshirish', 'callback_data' => 'vacancy_apply:'.$vacancy->id],
                        ],
                        [
                            ['text' => 'Orqaga', 'callback_data' => 'vacancies_back'],
                        ],
                    ],
                ],
                $schoolBot
            );

            return;
        }

        if (str_starts_with((string) $callback->data, 'vacancy_apply:')) {
            $vacancyId = (int) str_replace('vacancy_apply:', '', (string) $callback->data);
            $vacancy = Vacancy::query()->where('school_id', $schoolBot->school_id)->find($vacancyId);

            if (! $vacancy) {
                return;
            }

            $session->update([
                'state' => 'vacancy.full_name',
                'data' => [
                    'vacancy_id' => $vacancy->id,
                    'application_type' => 'current',
                ],
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "📝 Ariza topshirish boshlandi.\n\n1️⃣ F.I.O va aloqa uchun avval ism familiyangizni kiriting.",
                null,
                $schoolBot
            );

            return;
        }

        if ($callback->data === 'vacancy_reserve_apply') {
            $session->update([
                'state' => 'vacancy.full_name',
                'data' => [
                    'vacancy_id' => null,
                    'application_type' => 'reserve',
                ],
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "🗂 Zahira vakansiya uchun ariza topshirish boshlandi.\n\n1️⃣ F.I.O va aloqa uchun avval ism familiyangizni kiriting.",
                null,
                $schoolBot
            );

            return;
        }

        if ($callback->data === 'vacancies_back') {
            $session->update(['state' => 'idle', 'data' => []]);
            $this->sendVacancies($user->chat_id, $schoolBot);

            return;
        }

        if (str_starts_with((string) $callback->data, 'olympiad:')) {
            $olympiadId = (int) str_replace('olympiad:', '', (string) $callback->data);
            $olympiad = Olympiad::query()->where('school_id', $schoolBot->school_id)->find($olympiadId);

            if (! $olympiad) {
                return;
            }

            $session->update([
                'state' => 'idle',
                'data' => ['olympiad_id' => $olympiad->id],
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                $this->formatOlympiadDetails($olympiad),
                [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Ro`yxatdan o`tish', 'callback_data' => 'olympiad_apply:'.$olympiad->id],
                        ],
                        [
                            ['text' => 'Orqaga', 'callback_data' => 'olympiads_back'],
                        ],
                    ],
                ],
                $schoolBot
            );
            return;
        }

        if (str_starts_with((string) $callback->data, 'olympiad_apply:')) {
            $olympiadId = (int) str_replace('olympiad_apply:', '', (string) $callback->data);
            $olympiad = Olympiad::query()->where('school_id', $schoolBot->school_id)->find($olympiadId);

            if (! $olympiad) {
                return;
            }

            $session->update([
                'state' => 'olympiad.full_name',
                'data' => ['olympiad_id' => $olympiad->id],
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "🏆 Olimpiada: {$olympiad->title}\n\n👤 FIO kiriting.",
                null,
                $schoolBot
            );
            return;
        }

        if (str_starts_with((string) $callback->data, 'olympiad_class:')) {
            [$prefix, $olympiadId, $classNumber] = array_pad(explode(':', (string) $callback->data), 3, null);
            $olympiad = Olympiad::query()->where('school_id', $schoolBot->school_id)->find((int) $olympiadId);

            if (! $olympiad || ! is_numeric($classNumber)) {
                return;
            }

            $session->update([
                'state' => 'olympiad.subject_selection',
                'data' => array_merge($session->data ?? [], [
                    'olympiad_id' => $olympiad->id,
                    'class_number' => (int) $classNumber,
                    'class_letter' => null,
                ]),
            ]);

            // Get subjects from olympiad
            $subjects = $olympiad->subjects ?? [];

            if (empty($subjects)) {
                // If no subjects defined, skip to district
                $session->update([
                    'state' => 'olympiad.district',
                    'data' => array_merge($session->data ?? [], ['subject' => null]),
                ]);

                $this->telegramBotService->sendMessage(
                    $user->chat_id,
                    "📍 Tuman yoki shaharni kiriting.",
                    null,
                    $schoolBot
                );
                return;
            }

            // Build subject keyboard from olympiad subjects
            $subjectKeyboard = [];
            foreach (array_chunk($subjects, 2) as $chunk) {
                $row = [];
                foreach ($chunk as $subject) {
                    $row[] = [
                        'text' => $subject,
                        'callback_data' => 'olympiad_subject:'.$olympiad->id.':'.urlencode($subject),
                    ];
                }
                $subjectKeyboard[] = $row;
            }

            $subjectKeyboard[] = [
                ['text' => '⬅️ Orqaga', 'callback_data' => 'olympiads_back'],
            ];

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "📚 Qaysi fan bo'yicha ishtirok etasiz?",
                ['inline_keyboard' => $subjectKeyboard],
                $schoolBot
            );
            return;
        }

        if (str_starts_with((string) $callback->data, 'olympiad_subject:')) {
            [$prefix, $olympiadId, $subject] = array_pad(explode(':', (string) $callback->data, 3), 3, null);
            $olympiad = Olympiad::query()->where('school_id', $schoolBot->school_id)->find((int) $olympiadId);

            if (! $olympiad || ! $subject) {
                return;
            }

            $subject = urldecode($subject);

            $session->update([
                'state' => 'olympiad.district',
                'data' => array_merge($session->data ?? [], [
                    'olympiad_id' => $olympiad->id,
                    'subject' => $subject,
                ]),
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "📍 Tuman yoki shaharni kiriting.",
                null,
                $schoolBot
            );
            return;
        }

        if ($callback->data === 'olympiads_back') {
            $session->update(['state' => 'idle', 'data' => []]);
            $this->sendOlympiads($user->chat_id, $schoolBot);
            return;
        }

        if (str_starts_with((string) $callback->data, 'admission:')) {
            $admissionId = (int) str_replace('admission:', '', (string) $callback->data);
            $admission = Admission::query()->where('school_id', $schoolBot->school_id)->find($admissionId);

            if (! $admission) {
                return;
            }

            $session->update([
                'state' => 'admission.student_full_name',
                'data' => ['admission_id' => $admission->id],
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "🎓 Qabul: {$admission->title}\n\n📝 Ariza topshirish boshlandi.\n\n👤 O'quvchining F.I.Sh ni kiriting.",
                null,
                $schoolBot
            );
            return;
        }

        if (str_starts_with((string) $callback->data, 'admission_class:')) {
            [$prefix, $admissionId, $classNumber] = array_pad(explode(':', (string) $callback->data, 3), 3, null);
            $admission = Admission::query()->where('school_id', $schoolBot->school_id)->find((int) $admissionId);

            if (! $admission || ! is_numeric($classNumber)) {
                return;
            }

            $session->update([
                'state' => 'admission.education_language',
                'data' => array_merge($session->data ?? [], [
                    'admission_id' => $admission->id,
                    'target_class' => (int) $classNumber,
                    'target_variant' => $classNumber.'-sinf',
                ]),
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "🌍 Ta`lim tilini tanlang.",
                ['inline_keyboard' => $this->buildAdmissionLanguageKeyboard($admission)],
                $schoolBot
            );

            return;
        }

        if (str_starts_with((string) $callback->data, 'admission_language:')) {
            [$prefix, $admissionId, $language] = array_pad(explode(':', (string) $callback->data, 3), 3, null);
            $admission = Admission::query()->where('school_id', $schoolBot->school_id)->find((int) $admissionId);

            if (! $admission || ! in_array($language, ['uz', 'ru'], true)) {
                return;
            }

            $session->update([
                'state' => 'admission.previous_school',
                'data' => array_merge($session->data ?? [], [
                    'admission_id' => $admission->id,
                    'education_language' => $language,
                ]),
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "🏫 Qaysi maktabdan kelmoqda?",
                null,
                $schoolBot
            );

            return;
        }

        if ($callback->data === 'main_menu') {
            $session->update(['state' => 'idle', 'data' => []]);
            $this->sendMainMenu($user->chat_id, $schoolBot);
            return;
        }

        if (str_starts_with((string) $callback->data, 'school_info:')) {
            $this->handleSchoolInfoCallback($callback->data, $user, $schoolBot);
            return;
        }

        if (str_starts_with((string) $callback->data, 'settings:')) {
            $this->handleSettingsCallback($callback->data, $user, $schoolBot);
            return;
        }

        if (str_starts_with((string) $callback->data, 'language:')) {
            $lang = str_replace('language:', '', $callback->data);
            $this->languageService->setUserLanguage($user, $lang);

            app()->setLocale($lang);
            $langName = $this->languageService->getLanguageName($lang);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                __('bot.language_changed', ['language' => $langName]),
                $this->mainMenu($schoolBot),
                $schoolBot
            );
            return;
        }
    }

    private function handleState(BotSession $session, TelegramUser $user, string $text, SchoolBot $schoolBot): bool
    {
        if ($session->state === 'idle') {
            return false;
        }

        $data = $session->data ?? [];

        \Log::info('handleState called', [
            'state' => $session->state,
            'text' => $text,
            'data' => $data,
            'user_id' => $user->id,
        ]);

        return match ($session->state) {
            'vacancy.full_name' => $this->advanceSessionWithContactRequest($session, ['full_name' => $text], 'vacancy.phone', "📞 Telefon raqamingizni yuboring.", $user->chat_id, $schoolBot),
            'vacancy.phone' => $this->handlePhoneStep($session, $text, 'vacancy.birth_date', '🎂 Tug`ilgan sanani kiriting. Masalan: 2000-12-31 yoki 31.12.2000', $user->chat_id, $schoolBot),
            'vacancy.birth_date' => $this->handleDateStep($session, $text, 'vacancy.address', '📍 Yashash manzilingizni kiriting.', $user->chat_id, $schoolBot, 'birth_date'),
            'vacancy.address' => $this->advanceSession($session, ['address' => $text], 'vacancy.experience', '💼 Ish tajribangizni yozing. Qayerda ishlagansiz va necha yil?', $user->chat_id, $schoolBot),
            'vacancy.experience' => $this->advanceSession($session, ['experience' => $text], 'vacancy.education', '🎓 Ta`limingizni yozing. Universitet va yo`nalish.', $user->chat_id, $schoolBot),
            'vacancy.education' => $this->advanceSession($session, ['education' => $text], 'vacancy.certificates', '📜 Sertifikatlaringizni yozing. IELTS, CEFR, TESOL va h.k.', $user->chat_id, $schoolBot),
            'vacancy.certificates' => $this->advanceSession($session, ['certificates' => $text], 'vacancy.skills', '🧠 Ko`nikmalaringizni yozing. Til bilimi, kompyuter, fan.', $user->chat_id, $schoolBot),
            'vacancy.skills' => $this->advanceSession($session, ['skills' => $text], 'vacancy.achievements', '🏆 Yutuqlaringizni yozing. O`quvchilar natijasi yoki shaxsiy yutuqlar.', $user->chat_id, $schoolBot),
            'vacancy.achievements' => $this->advanceSession($session, ['achievements' => $text], 'vacancy.about_self', '⭐ O`zingiz haqingizda 1-2 gap yozing.', $user->chat_id, $schoolBot),
            'vacancy.about_self' => $this->advanceSession($session, ['about_self' => $text], 'vacancy.cv', "📄 CV yoki rezyume yuboring (PDF, DOC, DOCX).\n\nAgar yo`q bo`lsa, 0 kiriting.", $user->chat_id, $schoolBot),
            'vacancy.photo' => $this->finishVacancyApplication($session, $user, $data, $schoolBot),
            'olympiad.full_name' => $this->handleOlympiadFullNameStep($session, $text, $user->chat_id, $schoolBot),
            'olympiad.district' => $this->advanceSession($session, ['district' => $text], 'olympiad.school_name_custom', '🏫 Qaysi maktabdan ekaningizni kiriting.', $user->chat_id, $schoolBot),
            'olympiad.school_name_custom' => $this->advanceSessionWithContactRequest($session, ['school_name_custom' => $text], 'olympiad.phone', '📱 Telefon raqamingizni yuboring.', $user->chat_id, $schoolBot),
            'olympiad.phone' => $this->handleOlympiadPhoneStep($session, $text, $user, $schoolBot),
            'olympiad.class_selection' => $this->remindOlympiadClassSelection($user->chat_id, $schoolBot),
            'olympiad.subject_selection' => $this->remindOlympiadSubjectSelection($user->chat_id, $schoolBot),
            'admission.student_full_name' => $this->handleAdmissionStudentNameStep($session, $text, $user->chat_id, $schoolBot),
            'admission.target_class' => $this->remindAdmissionClassSelection($user->chat_id, $schoolBot),
            'admission.education_language' => $this->remindAdmissionLanguageSelection($user->chat_id, $schoolBot),
            'admission.previous_school' => $this->advanceSession($session, ['previous_school' => $text], 'admission.student_birth_date', '🎂 O`quvchining tug`ilgan sanasini kiriting. Masalan: 2014-05-20 yoki 20.05.2014', $user->chat_id, $schoolBot),
            'admission.student_birth_date' => $this->handleDateStep($session, $text, 'admission.address', '📍 Yashash manzilini kiriting.', $user->chat_id, $schoolBot, 'student_birth_date'),
            'admission.address' => $this->advanceSession($session, ['address' => $text], 'admission.parent_full_name', '👨‍👩‍👧 Ota-ona F.I.O ni kiriting.', $user->chat_id, $schoolBot),
            'admission.parent_full_name' => $this->advanceSessionWithContactRequest($session, ['parent_full_name' => $text], 'admission.parent_phone', '📞 Telefon raqamni yuboring.', $user->chat_id, $schoolBot),
            'admission.parent_phone' => $this->handleAdmissionParentPhoneStep($session, $text, $user, $schoolBot),
            'admission.parent_phone_2' => $this->handleAdmissionAdditionalPhoneStep($session, $text, $user, $schoolBot),
            'admission.transition_reason' => $this->finishAdmissionApplication($session, $user, array_merge($data, ['transition_reason' => $text]), $schoolBot),
            default => false,
        };
    }

    private function advanceSession(BotSession $session, array $payload, string $nextState, string $question, string $chatId, SchoolBot $schoolBot, ?array $keyboard = null): bool
    {
        $session->update([
            'state' => $nextState,
            'data' => array_merge($session->data ?? [], $payload),
        ]);

        if (isset($payload['phone'])) {
            $session->update(['phone' => $payload['phone']]);
        }

        $this->telegramBotService->sendMessage($chatId, $question, $keyboard, $schoolBot);

        return true;
    }

    private function advanceSessionWithContactRequest(BotSession $session, array $payload, string $nextState, string $question, string $chatId, SchoolBot $schoolBot): bool
    {
        return $this->advanceSession($session, $payload, $nextState, $question, $chatId, $schoolBot, $this->contactRequestKeyboard());
    }

    private function handlePhoneStep(BotSession $session, string $text, string $nextState, string $question, string $chatId, SchoolBot $schoolBot): bool
    {
        $phone = $this->normalizePhone($text);

        if (! $phone) {
            $this->telegramBotService->sendMessage(
                $chatId,
                "❌ Telefon raqam noto`g`ri formatda.\n\n📱 Shu ko`rinishda yuboring:\n+998910890639",
                null,
                $schoolBot
            );

            return true;
        }

        return $this->advanceSession($session, ['phone' => $phone], $nextState, $question, $chatId, $schoolBot);
    }

    private function finishVacancyApplication(BotSession $session, TelegramUser $user, array $data, SchoolBot $schoolBot): bool
    {
        $vacancy = ! empty($data['vacancy_id'])
            ? Vacancy::query()->where('school_id', $schoolBot->school_id)->find($data['vacancy_id'])
            : null;

        VacancyApplication::query()->create([
            'vacancy_id' => $data['vacancy_id'],
            'application_type' => $data['application_type'] ?? 'current',
            'school_id' => $schoolBot->school_id,
            'bot_session_id' => $session->id,
            'telegram_user_id' => (int) $user->telegram_id,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'telegram_contact' => $data['telegram_contact'] ?? null,
            'age' => ! empty($data['birth_date']) ? Carbon::parse($data['birth_date'])->age : null,
            'birth_date' => $data['birth_date'] ?? null,
            'address' => $data['address'] ?? null,
            'experience' => $data['experience'] ?? null,
            'experience_years' => $this->extractExperienceYears($data['experience'] ?? null),
            'education' => $data['education'] ?? null,
            'certificates' => $data['certificates'] ?? null,
            'skills' => $data['skills'] ?? null,
            'achievements' => $data['achievements'] ?? null,
            'about_self' => $data['about_self'] ?? null,
            'subject' => $vacancy?->title ?? 'Zahira vakansiya',
            'cv_file_path' => $data['cv_file_path'] ?? null,
            'photo_file_path' => $data['photo_file_path'] ?? null,
            'status' => 'pending',
        ]);

        $session->update(['state' => 'idle', 'data' => []]);
        $this->telegramBotService->sendMessage(
            $user->chat_id,
            ($data['application_type'] ?? 'current') === 'reserve'
                ? "✅ Zahira vakansiya uchun arizangiz qabul qilindi!\n\nVakansiya ochilganda siz bilan bog`lanishimiz mumkin."
                : "✅ Vakansiya bo`yicha arizangiz qabul qilindi!\n\nTez orada siz bilan bog'lanamiz.",
            $this->mainMenu($schoolBot),
            $schoolBot
        );

        return true;
    }

    private function finishOlympiadRegistration(BotSession $session, TelegramUser $user, array $data, SchoolBot $schoolBot): bool
    {
        OlympiadRegistration::query()->updateOrCreate([
            'olympiad_id' => $data['olympiad_id'],
            'telegram_user_id' => (int) $user->telegram_id,
        ], [
            'school_id' => $schoolBot->school_id,
            'bot_session_id' => $session->id,
            'full_name' => $data['full_name'],
            'subject' => $data['subject'] ?? null,
            'class_number' => $data['class_number'],
            'class_letter' => $data['class_letter'],
            'phone' => $data['phone'],
            'district' => $data['district'] ?? null,
            'school_name_custom' => $data['school_name_custom'] ?? null,
            'payment_status' => 'free',
            'status' => 'registered',
        ]);

        $session->update(['state' => 'idle', 'data' => []]);

        $subjectText = isset($data['subject']) ? "\n📚 Fan: {$data['subject']}" : '';
        $this->telegramBotService->sendMessage(
            $user->chat_id,
            "✅ Olimpiada uchun ro`yxatdan o`tdingiz!{$subjectText}\n🏫 Sinf: {$data['class_number']}-sinf",
            $this->mainMenu($schoolBot),
            $schoolBot
        );

        return true;
    }

    private function showStart(TelegramUser $user, SchoolBot $schoolBot): void
    {
        $missingChannels = $this->mandatorySubscriptionService->missingChannels($user, $schoolBot);

        Log::info('showStart called', [
            'user_id' => $user->id,
            'is_subscribed' => $user->is_subscribed,
            'missing_channels_count' => $missingChannels->count(),
            'missing_channels' => $missingChannels->pluck('title')->toArray(),
        ]);

        if ($missingChannels->isNotEmpty()) {
            Log::info('Sending subscription prompt', ['chat_id' => $user->chat_id]);
            $this->sendSubscriptionPrompt($user->chat_id, $missingChannels, $schoolBot);

            return;
        }

        $this->telegramBotService->sendMessage(
            $user->chat_id,
            $schoolBot->welcome_text ?: 'Assalomu alaykum. Kerakli bo`limni tanlang.',
            $this->mainMenu($schoolBot),
            $schoolBot,
        );
    }

    private function sendSubscriptionPrompt(string $chatId, $missingChannels, SchoolBot $schoolBot): void
    {
        $buttons = [];

        foreach ($missingChannels as $channel) {
            $buttons[][] = [
                'text' => $channel->title,
                'url' => $channel->invite_link ?: ('https://t.me/'.ltrim((string) $channel->username, '@')),
            ];
        }

        $buttons[][] = ['text' => 'Tekshirish', 'callback_data' => 'check-subscription'];

        $this->telegramBotService->sendMessage($chatId, 'Botdan foydalanish uchun obuna bo`ling.', ['inline_keyboard' => $buttons], $schoolBot);
    }

    private function sendVacancies(string $chatId, SchoolBot $schoolBot): void
    {
        $vacancies = Vacancy::query()->where('school_id', $schoolBot->school_id)->where('status', 'published')->latest()->get();

        $keyboard = $vacancies->map(fn (Vacancy $vacancy) => [[
            'text' => '📌 '.$vacancy->title,
            'callback_data' => 'vacancy:'.$vacancy->id,
        ]])->all();

        $keyboard[] = [[
            'text' => '🗂 Zahira vakansiya',
            'callback_data' => 'vacancy_reserve_apply',
        ]];

        if ($vacancies->isEmpty()) {
            $this->telegramBotService->sendMessage(
                $chatId,
                "Hozircha faol vakansiyalar yo`q.\n\nLekin siz `Zahira vakansiya` orqali umumiy kadrlar bazasiga ariza qoldirishingiz mumkin.",
                ['inline_keyboard' => $keyboard],
                $schoolBot
            );

            return;
        }

        $preview = $vacancies->take(5)->values()->map(function (Vacancy $vacancy, int $index): string {
            $lines = [
                ($index + 1).'. '.'📘 '.$vacancy->title,
            ];

            if (filled($vacancy->subject)) {
                $lines[] = '   📚 Fan: '.$vacancy->subject;
            }

            if (filled($vacancy->work_schedule)) {
                $lines[] = '   🕒 Jadval: '.$vacancy->work_schedule;
            }

            if ($vacancy->deadline) {
                $lines[] = '   ⏳ Deadline: '.$vacancy->deadline->format('d.m.Y');
            }

            return implode("\n", $lines);
        })->implode("\n\n");

        $text = "📋 Vakansiyalar ro`yxati\n\n{$preview}\n\n👇 Pastdagi tugmalardan birini tanlang.\nOxiridagi `Zahira vakansiya` tugmasi orqali umumiy kadrlar bazasiga ariza qoldirish mumkin.";

        $this->telegramBotService->sendMessage($chatId, $text, ['inline_keyboard' => $keyboard], $schoolBot);
    }

    private function formatVacancyDetails(Vacancy $vacancy): string
    {
        $lines = [
            $vacancy->title,
        ];

        if (filled($vacancy->subject)) {
            $lines[] = "Fan: {$vacancy->subject}";
        }

        if (filled($vacancy->work_schedule)) {
            $lines[] = "Ish jadvali: {$vacancy->work_schedule}";
        }

        if ($vacancy->deadline) {
            $lines[] = 'Deadline: '.$vacancy->deadline->format('d.m.Y');
        }

        if ($vacancy->salary_min || $vacancy->salary_max || filled($vacancy->salary_note)) {
            $salary = trim(sprintf(
                '%s - %s',
                $vacancy->salary_min ? number_format((float) $vacancy->salary_min, 0, '.', ' ') : '?',
                $vacancy->salary_max ? number_format((float) $vacancy->salary_max, 0, '.', ' ') : '?'
            ));

            $lines[] = 'Maosh: '.trim($salary.' '.($vacancy->salary_note ?? ''));
        }

        if (filled($vacancy->description)) {
            $lines[] = '';
            $lines[] = 'Tavsif:';
            $lines[] = $this->plainText($vacancy->description);
        }

        if (filled($vacancy->requirements)) {
            $lines[] = '';
            $lines[] = 'Talablar:';
            $lines[] = $this->plainText($vacancy->requirements);
        }

        if (filled($vacancy->conditions)) {
            $lines[] = '';
            $lines[] = 'Ish sharoiti:';
            $lines[] = $this->plainText($vacancy->conditions);
        }

        return collect($lines)
            ->filter(fn ($line) => $line !== null)
            ->implode("\n");
    }

    private function plainText(?string $value): string
    {
        $text = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = preg_replace('/<\/li>/i', "\n", $text);
        $text = strip_tags($text);
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);

        return trim((string) $text);
    }

    private function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/\s+/', '', trim($phone));

        if (preg_match('/^\+998\d{9}$/', $phone) === 1) {
            return $phone;
        }

        if (preg_match('/^998\d{9}$/', $phone) === 1) {
            return '+'.$phone;
        }

        return null;
    }

    private function normalizeDateInput(string $value): ?string
    {
        $value = trim($value);

        foreach (['Y-m-d', 'd.m.Y', 'd/m/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function handleDateStep(BotSession $session, string $text, string $nextState, string $question, string $chatId, SchoolBot $schoolBot, string $field): bool
    {
        $date = $this->normalizeDateInput($text);

        if (! $date) {
            $this->telegramBotService->sendMessage(
                $chatId,
                "❌ Sana noto`g`ri formatda.\n\nMasalan: 2000-12-31 yoki 31.12.2000",
                null,
                $schoolBot
            );

            return true;
        }

        return $this->advanceSession($session, [$field => $date], $nextState, $question, $chatId, $schoolBot);
    }

    private function extractExperienceYears(?string $experience): ?int
    {
        if (! $experience) {
            return null;
        }

        if (preg_match('/(\d{1,2})/', $experience, $matches) === 1) {
            return (int) $matches[1];
        }

        return null;
    }

    private function sendOlympiads(string $chatId, SchoolBot $schoolBot): void
    {
        $olympiads = Olympiad::query()->where('school_id', $schoolBot->school_id)->where('status', 'published')->orderBy('registration_start')->get();

        if ($olympiads->isEmpty()) {
            $this->telegramBotService->sendMessage($chatId, 'Hozircha faol olimpiadalar yo`q.', $this->mainMenu($schoolBot), $schoolBot);

            return;
        }

        $keyboard = $olympiads->map(fn (Olympiad $olympiad) => [[
            'text' => $olympiad->title,
            'callback_data' => 'olympiad:'.$olympiad->id,
        ]])->all();

        $this->telegramBotService->sendMessage($chatId, 'Olimpiadani tanlang, keyin mos sinfni button orqali tanlab ro`yxatdan o`ting.', ['inline_keyboard' => $keyboard], $schoolBot);
    }

    private function formatOlympiadDetails(Olympiad $olympiad): string
    {
        $lines = [
            '🏆 '.$olympiad->title,
        ];

        if (! empty($olympiad->subjects)) {
            $lines[] = '📚 Fanlar: '.collect($olympiad->subjects)->implode(', ');
        }

        if (! empty($olympiad->target_classes)) {
            $lines[] = '🏫 Sinflar: '.collect($olympiad->target_classes)->implode(', ').'-sinf';
        }

        $lines[] = '🗓 Ro`yxatdan o`tish: '.$olympiad->registration_start?->format('d.m.Y H:i').' - '.$olympiad->registration_end?->format('d.m.Y H:i');

        if ($olympiad->olympiad_date) {
            $lines[] = '📅 Olimpiada kuni: '.$olympiad->olympiad_date->format('d.m.Y');
        }

        if (filled($olympiad->olympiad_location)) {
            $lines[] = '📍 Joy: '.$olympiad->olympiad_location;
        }

        if (filled($olympiad->description)) {
            $lines[] = '';
            $lines[] = $this->plainText($olympiad->description);
        }

        return implode("\n", $lines);
    }

    private function sendAdmissions(string $chatId, SchoolBot $schoolBot): void
    {
        $admissions = Admission::query()->where('school_id', $schoolBot->school_id)->where('status', 'published')->latest()->get();

        if ($admissions->isEmpty()) {
            $this->telegramBotService->sendMessage($chatId, 'Hozircha faol qabul kampaniyalari yo`q.', $this->mainMenu($schoolBot), $schoolBot);
            return;
        }

        $keyboard = $admissions->map(fn (Admission $admission) => [[
            'text' => $admission->title . ' (' . implode(', ', $admission->target_classes ?? []) . '-sinflar)',
            'callback_data' => 'admission:' . $admission->id,
        ]])->all();

        $this->telegramBotService->sendMessage($chatId, '🎓 Qabul kampaniyasini tanlang va ariza topshiring:', ['inline_keyboard' => $keyboard], $schoolBot);
    }

    private function sendAnnouncements(string $chatId, SchoolBot $schoolBot): void
    {
        $announcements = Announcement::query()
            ->where('school_id', $schoolBot->school_id)
            ->whereIn('status', ['sent', 'scheduled'])
            ->latest()
            ->limit(5)
            ->get();

        if ($announcements->isEmpty()) {
            $this->telegramBotService->sendMessage($chatId, 'Hozircha e`lonlar yo`q.', $this->mainMenu($schoolBot), $schoolBot);
            return;
        }

        foreach ($announcements as $announcement) {
            $text = ($announcement->title ? "📢 {$announcement->title}\n\n" : '') . $announcement->message_text;

            // Send media if exists
            if ($announcement->media_files && is_array($announcement->media_files) && count($announcement->media_files) > 0) {
                $this->sendAnnouncementWithMedia($chatId, $announcement, $text, $schoolBot);
            } else {
                $this->telegramBotService->sendMessage($chatId, $text, null, $schoolBot);
            }
        }

        $this->telegramBotService->sendMessage($chatId, '👆 Barcha e\'lonlar', $this->mainMenu($schoolBot), $schoolBot);
    }

    private function sendAnnouncementWithMedia(string $chatId, Announcement $announcement, string $text, SchoolBot $schoolBot): void
    {
        $mediaFiles = $announcement->media_files;
        $mediaType = $announcement->media_type;

        try {
            if (count($mediaFiles) === 1) {
                // Single media
                $filePath = storage_path('app/public/' . $mediaFiles[0]);

                if (!file_exists($filePath)) {
                    $this->telegramBotService->sendMessage($chatId, $text, null, $schoolBot);
                    return;
                }

                $file = fopen($filePath, 'r');

                if ($mediaType === 'photo') {
                    $this->telegramBotService->api($schoolBot)->sendPhoto([
                        'chat_id' => $chatId,
                        'photo' => $file,
                        'caption' => $text,
                    ]);
                } elseif ($mediaType === 'video') {
                    $this->telegramBotService->api($schoolBot)->sendVideo([
                        'chat_id' => $chatId,
                        'video' => $file,
                        'caption' => $text,
                    ]);
                } elseif ($mediaType === 'document') {
                    $this->telegramBotService->api($schoolBot)->sendDocument([
                        'chat_id' => $chatId,
                        'document' => $file,
                        'caption' => $text,
                    ]);
                }
            } else {
                // Multiple media (media group)
                $media = [];
                foreach ($mediaFiles as $index => $mediaFile) {
                    $filePath = storage_path('app/public/' . $mediaFile);

                    if (!file_exists($filePath)) {
                        continue;
                    }

                    $media[] = [
                        'type' => $mediaType === 'document' ? 'document' : $mediaType,
                        'media' => 'attach://file' . $index,
                        'caption' => $index === 0 ? $text : '',
                    ];
                }

                if (!empty($media)) {
                    $files = [];
                    foreach ($mediaFiles as $index => $mediaFile) {
                        $filePath = storage_path('app/public/' . $mediaFile);
                        if (file_exists($filePath)) {
                            $files['file' . $index] = fopen($filePath, 'r');
                        }
                    }

                    $this->telegramBotService->api($schoolBot)->sendMediaGroup([
                        'chat_id' => $chatId,
                        'media' => json_encode($media),
                    ] + $files);

                    foreach ($files as $file) {
                        fclose($file);
                    }
                }
            }
        } catch (\Exception $e) {
            // Fallback to text only
            $this->telegramBotService->sendMessage($chatId, $text, null, $schoolBot);
        }
    }

    private function showSchoolInfo(TelegramUser $user, SchoolBot $schoolBot): void
    {
        $info = $schoolBot->school->info;

        if (!$info) {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                'Maktab haqida ma\'lumot hali qo\'shilmagan.',
                $this->mainMenu($schoolBot),
                $schoolBot
            );
            return;
        }

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📖 Umumiy ma\'lumot', 'callback_data' => 'school_info:about'],
                ],
                [
                    ['text' => '📜 Tarix', 'callback_data' => 'school_info:history'],
                ],
                [
                    ['text' => '🎯 Missiya va Viziya', 'callback_data' => 'school_info:mission'],
                ],
                [
                    ['text' => '👨‍💼 Direktor', 'callback_data' => 'school_info:director'],
                ],
                [
                    ['text' => '📸 Galereya', 'callback_data' => 'school_info:gallery'],
                ],
                [
                    ['text' => '📹 Video', 'callback_data' => 'school_info:video'],
                ],
                [
                    ['text' => '📞 Kontaktlar', 'callback_data' => 'school_info:contacts'],
                ],
                [
                    ['text' => '📍 Manzil', 'callback_data' => 'school_info:location'],
                ],
                [
                    ['text' => '⬅️ Orqaga', 'callback_data' => 'main_menu'],
                ],
            ],
        ];

        $text = "🏫 {$schoolBot->school->name}\n\nQuyidagi bo'limlardan birini tanlang:";

        $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
    }

    private function handleSchoolInfoCallback(string $callbackData, TelegramUser $user, SchoolBot $schoolBot): void
    {
        $info = $schoolBot->school->info;

        if (!$info) {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                'Maktab haqida ma\'lumot hali qo\'shilmagan.',
                $this->mainMenu($schoolBot),
                $schoolBot
            );
            return;
        }

        $action = str_replace('school_info:', '', $callbackData);
        $backButton = [
            'inline_keyboard' => [
                [['text' => '⬅️ Orqaga', 'callback_data' => 'school_info:menu']],
            ],
        ];

        match ($action) {
            'menu' => $this->showSchoolInfo($user, $schoolBot),
            'about' => $this->sendSchoolInfoText($user, $schoolBot, $info->about_text_uz, '📖 Maktab haqida', $backButton),
            'history' => $this->sendSchoolInfoText($user, $schoolBot, $info->history_text_uz, '📜 Maktab tarixi', $backButton),
            'mission' => $this->sendMissionVision($user, $schoolBot, $info, $backButton),
            'director' => $this->sendDirectorInfo($user, $schoolBot, $info, $backButton),
            'gallery' => $this->sendGallery($user, $schoolBot, $info, $backButton),
            'video' => $this->sendVideo($user, $schoolBot, $info, $backButton),
            'contacts' => $this->sendContacts($user, $schoolBot, $info, $backButton),
            'location' => $this->sendLocation($user, $schoolBot, $info, $backButton),
            default => null,
        };
    }

    private function sendSchoolInfoText(TelegramUser $user, SchoolBot $schoolBot, ?string $text, string $title, array $keyboard): void
    {
        $message = $text ? "{$title}\n\n{$text}" : "{$title}\n\nMa'lumot hali qo'shilmagan.";
        $this->telegramBotService->sendMessage($user->chat_id, $message, $keyboard, $schoolBot);
    }

    private function sendMissionVision(TelegramUser $user, SchoolBot $schoolBot, $info, array $keyboard): void
    {
        $text = "🎯 Missiya va Viziya\n\n";

        if ($info->mission_text_uz) {
            $text .= "📌 Missiya:\n{$info->mission_text_uz}\n\n";
        }

        if ($info->vision_text_uz) {
            $text .= "🔭 Viziya:\n{$info->vision_text_uz}";
        }

        if (!$info->mission_text_uz && !$info->vision_text_uz) {
            $text .= "Ma'lumot hali qo'shilmagan.";
        }

        $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
    }

    private function sendDirectorInfo(TelegramUser $user, SchoolBot $schoolBot, $info, array $keyboard): void
    {
        $text = "👨‍💼 Direktor\n\n";

        if ($info->director_name) {
            $text .= "FIO: {$info->director_name}\n\n";
        }

        if ($info->director_message_uz) {
            $text .= "Xabar:\n{$info->director_message_uz}";
        }

        if (!$info->director_name && !$info->director_message_uz) {
            $text .= "Ma'lumot hali qo'shilmagan.";
        }

        // Check if director photo exists and is a valid file
        if ($info->director_photo && file_exists(storage_path('app/public/' . $info->director_photo))) {
            try {
                $photoPath = storage_path('app/public/' . $info->director_photo);

                $this->telegramBotService->api($schoolBot)->sendPhoto([
                    'chat_id' => $user->chat_id,
                    'photo' => \Telegram\Bot\FileUpload\InputFile::create($photoPath),
                    'caption' => $text,
                    'reply_markup' => json_encode($keyboard),
                ]);
            } catch (\Exception $e) {
                // If photo upload fails, send text only
                \Log::error('Failed to send director photo', ['error' => $e->getMessage()]);
                $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
            }
        } else {
            $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
        }
    }

    private function sendGallery(TelegramUser $user, SchoolBot $schoolBot, $info, array $keyboard): void
    {
        if (!$info->gallery_images || count($info->gallery_images) === 0) {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "📸 Galereya\n\nRasmlar hali qo'shilmagan.",
                $keyboard,
                $schoolBot
            );
            return;
        }

        $validImages = [];
        foreach ($info->gallery_images as $image) {
            if (file_exists(storage_path('app/public/' . $image))) {
                $validImages[] = $image;
            }
        }

        if (empty($validImages)) {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "📸 Galereya\n\nRasmlar topilmadi.",
                $keyboard,
                $schoolBot
            );
            return;
        }

        foreach ($validImages as $index => $image) {
            $isLast = $index === count($validImages) - 1;
            try {
                $photoPath = storage_path('app/public/' . $image);
                $this->telegramBotService->api($schoolBot)->sendPhoto([
                    'chat_id' => $user->chat_id,
                    'photo' => \Telegram\Bot\FileUpload\InputFile::create($photoPath),
                    'caption' => $isLast ? '📸 Galereya' : null,
                    'reply_markup' => $isLast ? json_encode($keyboard) : null,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send gallery photo', ['image' => $image, 'error' => $e->getMessage()]);
                if ($isLast) {
                    $this->telegramBotService->sendMessage($user->chat_id, "📸 Galereya", $keyboard, $schoolBot);
                }
            }
        }
    }

    private function sendVideo(TelegramUser $user, SchoolBot $schoolBot, $info, array $keyboard): void
    {
        $text = "📹 Video\n\n";

        if ($info->video_url) {
            $text .= "Video: {$info->video_url}";
        } else {
            $text .= "Video hali qo'shilmagan.";
        }

        $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
    }

    private function sendContacts(TelegramUser $user, SchoolBot $schoolBot, $info, array $keyboard): void
    {
        $text = "📞 Kontaktlar\n\n";

        if ($info->contact_phone) {
            $text .= "📱 Telefon: {$info->contact_phone}\n";
        }

        if ($info->contact_email) {
            $text .= "📧 Email: {$info->contact_email}\n";
        }

        if ($info->address_uz) {
            $text .= "\n📍 Manzil:\n{$info->address_uz}";
        }

        if (!$info->contact_phone && !$info->contact_email && !$info->address_uz) {
            $text .= "Ma'lumot hali qo'shilmagan.";
        }

        $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
    }

    private function sendLocation(TelegramUser $user, SchoolBot $schoolBot, $info, array $keyboard): void
    {
        if ($info->map_latitude && $info->map_longitude) {
            $this->telegramBotService->api($schoolBot)->sendLocation([
                'chat_id' => $user->chat_id,
                'latitude' => (float) $info->map_latitude,
                'longitude' => (float) $info->map_longitude,
                'reply_markup' => json_encode($keyboard),
            ]);
        } else {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "📍 Manzil\n\nXarita koordinatalari hali qo'shilmagan.",
                $keyboard,
                $schoolBot
            );
        }
    }

    private function showSettings(TelegramUser $user, SchoolBot $schoolBot): void
    {
        $lang = $this->languageService->getUserLanguage($user);
        $langName = $this->languageService->getLanguageName($lang);
        $notificationsEnabled = $user->preference?->notifications_enabled ?? true;

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => __('bot.language') . ": {$langName}", 'callback_data' => 'settings:language'],
                ],
                [
                    ['text' => __('bot.notifications') . ': ' . ($notificationsEnabled ? '✅' : '❌'), 'callback_data' => 'settings:notifications'],
                ],
                [
                    ['text' => __('bot.profile'), 'callback_data' => 'settings:profile'],
                ],
                [
                    ['text' => __('bot.my_applications'), 'callback_data' => 'settings:applications'],
                ],
                [
                    ['text' => __('bot.help'), 'callback_data' => 'settings:help'],
                ],
                [
                    ['text' => __('bot.back'), 'callback_data' => 'main_menu'],
                ],
            ],
        ];

        $this->telegramBotService->sendMessage(
            $user->chat_id,
            __('bot.settings_menu'),
            $keyboard,
            $schoolBot
        );
    }

    private function handleSettingsCallback(string $callbackData, TelegramUser $user, SchoolBot $schoolBot): void
    {
        $action = str_replace('settings:', '', $callbackData);

        match ($action) {
            'menu' => $this->showSettings($user, $schoolBot),
            'language' => $this->showLanguageSelection($user, $schoolBot),
            'notifications' => $this->toggleNotifications($user, $schoolBot),
            'profile' => $this->showProfile($user, $schoolBot),
            'applications' => $this->showMyApplications($user, $schoolBot),
            'help' => $this->showHelp($user, $schoolBot),
            default => null,
        };
    }

    private function showLanguageSelection(TelegramUser $user, SchoolBot $schoolBot): void
    {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🇺🇿 O\'zbek', 'callback_data' => 'language:uz'],
                    ['text' => '🇷🇺 Русский', 'callback_data' => 'language:ru'],
                ],
                [
                    ['text' => __('bot.back'), 'callback_data' => 'settings:menu'],
                ],
            ],
        ];

        $this->telegramBotService->sendMessage(
            $user->chat_id,
            __('bot.select_language'),
            $keyboard,
            $schoolBot
        );
    }

    private function toggleNotifications(TelegramUser $user, SchoolBot $schoolBot): void
    {
        $currentStatus = $user->preference?->notifications_enabled ?? true;
        $newStatus = !$currentStatus;

        $user->preference()->updateOrCreate(
            ['telegram_user_id' => $user->id],
            ['notifications_enabled' => $newStatus]
        );

        $message = $newStatus ? __('bot.notifications_enabled') : __('bot.notifications_disabled');

        $this->telegramBotService->sendMessage(
            $user->chat_id,
            $message,
            null,
            $schoolBot
        );

        $this->showSettings($user, $schoolBot);
    }

    private function showProfile(TelegramUser $user, SchoolBot $schoolBot): void
    {
        $lang = $this->languageService->getUserLanguage($user);
        $text = "👤 " . __('bot.profile') . "\n\n";
        $text .= "ID: {$user->telegram_id}\n";
        $text .= "Ism: {$user->first_name}";
        if ($user->last_name) {
            $text .= " {$user->last_name}";
        }
        if ($user->username) {
            $text .= "\nUsername: @{$user->username}";
        }
        $text .= "\nTil: " . $this->languageService->getLanguageName($lang);

        $keyboard = [
            'inline_keyboard' => [
                [['text' => __('bot.back'), 'callback_data' => 'settings:menu']],
            ],
        ];

        $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
    }

    private function showMyApplications(TelegramUser $user, SchoolBot $schoolBot): void
    {
        $vacancyApps = VacancyApplication::where('telegram_user_id', $user->telegram_id)->count();
        $olympiadRegs = OlympiadRegistration::where('telegram_user_id', $user->telegram_id)->count();

        $text = "📝 " . __('bot.my_applications') . "\n\n";
        $text .= "📋 Vakansiya arizalari: {$vacancyApps}\n";
        $text .= "🏆 Olimpiada ro'yxatdan o'tishlari: {$olympiadRegs}";

        $keyboard = [
            'inline_keyboard' => [
                [['text' => __('bot.back'), 'callback_data' => 'settings:menu']],
            ],
        ];

        $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
    }

    private function showHelp(TelegramUser $user, SchoolBot $schoolBot): void
    {
        $text = "❓ " . __('bot.help') . "\n\n";
        $text .= "Bot orqali siz quyidagilarni amalga oshirishingiz mumkin:\n\n";
        $text .= "🏫 Maktab haqida ma'lumot olish\n";
        $text .= "📋 Vakansiyalarga ariza topshirish\n";
        $text .= "🏆 Olimpiadalarga ro'yxatdan o'tish\n";
        $text .= "🎓 Qabul haqida ma'lumot olish\n";
        $text .= "📢 E'lonlarni ko'rish\n\n";
        $text .= "Savol yoki muammo bo'lsa, maktab bilan bog'laning.";

        $keyboard = [
            'inline_keyboard' => [
                [['text' => __('bot.back'), 'callback_data' => 'settings:menu']],
            ],
        ];

        $this->telegramBotService->sendMessage($user->chat_id, $text, $keyboard, $schoolBot);
    }

    private function finishAdmissionApplication(BotSession $session, TelegramUser $user, array $data, SchoolBot $schoolBot): bool
    {
        \Log::info('finishAdmissionApplication called', [
            'data' => $data,
            'user_id' => $user->id,
        ]);

        $admission = Admission::find($data['admission_id'] ?? null);

        if (!$admission) {
            \Log::error('Admission not found', ['admission_id' => $data['admission_id'] ?? null]);
            $this->telegramBotService->sendMessage($user->chat_id, "❌ Xatolik yuz berdi. Iltimos, qaytadan urinib ko'ring.", $this->mainMenu($schoolBot), $schoolBot);
            $session->update(['state' => 'idle', 'data' => []]);
            return true;
        }

        try {
            AdmissionApplication::create([
                'school_id' => $schoolBot->school_id,
                'admission_id' => $admission->id,
                'telegram_user_id' => (int) $user->telegram_id,
                'student_full_name' => $data['student_full_name'] ?? null,
                'student_birth_date' => $data['student_birth_date'] ?? null,
                'target_class' => $data['target_class'],
                'target_variant' => $data['target_variant'] ?? null,
                'education_language' => $data['education_language'] ?? null,
                'previous_school' => $data['previous_school'] ?? null,
                'parent_full_name' => $data['parent_full_name'] ?? null,
                'parent_phone' => $data['parent_phone'] ?? null,
                'parent_phone_2' => $data['parent_phone_2'] ?? null,
                'address' => $data['address'] ?? null,
                'transition_reason' => $data['transition_reason'] ?? null,
                'status' => 'pending',
            ]);

            $session->update(['state' => 'idle', 'data' => []]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "✅ Arizangiz qabul qilindi!\n\n📋 Qabul: {$admission->title}\n👤 O'quvchi: ".($data['student_full_name'] ?? '-')."\n🎒 Sinf: ".(($data['target_class'] ?? null) ? $data['target_class'].'-sinf' : '-')."\n🌍 Ta`lim tili: ".(($data['education_language'] ?? null) === 'ru' ? 'Rus' : (($data['education_language'] ?? null) === 'uz' ? 'O`zbek' : '-'))."\n📞 Telefon: ".($data['parent_phone'] ?? '-')."\n\nTez orada siz bilan bog'lanamiz.",
                $this->mainMenu($schoolBot),
                $schoolBot
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Admission application error', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => $user->id,
            ]);

            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "❌ Xatolik yuz berdi: " . $e->getMessage() . "\n\nIltimos, qaytadan urinib ko'ring.",
                $this->mainMenu($schoolBot),
                $schoolBot
            );

            $session->update(['state' => 'idle', 'data' => []]);
            return true;
        }
    }

    private function sendMainMenu(string $chatId, SchoolBot $schoolBot): void
    {
        $this->telegramBotService->sendMessage($chatId, $schoolBot->main_menu_text ?: 'Asosiy menyu', $this->mainMenu($schoolBot), $schoolBot);
    }

    private function mainMenu(SchoolBot $schoolBot): array
    {
        // Use translations for menu buttons
        $buttons = [
            [__('bot.school_info')],
            [__('bot.vacancies'), __('bot.olympiads')],
            [__('bot.admissions'), __('bot.announcements')],
            [__('bot.settings')],
        ];

        return Keyboard::make([
            'keyboard' => $buttons,
            'resize_keyboard' => true,
        ])->toArray();
    }

    private function contactRequestKeyboard(string $buttonText = '📱 Telefon raqamni yuborish'): array
    {
        return Keyboard::make([
            'keyboard' => [
                [
                    [
                        'text' => $buttonText,
                        'request_contact' => true,
                    ],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ])->toArray();
    }

    private function handleFileUpload($message, BotSession $session, TelegramUser $user, SchoolBot $schoolBot): bool
    {
        $state = $session->state;

        // Check if we're expecting a file
        if (!in_array($state, ['vacancy.cv', 'vacancy.photo'])) {
            return false;
        }

        $data = $session->data ?? [];

        // Handle skip (user sends "0")
        if (isset($message->text) && trim($message->text) === '0') {
            if ($state === 'vacancy.cv') {
                $session->update([
                    'state' => 'vacancy.photo',
                    'data' => array_merge($data, ['cv_file_path' => null]),
                ]);
                $this->telegramBotService->sendMessage(
                    $user->chat_id,
                    "📸 Rasmingizni yuboring.\n\nAgar yo'q bo'lsa, 0 kiriting.",
                    null,
                    $schoolBot
                );
                return true;
            } elseif ($state === 'vacancy.photo') {
                $session->update([
                    'data' => array_merge($data, ['photo_file_path' => null]),
                ]);
                return $this->finishVacancyApplication($session, $user, $session->data, $schoolBot);
            }
        }

        // Handle CV file
        if ($state === 'vacancy.cv' && isset($message->document)) {
            $document = $message->document;
            $fileId = $document->fileId;

            try {
                $file = $this->telegramBotService->api($schoolBot)->getFile(['file_id' => $fileId]);
                $filePath = $file->filePath;

                // Download file
                $fileUrl = "https://api.telegram.org/file/bot{$schoolBot->bot_token}/{$filePath}";
                $fileName = 'cv_' . $user->telegram_id . '_' . time() . '_' . $document->fileName;
                $localPath = storage_path('app/public/vacancy_files/' . $fileName);

                // Create directory if not exists
                if (!file_exists(dirname($localPath))) {
                    mkdir(dirname($localPath), 0755, true);
                }

                file_put_contents($localPath, file_get_contents($fileUrl));

                $session->update([
                    'state' => 'vacancy.photo',
                    'data' => array_merge($data, ['cv_file_path' => 'vacancy_files/' . $fileName]),
                ]);

                $this->telegramBotService->sendMessage(
                    $user->chat_id,
                    "✅ CV qabul qilindi.\n\n📸 Rasmingizni yuboring.\n\nAgar yo'q bo'lsa, 0 kiriting.",
                    null,
                    $schoolBot
                );

                return true;
            } catch (\Exception $e) {
                $this->telegramBotService->sendMessage(
                    $user->chat_id,
                    "❌ Xatolik yuz berdi. Iltimos, qaytadan urinib ko'ring.",
                    null,
                    $schoolBot
                );
                return true;
            }
        }

        // Handle photo
        if ($state === 'vacancy.photo' && isset($message->photo)) {
            $photo = end($message->photo); // Get largest photo
            $fileId = $photo->fileId;

            try {
                $file = $this->telegramBotService->api($schoolBot)->getFile(['file_id' => $fileId]);
                $filePath = $file->filePath;

                // Download file
                $fileUrl = "https://api.telegram.org/file/bot{$schoolBot->bot_token}/{$filePath}";
                $fileName = 'photo_' . $user->telegram_id . '_' . time() . '.jpg';
                $localPath = storage_path('app/public/vacancy_files/' . $fileName);

                // Create directory if not exists
                if (!file_exists(dirname($localPath))) {
                    mkdir(dirname($localPath), 0755, true);
                }

                file_put_contents($localPath, file_get_contents($fileUrl));

                $session->update([
                    'data' => array_merge($data, ['photo_file_path' => 'vacancy_files/' . $fileName]),
                ]);

                return $this->finishVacancyApplication($session, $user, $session->data, $schoolBot);
            } catch (\Exception $e) {
                $this->telegramBotService->sendMessage(
                    $user->chat_id,
                    "❌ Xatolik yuz berdi. Iltimos, qaytadan urinib ko'ring.",
                    null,
                    $schoolBot
                );
                return true;
            }
        }

        // If wrong file type
        if ($state === 'vacancy.cv') {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "❌ Iltimos, hujjat (PDF, DOC, DOCX) yuboring yoki 0 kiriting.",
                null,
                $schoolBot
            );
            return true;
        }

        if ($state === 'vacancy.photo') {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "❌ Iltimos, rasm yuboring yoki 0 kiriting.",
                null,
                $schoolBot
            );
            return true;
        }

        return false;
    }

    private function handleContactShare($message, BotSession $session, TelegramUser $user, SchoolBot $schoolBot): bool
    {
        $state = $session->state;

        // Check if we're expecting a phone number
        if (!in_array($state, ['vacancy.phone', 'olympiad.phone', 'admission.parent_phone'], true)) {
            return false;
        }

        // Check if contact was shared
        if (isset($message->contact)) {
            $phone = $message->contact->phoneNumber;

            // Ensure phone starts with +
            if (!str_starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }

            // Process based on state
            if ($state === 'vacancy.phone') {
                return $this->handlePhoneStep($session, $phone, 'vacancy.birth_date', '🎂 Tug`ilgan sanani kiriting. Masalan: 2000-12-31 yoki 31.12.2000', $user->chat_id, $schoolBot);
            } elseif ($state === 'olympiad.phone') {
                return $this->handleOlympiadPhoneStep($session, $phone, $user, $schoolBot);
            } elseif ($state === 'admission.parent_phone') {
                return $this->handleAdmissionParentPhoneStep($session, $phone, $user, $schoolBot);
            }
        }

        return false;
    }

    private function handleOlympiadFullNameStep(BotSession $session, string $text, string $chatId, SchoolBot $schoolBot): bool
    {
        $data = array_merge($session->data ?? [], ['full_name' => $text]);
        $olympiad = Olympiad::query()->where('school_id', $schoolBot->school_id)->find($data['olympiad_id'] ?? null);

        if (! $olympiad) {
            $session->update(['state' => 'idle', 'data' => []]);
            $this->telegramBotService->sendMessage($chatId, "❌ Olimpiada topilmadi. Iltimos, qaytadan tanlang.", $this->mainMenu($schoolBot), $schoolBot);

            return true;
        }

        $classOptions = collect($olympiad->target_classes ?? [])
            ->map(fn ($class) => is_numeric($class) ? (int) $class : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($classOptions === []) {
            $classOptions = range(1, 11);
        }

        $keyboardRows = [];

        foreach (array_chunk($classOptions, 3) as $chunk) {
            $row = [];

            foreach ($chunk as $classNumber) {
                $row[] = [
                    'text' => $classNumber.'-sinf',
                    'callback_data' => 'olympiad_class:'.$olympiad->id.':'.$classNumber,
                ];
            }

            $keyboardRows[] = $row;
        }

        $keyboardRows[] = [
            ['text' => '⬅️ Orqaga', 'callback_data' => 'olympiads_back'],
        ];

        $session->update([
            'state' => 'olympiad.class_selection',
            'data' => $data,
        ]);

        $this->telegramBotService->sendMessage(
            $chatId,
            "🏫 Sinfingizni tanlang:",
            ['inline_keyboard' => $keyboardRows],
            $schoolBot
        );

        return true;
    }

    private function handleOlympiadPhoneStep(BotSession $session, string $text, TelegramUser $user, SchoolBot $schoolBot): bool
    {
        $phone = $this->normalizePhone($text);

        if (! $phone) {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "❌ Telefon raqam noto`g`ri formatda.\n\n📱 Shu ko`rinishda yuboring:\n+998910890639",
                null,
                $schoolBot
            );

            return true;
        }

        $session->update([
            'state' => 'olympiad.phone',
            'data' => array_merge($session->data ?? [], ['phone' => $phone]),
            'phone' => $phone,
        ]);

        return $this->finishOlympiadRegistration($session, $user, $session->data ?? [], $schoolBot);
    }

    private function remindOlympiadClassSelection(string $chatId, SchoolBot $schoolBot): bool
    {
        $this->telegramBotService->sendMessage(
            $chatId,
            "⬇️ Iltimos, sinfni pastdagi buttonlardan tanlang.",
            null,
            $schoolBot
        );

        return true;
    }

    private function remindOlympiadSubjectSelection(string $chatId, SchoolBot $schoolBot): bool
    {
        $this->telegramBotService->sendMessage(
            $chatId,
            "⬇️ Iltimos, fanni pastdagi buttonlardan tanlang.",
            null,
            $schoolBot
        );

        return true;
    }

    private function remindAdmissionClassSelection(string $chatId, SchoolBot $schoolBot): bool
    {
        $this->telegramBotService->sendMessage(
            $chatId,
            "⬇️ Iltimos, sinf yoki yo`nalishni pastdagi buttonlardan tanlang.",
            null,
            $schoolBot
        );

        return true;
    }

    private function remindAdmissionLanguageSelection(string $chatId, SchoolBot $schoolBot): bool
    {
        $this->telegramBotService->sendMessage(
            $chatId,
            "⬇️ Iltimos, ta`lim tilini pastdagi buttonlardan tanlang.",
            null,
            $schoolBot
        );

        return true;
    }

    private function handleAdmissionStudentNameStep(BotSession $session, string $text, string $chatId, SchoolBot $schoolBot): bool
    {
        $admission = Admission::query()->where('school_id', $schoolBot->school_id)->find(data_get($session->data, 'admission_id'));

        if (! $admission) {
            $session->update(['state' => 'idle', 'data' => []]);
            $this->telegramBotService->sendMessage($chatId, "❌ Qabul topilmadi. Iltimos, qaytadan tanlang.", $this->mainMenu($schoolBot), $schoolBot);

            return true;
        }

        $session->update([
            'state' => 'admission.target_class',
            'data' => array_merge($session->data ?? [], ['student_full_name' => $text]),
        ]);

        $this->telegramBotService->sendMessage(
            $chatId,
            "🏫 Sinf yoki yo`nalishni tanlang.",
            ['inline_keyboard' => $this->buildAdmissionSelectionKeyboard($admission)],
            $schoolBot
        );

        return true;
    }

    private function handleAdmissionAdditionalPhoneStep(BotSession $session, string $text, TelegramUser $user, SchoolBot $schoolBot): bool
    {
        if (trim($text) === '0') {
            return $this->advanceSession($session, ['parent_phone_2' => null], 'admission.transition_reason', '👉 Bizning maktabimizga o`tishingiz sababi', $user->chat_id, $schoolBot);
        }

        $phone = $this->normalizePhone($text);

        if (! $phone) {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "❌ Telefon raqam noto`g`ri formatda.\n\n📱 Shu ko`rinishda yuboring:\n+998910890639",
                null,
                $schoolBot
            );

            return true;
        }

        return $this->advanceSession($session, ['parent_phone_2' => $phone], 'admission.transition_reason', '👉 Bizning maktabimizga o`tishingiz sababi', $user->chat_id, $schoolBot);
    }

    private function handleAdmissionParentPhoneStep(BotSession $session, string $text, TelegramUser $user, SchoolBot $schoolBot): bool
    {
        $phone = $this->normalizePhone($text);

        if (! $phone) {
            $this->telegramBotService->sendMessage(
                $user->chat_id,
                "❌ Telefon raqam noto`g`ri formatda.\n\n📱 Shu ko`rinishda yuboring:\n+998910890639",
                null,
                $schoolBot
            );

            return true;
        }

        return $this->advanceSession($session, ['parent_phone' => $phone], 'admission.parent_phone_2', '☎️ Qo`shimcha telefon raqami kiriting. Bo`lmasa 0 yuboring.', $user->chat_id, $schoolBot);
    }

    private function buildAdmissionSelectionKeyboard(Admission $admission): array
    {
        $rows = [];
        $classes = collect($admission->target_classes ?? [])
            ->map(fn ($class) => is_numeric($class) ? (int) $class : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach (array_chunk($classes, 3) as $chunk) {
            $row = [];

            foreach ($chunk as $classNumber) {
                $variant = $classNumber.'-sinf';

                $row[] = [
                    'text' => $variant,
                    'callback_data' => 'admission_class:'.$admission->id.':'.$classNumber,
                ];
            }

            $rows[] = $row;
        }

        $rows[] = [
            ['text' => '⬅️ Orqaga', 'callback_data' => 'main_menu'],
        ];

        return $rows;
    }

    private function buildAdmissionLanguageKeyboard(Admission $admission): array
    {
        $languages = collect($admission->admission_options ?: ['uz', 'ru'])
            ->filter(fn ($item) => in_array($item, ['uz', 'ru'], true))
            ->unique()
            ->values();

        $rows = $languages->map(fn (string $language) => [[
            'text' => $language === 'ru' ? '🇷🇺 Rus' : '🇺🇿 O`zbek',
            'callback_data' => 'admission_language:'.$admission->id.':'.$language,
        ]])->all();

        $rows[] = [
            ['text' => '⬅️ Orqaga', 'callback_data' => 'main_menu'],
        ];

        return $rows;
    }
}
