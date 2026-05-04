<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsUpdateRequest;
use App\Models\SchoolBot;
use App\Support\AdminSchoolContext;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Telegram\Bot\Exceptions\TelegramSDKException;

class SettingsController extends Controller
{
    public function __construct(
        private readonly AdminSchoolContext $schoolContext,
        private readonly TelegramBotService $telegramBotService,
    ) {}

    public function edit(): View
    {
        $school = $this->schoolContext->current(request()->user());
        abort_if(! $school, 404, 'Maktab topilmadi.');

        $schoolBot = $school->bot ?? new SchoolBot(['school_id' => $school->id]);

        return view('admin.settings.edit', [
            'schoolModel' => $school,
            'schoolBot' => $schoolBot,
            'resolvedWebhookUrl' => $this->resolveWebhookUrl($schoolBot->exists ? $schoolBot : null),
            'availableSchools' => $this->schoolContext->schools(request()->user()),
            'currentSchool' => $school,
        ]);
    }

    public function update(SettingsUpdateRequest $request): RedirectResponse
    {
        $school = $this->schoolContext->current($request->user());
        abort_if(! $school, 404, 'Maktab topilmadi.');

        $data = $request->validated();

        $school->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'director_name' => $data['director_name'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $bot = $school->bot()->updateOrCreate(
            ['school_id' => $school->id],
            [
                'bot_token' => $data['bot_token'] ?? null,
                'bot_name' => $data['bot_name'] ?? $school->name,
                'bot_username' => $data['bot_username'] ?? null,
                'welcome_text' => $data['welcome_text'] ?? null,
                'main_menu_text' => $data['main_menu_text'] ?? null,
                'main_channel' => $data['main_channel'] ?? null,
                'main_group' => $data['main_group'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ],
        );

        $bot->update([
            'webhook_url' => $this->resolveWebhookUrl($bot),
        ]);

        $status = 'Asosiy bot ma`lumotlari yangilandi.';

        if (filled($bot->bot_token) && filled($bot->webhook_url)) {
            try {
                $result = $this->telegramBotService->setWebhook($bot);

                $bot->update(['webhook_set' => $result]);
                $status .= $result ? ' Webhook avtomatik ulandi.' : ' Webhook ulanmadi.';
            } catch (TelegramSDKException $exception) {
                $bot->update(['webhook_set' => false]);
                $status .= ' Webhookda xatolik: '.$exception->getMessage();
            }
        } else {
            $bot->update(['webhook_set' => false]);
            $status .= ' Token yoki webhook URL to`liq emasligi uchun webhook ulanmagan.';
        }

        return back()->with('status', $status);
    }

    private function resolveWebhookUrl(?SchoolBot $schoolBot): ?string
    {
        if (! $schoolBot?->exists) {
            return null;
        }

        $baseUrl = rtrim(
            (string) (config('services.telegram.webhook_base_url') ?: config('app.url')),
            '/',
        );

        $path = route('telegram.webhook', ['schoolBot' => $schoolBot], false);

        return $baseUrl.$path;
    }
}
