<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OlympiadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_classes' => ['required', 'string', 'regex:/^\d+(,\s*\d+)*$/'],
            'min_age' => ['nullable', 'integer', 'min:1'],
            'max_age' => ['nullable', 'integer', 'min:1'],
            'registration_start' => ['required', 'date'],
            'registration_end' => ['required', 'date', 'after_or_equal:registration_start'],
            'olympiad_date' => ['nullable', 'date'],
            'olympiad_location' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'is_free' => ['nullable', 'boolean'],
            'price' => ['nullable', 'numeric'],
            'status' => ['required', 'in:draft,published,closed,completed,cancelled'],
        ];
    }

    public function messages(): array
    {
        return [
            'target_classes.required' => 'Sinflar maydoni majburiy.',
            'target_classes.regex' => 'Sinflarni faqat vergul bilan ajratib kiriting. Masalan: 5, 6, 7',
        ];
    }
}
