<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\TelegramUser;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledAnnouncements extends Command
{
    protected $signature = 'announcements:send-scheduled';
    protected $description = 'Send scheduled announcements';

    public function __construct(private readonly TelegramBotService $telegramBotService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $announcements = Announcement::query()
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($announcements->isEmpty()) {
            $this->info('No scheduled announcements to send.');
            return self::SUCCESS;
        }

        foreach ($announcements as $announcement) {
            $this->info("Sending announcement: {$announcement->title}");

            try {
                $this->sendAnnouncement($announcement);
                $this->info("✅ Sent: {$announcement->title}");
            } catch (\Exception $e) {
                $this->error("❌ Failed: {$announcement->title} - {$e->getMessage()}");
                Log::error('Failed to send scheduled announcement', [
                    'announcement_id' => $announcement->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return self::SUCCESS;
    }

    private function sendAnnouncement(Announcement $announcement): void
    {
        $announcement->update(['status' => 'sending']);

        $recipients = $this->getRecipients($announcement);
        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $user) {
            try {
                $this->sendToUser($announcement, $user);
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
            }
        }

        $announcement->update([
            'status' => 'sent',
            'sent_at' => now(),
            'total_recipients' => $recipients->count(),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
        ]);
    }

    private function getRecipients(Announcement $announcement)
    {
        $query = TelegramUser::query()->where('school_id', $announcement->school_id);

        return match ($announcement->target_type) {
            'all_users' => $query->get(),
            'specific_users' => $query->whereIn('telegram_id', $announcement->target_user_ids ?? [])->get(),
            default => collect(),
        };
    }

    private function sendToUser(Announcement $announcement, TelegramUser $user): void
    {
        $text = ($announcement->title ? "📢 {$announcement->title}\n\n" : '') . $announcement->message_text;

        if ($announcement->media_files && count($announcement->media_files) > 0) {
            $firstMedia = $announcement->media_files[0];
            $mediaPath = storage_path('app/public/' . $firstMedia);

            if (file_exists($mediaPath)) {
                $this->telegramBotService->sendPhoto(
                    $user->chat_id,
                    $mediaPath,
                    $text,
                    $announcement->school->bot
                );
                return;
            }
        }

        $this->telegramBotService->sendMessage(
            $user->chat_id,
            $text,
            [],
            $announcement->school->bot
        );
    }
}
