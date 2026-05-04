<?php

return [
    'bots' => [
        'mybot' => [
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'certificate_path' => env('TELEGRAM_CERTIFICATE_PATH'),
            'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
            'allowed_updates' => ['message', 'callback_query'],
            'commands' => [],
        ],
    ],
    'default' => 'mybot',
    'async_requests' => false,
    'http_client_handler' => null,
    'base_bot_url' => null,
    'resolve_command_dependencies' => true,
    'commands' => [],
    'command_groups' => [],
    'shared_commands' => [],
];
