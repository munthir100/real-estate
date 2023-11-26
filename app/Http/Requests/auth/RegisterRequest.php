<?php

namespace App\Http\Requests\auth;

use App\Rules\SaudiArabianPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'nullable|email|max:60|min:6|unique:re_accounts,email',
            'phone' => [
                'nullable',
                'unique:re_accounts,phone',
                function ($attribute, $value, $fail) {
                    if (empty($value) && empty(request()->input('email'))) {
                        $fail('The phone field is required when the email field is not present.');
                    }
                },
                new SaudiArabianPhoneNumber,
            ],

            'password' => 'required|string|min:6|confirmed',
            'account_type_id' => 'required|integer|exists:re_account_types,id',
        ];
    }
}
