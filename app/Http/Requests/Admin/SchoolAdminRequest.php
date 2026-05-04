<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('admin')?->id;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:6'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ism kiritish majburiy.',
            'email.required' => 'Email kiritish majburiy.',
            'email.email' => 'Email formati noto`g`ri.',
            'email.unique' => 'Bu email allaqachon ishlatilmoqda.',
            'password.required' => 'Parol kiritish majburiy.',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo`lishi kerak.',
        ];
    }
}
