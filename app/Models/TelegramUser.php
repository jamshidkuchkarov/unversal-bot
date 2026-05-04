<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    protected $fillable = [
        'school_id',
        'telegram_id',
        'chat_id',
        'username',
        'first_name',
        'last_name',
        'language_code',
        'is_active',
        'is_subscribed',
        'last_seen_at',
        'subscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_subscribed' => 'boolean',
            'last_seen_at' => 'datetime',
            'subscribed_at' => 'datetime',
        ];
    }

    public function preference(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function vacancyApplications(): HasMany
    {
        return $this->hasMany(VacancyApplication::class, 'telegram_user_id', 'telegram_id');
    }

    public function olympiadRegistrations(): HasMany
    {
        return $this->hasMany(OlympiadRegistration::class, 'telegram_user_id', 'telegram_id');
    }

    public function admissionApplications(): HasMany
    {
        return $this->hasMany(AdmissionApplication::class, 'telegram_user_id', 'telegram_id');
    }
}
