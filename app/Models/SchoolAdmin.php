<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolAdmin extends Model
{
    protected $fillable = [
        'user_id',
        'school_id',
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }
}
