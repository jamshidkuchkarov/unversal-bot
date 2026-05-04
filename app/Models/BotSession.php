<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotSession extends Model
{
    protected $fillable = [
        'school_id',
        'telegram_user_id',
        'telegram_username',
        'telegram_first_name',
        'telegram_last_name',
        'phone',
        'state',
        'data',
        'is_blocked',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_blocked' => 'boolean',
            'last_message_at' => 'datetime',
        ];
    }
}
