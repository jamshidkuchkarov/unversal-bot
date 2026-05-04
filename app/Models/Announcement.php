<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'created_by',
        'title',
        'message_text',
        'media_path',
        'media_type',
        'media_files',
        'inline_buttons',
        'target_type',
        'target_channel',
        'target_user_ids',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'failed_count',
        'views_count',
        'is_recurring',
        'recurring_schedule',
    ];

    protected function casts(): array
    {
        return [
            'inline_buttons' => 'array',
            'target_user_ids' => 'array',
            'media_files' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'is_recurring' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
