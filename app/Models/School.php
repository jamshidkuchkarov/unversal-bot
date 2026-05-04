<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'city',
        'district',
        'address',
        'phone',
        'email',
        'logo_path',
        'director_name',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function bot(): HasOne
    {
        return $this->hasOne(SchoolBot::class);
    }

    public function info(): HasOne
    {
        return $this->hasOne(SchoolInfo::class);
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'school_admins')
            ->withPivot('permissions')
            ->withTimestamps();
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class);
    }

    public function olympiads(): HasMany
    {
        return $this->hasMany(Olympiad::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
