<?php

namespace App\Console\Commands;

use App\Models\SchoolBot;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class GetChatIdCommand extends Command
{
    protected $signature = 'telegram:get-chat-id {channel_username}';

    protected $description = 'Get chat ID for a Telegram channel by username';

    public function handle(): int
    {
        $username = $this->argument('channel_username');
        $username = ltrim($username, '@');

        $schoolBot = SchoolBot::query()->where('is_active', true)->first();

        if (!$schoolBot) {
            $this->error('Faol bot topilmadi!');
            return self::FAILURE;
        }

        try {
            config(['telegram.bots.mybot.token' => $schoolBot->bot_token]);

            $response = Telegram::getChat(['chat_id' => '@' . $username]);

            $this->info('✅ Kanal topildi!');
            $this->line('');
            $this->line('Nomi: ' . $response->title);
            $this->line('Chat ID: ' . $response->id);
            $this->line('Username: @' . ($response->username ?? 'N/A'));
            $this->line('Turi: ' . $response->type);
            $this->line('');
            $this->info('Admin panelga kiriting: ' . $response->id);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Xatolik: ' . $e->getMessage());
            $this->line('');
            $this->warn('Eslatma:');
            $this->line('1. Bot kanalda admin bo\'lishi kerak');
            $this->line('2. Kanal username to\'g\'ri kiritilganiga ishonch hosil qiling');

            return self::FAILURE;
        }
    }
}
