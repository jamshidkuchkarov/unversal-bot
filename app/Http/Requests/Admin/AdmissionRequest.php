<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'max:20'],
            'target_classes' => ['required', 'string', 'regex:/^\d+(,\s*\d+)*$/'],
            'admission_options' => ['nullable', 'array'],
            'admission_options.*' => ['string', 'in:uz,ru'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'required_documents' => ['nullable', 'string'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,published,closed,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Kampaniya nomi majburiy.',
            'academic_year.required' => 'O\'quv yili majburiy.',
            'target_classes.required' => 'Sinflar maydoni majburiy.',
            'target_classes.regex' => 'Sinflarni faqat vergul bilan ajratib kiriting. Masalan: 1, 2, 3',
            'admission_options.*.in' => 'Faqat uz yoki ru tillarini tanlash mumkin.',
            'start_date.required' => 'Boshlanish sanasi majburiy.',
            'end_date.required' => 'Tugash sanasi majburiy.',
            'end_date.after_or_equal' => 'Tugash sanasi boshlanish sanasidan kech bo\'lishi kerak.',
        ];
    }
}
