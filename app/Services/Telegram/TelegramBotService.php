<?php

namespace App\Services\Telegram;

use App\Models\SchoolBot;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Update;

class TelegramBotService
{
    public function sendMessage(string|int $chatId, string $text, ?array $replyMarkup = null, ?SchoolBot $schoolBot = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        $this->api($schoolBot)->sendMessage($payload);
    }

    public function sendPhoto(string|int $chatId, string $photoPath, string $caption = '', ?array $replyMarkup = null, ?SchoolBot $schoolBot = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'photo' => fopen($photoPath, 'r'),
            'caption' => $caption,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        $this->api($schoolBot)->sendPhoto($payload);
    }

    public function getChatMember(string $chatId, string|int $userId, ?SchoolBot $schoolBot = null): mixed
    {
        return $this->api($schoolBot)->getChatMember([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    public function webhookUpdate(SchoolBot $schoolBot): Update
    {
        return $this->api($schoolBot)->getWebhookUpdate(true);
    }

    /**
     * @throws TelegramSDKException
     */
    public function setWebhook(SchoolBot $schoolBot): bool
    {
        return $this->api($schoolBot)->setWebhook([
            'url' => $schoolBot->webhook_url,
            'allowed_updates' => ['message', 'callback_query'],
        ]);
    }

    public function api(?SchoolBot $schoolBot = null): Api
    {
        $token = $schoolBot?->bot_token ?: config('telegram.bots.mybot.token');

        return new Api($token);
    }
}
