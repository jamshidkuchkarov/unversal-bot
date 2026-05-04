<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OlympiadRegistration extends Model
{
    protected $table = 'olympiad_registrations';

    protected $fillable = [
        'olympiad_id',
        'school_id',
        'bot_session_id',
        'telegram_user_id',
        'full_name',
        'class_number',
        'class_letter',
        'phone',
        'district',
        'school_name_custom',
        'payment_status',
        'payment_ref',
        'score',
        'place',
        'prize',
        'result_sent',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'result_sent' => 'boolean',
        ];
    }

    public function olympiad(): BelongsTo
    {
        return $this->belongsTo(Olympiad::class);
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'telegram_id');
    }
}
