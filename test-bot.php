<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$bot = \App\Models\SchoolBot::first();

if (!$bot) {
    echo "No bot found in database\n";
    exit(1);
}

$api = new \Telegram\Bot\Api($bot->bot_token);
$handler = app(\App\Services\Telegram\BotUpdateHandler::class);

echo "Bot started: @" . $api->getMe()->username . "\n";
echo "Press Ctrl+C to stop\n\n";

$offset = 0;

while (true) {
    try {
        $updates = $api->getUpdates(['offset' => $offset, 'timeout' => 30]);

        foreach ($updates as $update) {
            $offset = $update->updateId + 1;

            echo "[" . date('H:i:s') . "] Processing update #{$update->updateId}\n";

            try {
                $handler->handle($update, $bot);
                echo "✓ Processed successfully\n\n";
            } catch (\Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n\n";
            }
        }
    } catch (\Exception $e) {
        echo "Connection error: " . $e->getMessage() . "\n";
        sleep(5);
    }
}
