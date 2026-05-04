<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolInfo extends Model
{
    protected $table = 'school_info';

    protected $fillable = [
        'school_id',
        'about_text_uz',
        'about_text_ru',
        'history_text_uz',
        'history_text_ru',
        'mission_text_uz',
        'mission_text_ru',
        'vision_text_uz',
        'vision_text_ru',
        'director_name',
        'director_photo',
        'director_message_uz',
        'director_message_ru',
        'achievements',
        'gallery_images',
        'video_url',
        'contact_phone',
        'contact_email',
        'address_uz',
        'address_ru',
        'map_latitude',
        'map_longitude',
        'working_hours',
        'social_links',
    ];

    protected function casts(): array
    {
        return [
            'achievements' => 'array',
            'gallery_images' => 'array',
            'working_hours' => 'array',
            'social_links' => 'array',
            'map_latitude' => 'decimal:8',
            'map_longitude' => 'decimal:8',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get text in specified language
     */
    public function getText(string $field, string $lang = 'uz'): ?string
    {
        $fieldName = "{$field}_{$lang}";

        return $this->$fieldName;
    }
}
