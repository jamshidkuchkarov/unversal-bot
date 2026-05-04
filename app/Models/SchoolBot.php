<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolBot extends Model
{
    protected $fillable = [
        'school_id',
        'bot_token',
        'bot_username',
        'bot_name',
        'webhook_url',
        'webhook_set',
        'welcome_text',
        'main_menu_text',
        'menu_buttons',
        'main_channel',
        'main_group',
        'is_active',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'webhook_set' => 'boolean',
            'menu_buttons' => 'array',
            'is_active' => 'boolean',
            'last_activity_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
