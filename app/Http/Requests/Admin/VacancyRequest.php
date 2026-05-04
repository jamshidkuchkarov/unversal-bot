<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:teacher,technical,management,other'],
            'subject' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'conditions' => ['nullable', 'string'],
            'salary_min' => ['nullable', 'numeric'],
            'salary_max' => ['nullable', 'numeric'],
            'salary_note' => ['nullable', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'work_schedule' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published,closed,archived'],
        ];
    }
}
