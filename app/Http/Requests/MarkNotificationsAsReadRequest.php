<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkNotificationsAsReadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // No authentication required
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:user_activity_notifications,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'notification_ids.required' => 'The notification IDs field is required.',
            'notification_ids.array' => 'The notification IDs must be an array.',
            'notification_ids.*.exists' => 'One or more notification IDs are invalid.',
        ];
    }
}
