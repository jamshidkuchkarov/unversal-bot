<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnnouncementRequest;
use App\Jobs\SendAnnouncementJob;
use App\Models\Announcement;
use App\Models\TelegramUser;
use App\Services\Telegram\TelegramBotService;
use App\Support\AdminSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AdminSchoolContext $schoolContext,
        private readonly TelegramBotService $telegramBotService
    ) {}

    public function index(): View
    {
        $school = $this->schoolContext->current(request()->user());

        return view('admin.announcements.index', [
            'currentSchool' => $school,
            'availableSchools' => $this->schoolContext->schools(request()->user()),
            'announcements' => Announcement::query()->when($school, fn ($query) => $query->where('school_id', $school->id))->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.announcements.form', ['announcement' => new Announcement()]);
    }

    public function store(AnnouncementRequest $request): RedirectResponse
    {
        $school = $this->schoolContext->current($request->user());
        abort_if(! $school, 404);

        $mediaFiles = [];
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $file) {
                $path = $file->store('announcement_media', 'public');
                $mediaFiles[] = $path;
            }
        }

        Announcement::query()->create([
            ...$request->validated(),
            'school_id' => $school->id,
            'created_by' => $request->user()->id,
            'target_user_ids' => $this->parseCsv($request->input('target_user_ids')),
            'is_recurring' => $request->boolean('is_recurring'),
            'media_files' => $mediaFiles,
            'inline_buttons' => $this->parseInlineButtons($request->input('inline_buttons')),
        ]);

        return redirect()->route('admin.announcements.index')->with('status', 'E`lon yaratildi.');
    }

    public function edit(Announcement $announcement): View
    {
        $this->schoolContext->authorizeModel(request()->user(), $announcement);

        return view('admin.announcements.form', compact('announcement'));
    }

    public function update(AnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $this->schoolContext->authorizeModel($request->user(), $announcement);

        $mediaFiles = $announcement->media_files ?? [];

        if ($request->hasFile('media_files')) {
            // Delete old files if replacing
            if ($request->boolean('replace_media') && !empty($mediaFiles)) {
                foreach ($mediaFiles as $oldFile) {
                    $oldPath = storage_path('app/public/' . $oldFile);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $mediaFiles = [];
            }

            // Upload new files
            foreach ($request->file('media_files') as $file) {
                $path = $file->store('announcement_media', 'public');
                $mediaFiles[] = $path;
            }
        }

        $announcement->update([
            ...$request->validated(),
            'target_user_ids' => $this->parseCsv($request->input('target_user_ids')),
            'is_recurring' => $request->boolean('is_recurring'),
            'media_files' => $mediaFiles,
            'inline_buttons' => $this->parseInlineButtons($request->input('inline_buttons')),
        ]);

        return redirect()->route('admin.announcements.index')->with('status', 'E`lon yangilandi.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->schoolContext->authorizeModel(request()->user(), $announcement);

        $announcement->delete();

        return back()->with('status', 'E`lon o`chirildi.');
    }

    public function send(Announcement $announcement): RedirectResponse
    {
        $this->schoolContext->authorizeModel(request()->user(), $announcement);

        if (!in_array($announcement->status, ['draft', 'scheduled', 'sent', 'failed'])) {
            return back()->with('error', 'Faqat draft, scheduled, sent yoki failed statusdagi e\'lonlarni yuborish mumkin.');
        }

        // Reset counters for resending
        $announcement->update([
            'status' => 'draft',
            'sent_count' => 0,
            'failed_count' => 0,
            'total_recipients' => 0,
        ]);

        // Dispatch job to queue
        SendAnnouncementJob::dispatch($announcement);

        return redirect()->route('admin.announcements.index')
            ->with('status', 'E\'lon yuborilmoqda! Jarayon sahifasini yangilang.');
    }

    public function sendTest(Announcement $announcement): RedirectResponse
    {
        $this->schoolContext->authorizeModel(request()->user(), $announcement);

        $admin = request()->user();

        // Find admin's telegram user
        $telegramUser = TelegramUser::query()
            ->where('school_id', $announcement->school_id)
            ->where('telegram_id', $admin->telegram_id ?? null)
            ->first();

        if (!$telegramUser) {
            return back()->with('error', 'Sizning Telegram akkauntingiz topilmadi. Avval botga /start yuboring.');
        }

        try {
            $text = "🧪 TEST E'LON\n\n";
            $text .= ($announcement->title ? "📢 {$announcement->title}\n\n" : '') . $announcement->message_text;

            $replyMarkup = [];
            if ($announcement->inline_buttons && is_array($announcement->inline_buttons)) {
                $replyMarkup = ['inline_keyboard' => $announcement->inline_buttons];
            }

            if ($announcement->media_files && count($announcement->media_files) > 0) {
                $firstMedia = $announcement->media_files[0];
                $mediaPath = storage_path('app/public/' . $firstMedia);

                if (file_exists($mediaPath)) {
                    $this->telegramBotService->sendPhoto(
                        $telegramUser->chat_id,
                        $mediaPath,
                        $text,
                        $replyMarkup,
                        $announcement->school->bot
                    );
                } else {
                    $this->telegramBotService->sendMessage(
                        $telegramUser->chat_id,
                        $text,
                        $replyMarkup,
                        $announcement->school->bot
                    );
                }
            } else {
                $this->telegramBotService->sendMessage(
                    $telegramUser->chat_id,
                    $text,
                    $replyMarkup,
                    $announcement->school->bot
                );
            }

            return back()->with('status', '✅ Test xabar yuborildi! Telegram botni tekshiring.');
        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    private function parseCsv(?string $value): array
    {
        return collect(explode(',', (string) $value))->map(fn (string $item) => trim($item))->filter()->values()->all();
    }

    private function parseInlineButtons(?string $value): ?array
    {
        if (empty($value)) {
            return null;
        }

        try {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
