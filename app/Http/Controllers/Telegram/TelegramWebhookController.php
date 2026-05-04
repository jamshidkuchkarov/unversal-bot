<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\SchoolBot;
use App\Services\Telegram\BotUpdateHandler;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly BotUpdateHandler $handler,
        private readonly TelegramBotService $telegramBotService,
    ) {}

    public function __invoke(Request $request, SchoolBot $schoolBot): JsonResponse
    {
        abort_unless($schoolBot->is_active && filled($schoolBot->bot_token), 404);

        try {
            Log::info('telegram.webhook.received', [
                'school_bot_id' => $schoolBot->id,
                'school_id' => $schoolBot->school_id,
                'payload' => $request->json()->all(),
            ]);

            $update = $this->telegramBotService->webhookUpdate($schoolBot);
            $this->handler->handle($update, $schoolBot);
        } catch (Throwable $exception) {
            Log::error('telegram.webhook.failed', [
                'school_bot_id' => $schoolBot->id,
                'school_id' => $schoolBot->school_id,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'payload' => $request->getContent(),
            ]);

            return response()->json(['ok' => false], 500);
        }

        return response()->json(['ok' => true]);
    }
}
