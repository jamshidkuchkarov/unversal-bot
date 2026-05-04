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

    public function sendVideo(string|int $chatId, string $videoPath, string $caption = '', ?array $replyMarkup = null, ?SchoolBot $schoolBot = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'video' => fopen($videoPath, 'r'),
            'caption' => $caption,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        $this->api($schoolBot)->sendVideo($payload);
    }

    public function sendDocument(string|int $chatId, string $documentPath, string $caption = '', ?array $replyMarkup = null, ?SchoolBot $schoolBot = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'document' => fopen($documentPath, 'r'),
            'caption' => $caption,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        $this->api($schoolBot)->sendDocument($payload);
    }

    public function sendAnimation(string|int $chatId, string $animationPath, string $caption = '', ?array $replyMarkup = null, ?SchoolBot $schoolBot = null): void
    {
        $payload = [
            'chat_id' => $chatId,
            'animation' => fopen($animationPath, 'r'),
            'caption' => $caption,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        $this->api($schoolBot)->sendAnimation($payload);
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

    /**
     * @throws TelegramSDKException
     */
    public function getMe(SchoolBot $schoolBot): array
    {
        $response = $this->api($schoolBot)->getMe();

        return $response->toArray();
    }

    public function api(?SchoolBot $schoolBot = null): Api
    {
        $token = $schoolBot?->bot_token ?: config('telegram.bots.mybot.token');

        return new Api($token);
    }
}
