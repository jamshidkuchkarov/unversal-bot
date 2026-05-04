<?php

namespace App\Services\Telegram;

use App\Models\TelegramUser;

class LanguageService
{
    /**
     * Get user's preferred language
     */
    public function getUserLanguage(TelegramUser $user): string
    {
        return $user->preference?->language ?? 'uz';
    }

    /**
     * Set user's preferred language
     */
    public function setUserLanguage(TelegramUser $user, string $lang): void
    {
        $user->preference()->updateOrCreate(
            ['telegram_user_id' => $user->id],
            ['language' => $lang]
        );
    }

    /**
     * Translate bot message
     */
    public function trans(string $key, array $replace = [], ?string $lang = null): string
    {
        return __("bot.{$key}", $replace, $lang);
    }

    /**
     * Get language name
     */
    public function getLanguageName(string $lang): string
    {
        return match ($lang) {
            'uz' => 'O\'zbek',
            'ru' => 'Русский',
            default => 'O\'zbek',
        };
    }

    /**
     * Get available languages
     */
    public function getAvailableLanguages(): array
    {
        return [
            'uz' => '🇺🇿 O\'zbek',
            'ru' => '🇷🇺 Русский',
        ];
    }
}
