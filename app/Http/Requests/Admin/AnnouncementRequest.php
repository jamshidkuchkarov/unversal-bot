<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'message_text' => ['required', 'string'],
            'media_type' => ['required', 'in:none,photo,video,document,animation'],
            'inline_buttons' => ['nullable', 'string'],
            'target_type' => ['required', 'in:all_users,channel,group,specific_users'],
            'target_channel' => ['nullable', 'string', 'max:255'],
            'target_user_ids' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,scheduled,sending,sent,failed,cancelled'],
            'scheduled_at' => ['nullable', 'date'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurring_schedule' => ['nullable', 'string', 'max:255'],
        ];
    }
}
