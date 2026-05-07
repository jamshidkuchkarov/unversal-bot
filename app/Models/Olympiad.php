<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Olympiad extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'school_id',
        'created_by',
        'title',
        'subjects',
        'description',
        'cover_image',
        'target_classes',
        'min_age',
        'max_age',
        'registration_start',
        'registration_end',
        'olympiad_date',
        'olympiad_location',
        'max_participants',
        'is_free',
        'price',
        'status',
        'result_text',
        'results_published',
        'announcement_message_id',
        'announced_to_channel',
    ];

    protected function casts(): array
    {
        return [
            'subjects' => 'array',
            'target_classes' => 'array',
            'registration_start' => 'datetime',
            'registration_end' => 'datetime',
            'olympiad_date' => 'date',
            'is_free' => 'boolean',
            'price' => 'decimal:2',
            'results_published' => 'boolean',
            'announced_to_channel' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(OlympiadRegistration::class);
    }
}
