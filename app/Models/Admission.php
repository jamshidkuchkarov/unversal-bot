<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'created_by',
        'title',
        'academic_year',
        'target_classes',
        'admission_options',
        'description',
        'requirements',
        'required_documents',
        'quota',
        'accepted_count',
        'start_date',
        'end_date',
        'status',
        'announced_to_channel',
        'announcement_message_id',
    ];

    protected function casts(): array
    {
        return [
            'target_classes' => 'array',
            'admission_options' => 'array',
            'required_documents' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'announced_to_channel' => 'boolean',
        ];
    }
}
