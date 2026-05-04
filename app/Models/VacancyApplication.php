<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class VacancyApplication extends Model
{
    protected $fillable = [
        'vacancy_id',
        'application_type',
        'school_id',
        'bot_session_id',
        'telegram_user_id',
        'full_name',
        'phone',
        'telegram_contact',
        'email',
        'age',
        'birth_date',
        'address',
        'experience',
        'experience_years',
        'education',
        'certificates',
        'skills',
        'achievements',
        'about_self',
        'subject',
        'cv_file_path',
        'photo_file_path',
        'status',
        'admin_notes',
        'response_text',
        'response_sent',
        'reviewed_at',
        'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'application_type' => 'string',
            'birth_date' => 'date',
            'response_sent' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
