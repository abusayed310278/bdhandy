<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'tab' => ['required', 'string', 'in:general,security,notifications'],
        ];

        if ($this->input('tab') === 'general') {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ];
            $rules['phone'] = [
                'nullable',
                'string',
                'max:20',
                Rule::unique(User::class)->ignore($this->user()->id),
            ];
            $rules['photo'] = ['nullable', 'image', 'max:2048'];
            $rules['gender'] = ['nullable', 'string', 'in:male,female,other'];
            $rules['date_of_birth'] = ['nullable', 'date'];
            $rules['preferred_language'] = ['nullable', 'string', 'exists:languages,code'];
            $rules['bio'] = ['nullable', 'string', 'max:1000'];
        }

        if ($this->input('tab') === 'notifications') {
            $rules['notification_preferences'] = ['required', 'array'];
            $rules['notification_preferences.*'] = ['boolean'];
        }

        return $rules;
    }
}
