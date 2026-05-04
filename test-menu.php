<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set locale to Uzbek
app()->setLocale('uz');

echo "Testing menu translations:\n";
echo "=========================\n\n";

$keys = [
    'school_info',
    'vacancies',
    'olympiads',
    'admissions',
    'announcements',
    'settings',
];

foreach ($keys as $key) {
    $uz = __("bot.{$key}", [], 'uz');
    $ru = __("bot.{$key}", [], 'ru');
    echo "Key: bot.{$key}\n";
    echo "  UZ: {$uz}\n";
    echo "  RU: {$ru}\n\n";
}

echo "\nTesting match logic:\n";
echo "===================\n\n";

$testTexts = [
    '🏫 Maktab haqida',
    '💼 Vakansiyalar',
    '🏆 Olimpiadalar',
    '🎓 Qabul',
    '📢 E\'lonlar',
    '⚙️ Sozlamalar',
];

app()->setLocale('uz');
foreach ($testTexts as $text) {
    $matched = match ($text) {
        __('bot.school_info') => 'school_info',
        __('bot.vacancies') => 'vacancies',
        __('bot.olympiads') => 'olympiads',
        __('bot.admissions') => 'admissions',
        __('bot.announcements') => 'announcements',
        __('bot.settings') => 'settings',
        default => 'NOT MATCHED',
    };

    echo "Text: '{$text}' => {$matched}\n";
}
