<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ism kiritish majburiy.',
            'email.required' => 'Email kiritish majburiy.',
            'email.email' => 'Email formati noto`g`ri.',
            'email.unique' => 'Bu email allaqachon ishlatilmoqda.',
            'current_password.required_with' => 'Joriy parolni kiriting.',
            'current_password.current_password' => 'Joriy parol noto`g`ri.',
            'new_password.min' => 'Yangi parol kamida 6 ta belgidan iborat bo`lishi kerak.',
            'new_password.confirmed' => 'Parol tasdiqlash mos kelmadi.',
        ];
    }
}
