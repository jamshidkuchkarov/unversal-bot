<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'school_id',
        'created_by',
        'title',
        'category',
        'subject',
        'description',
        'requirements',
        'conditions',
        'salary_min',
        'salary_max',
        'salary_note',
        'deadline',
        'work_schedule',
        'status',
        'announced_to_channel',
        'announcement_message_id',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'announced_to_channel' => 'boolean',
        ];
    }
}
