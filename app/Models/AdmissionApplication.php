<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionApplication extends Model
{
    protected $fillable = [
        'school_id',
        'admission_id',
        'telegram_user_id',
        'student_full_name',
        'student_birth_date',
        'student_gender',
        'target_class',
        'target_variant',
        'education_language',
        'previous_school',
        'parent_full_name',
        'parent_phone',
        'parent_phone_2',
        'parent_relation',
        'address',
        'transition_reason',
        'documents',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'student_birth_date' => 'date',
            'documents' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'telegram_id');
    }
}
