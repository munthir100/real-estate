<?php

namespace Botble\RealEstate\Http\Requests;

use App\Rules\SaudiArabianPhoneNumber;
use Botble\Support\Http\Requests\Request;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:120|min:2',
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
