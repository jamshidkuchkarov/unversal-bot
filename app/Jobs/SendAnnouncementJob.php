<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Models\TelegramUser;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendAnnouncementJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Announcement $announcement
    ) {}

    public function handle(TelegramBotService $telegramBotService): void
    {
        $this->announcement->update(['status' => 'sending']);

        // If target is channel or group, send once to that channel/group
        if (in_array($this->announcement->target_type, ['channel', 'group'])) {
            try {
                $this->sendToChannel($telegramBotService);

                $this->announcement->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'total_recipients' => 1,
                    'sent_count' => 1,
                    'failed_count' => 0,
                ]);
            } catch (\Exception $e) {
                $this->announcement->update([
                    'status' => 'failed',
                    'total_recipients' => 1,
                    'sent_count' => 0,
                    'failed_count' => 1,
                ]);
                throw $e;
            }
            return;
        }

        // Otherwise, send to individual users
        $recipients = $this->getRecipients();
        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $user) {
            try {
                $this->sendToUser($user, $telegramBotService);
                $sentCount++;

                // Update progress
                $this->announcement->update([
                    'sent_count' => $sentCount,
                    'failed_count' => $failedCount,
                ]);

                // Small delay to see progress (remove in production)
                usleep(500000); // 0.5 second delay
            } catch (\Exception $e) {
                $failedCount++;
                Log::error('Failed to send announcement to user', [
                    'announcement_id' => $this->announcement->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->announcement->update([
            'status' => 'sent',
            'sent_at' => now(),
            'total_recipients' => $recipients->count(),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
        ]);
    }

    private function getRecipients()
    {
        $query = TelegramUser::query()->where('school_id', $this->announcement->school_id);

        return match ($this->announcement->target_type) {
            'all_users' => $query->get(),
            'specific_users' => $query->whereIn('telegram_id', $this->announcement->target_user_ids ?? [])->get(),
            'channel', 'group' => collect(), // Handled separately
            default => collect(),
        };
    }

    private function sendToUser(TelegramUser $user, TelegramBotService $telegramBotService): void
    {
        $text = ($this->announcement->title ? "📢 {$this->announcement->title}\n\n" : '') . $this->announcement->message_text;

        // Add inline buttons if exists
        $replyMarkup = [];
        if ($this->announcement->inline_buttons && is_array($this->announcement->inline_buttons)) {
            $replyMarkup = ['inline_keyboard' => $this->announcement->inline_buttons];
        }

        if ($this->announcement->media_files && count($this->announcement->media_files) > 0) {
            $firstMedia = $this->announcement->media_files[0];
            $mediaPath = storage_path('app/public/' . $firstMedia);

            if (file_exists($mediaPath)) {
                try {
                    $telegramBotService->sendPhoto(
                        $user->chat_id,
                        $mediaPath,
                        $text,
                        $replyMarkup,
                        $this->announcement->school->bot
                    );
                    return;
                } catch (\Exception $e) {
                    // If photo fails, send as text instead
                    Log::warning('Failed to send photo, sending as text', [
                        'announcement_id' => $this->announcement->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $telegramBotService->sendMessage(
            $user->chat_id,
            $text,
            $replyMarkup,
            $this->announcement->school->bot
        );
    }

    private function sendToChannel(TelegramBotService $telegramBotService): void
    {
        $chatId = $this->announcement->target_channel;

        if (empty($chatId)) {
            throw new \Exception('Target channel is not specified');
        }

        $text = ($this->announcement->title ? "📢 {$this->announcement->title}\n\n" : '') . $this->announcement->message_text;

        $replyMarkup = [];
        if ($this->announcement->inline_buttons && is_array($this->announcement->inline_buttons)) {
            $replyMarkup = ['inline_keyboard' => $this->announcement->inline_buttons];
        }

        if ($this->announcement->media_files && count($this->announcement->media_files) > 0) {
            $firstMedia = $this->announcement->media_files[0];
            $mediaPath = storage_path('app/public/' . $firstMedia);

            if (file_exists($mediaPath)) {
                try {
                    $telegramBotService->sendPhoto(
                        $chatId,
                        $mediaPath,
                        $text,
                        $replyMarkup,
                        $this->announcement->school->bot
                    );
                    return;
                } catch (\Exception $e) {
                    // If photo fails, send as text instead
                    Log::warning('Failed to send photo to channel, sending as text', [
                        'announcement_id' => $this->announcement->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $telegramBotService->sendMessage(
            $chatId,
            $text,
            $replyMarkup,
            $this->announcement->school->bot
        );
    }
}
