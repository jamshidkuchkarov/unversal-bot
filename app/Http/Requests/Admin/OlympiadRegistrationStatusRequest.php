<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OlympiadRegistrationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:registered,confirmed,cancelled,participated,absent'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
