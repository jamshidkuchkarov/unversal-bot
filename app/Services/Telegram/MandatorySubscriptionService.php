<?php

namespace App\Services\Telegram;

use App\Models\Channel;
use App\Models\SchoolBot;
use App\Models\TelegramUser;
use Illuminate\Support\Collection;
use Telegram\Bot\Exceptions\TelegramSDKException;

class MandatorySubscriptionService
{
    public function __construct(private readonly TelegramBotService $telegramBotService) {}

    public function requiredChannels(SchoolBot $schoolBot): Collection
    {
        return Channel::query()
            ->where('school_id', $schoolBot->school_id)
            ->where('is_required', true)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
    }

    public function missingChannels(TelegramUser $user, SchoolBot $schoolBot): Collection
    {
        return $this->requiredChannels($schoolBot)->filter(function (Channel $channel) use ($user, $schoolBot): bool {
            try {
                $member = $this->telegramBotService->getChatMember($channel->chat_id, $user->telegram_id, $schoolBot);
                $status = $member->status ?? 'left';

                \Illuminate\Support\Facades\Log::info('Channel membership check', [
                    'channel_id' => $channel->id,
                    'channel_title' => $channel->title,
                    'user_id' => $user->id,
                    'status' => $status,
                    'is_missing' => in_array($status, ['left', 'kicked'], true),
                ]);

                return in_array($status, ['left', 'kicked'], true);
            } catch (TelegramSDKException $e) {
                \Illuminate\Support\Facades\Log::warning('Channel membership check failed', [
                    'channel_id' => $channel->id,
                    'channel_title' => $channel->title,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                return true;
            }
        })->values();
    }
}
