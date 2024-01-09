<?php

namespace Botble\RealEstate\Http\Requests;

use Illuminate\Validation\Rule;
use Botble\Base\Facades\BaseHelper;
use App\Rules\SaudiArabianPhoneNumber;
use Botble\Support\Http\Requests\Request;

class SettingRequest extends Request
{

    public function rules(): array
    {
        $rules = [

            'username' => 'required|string|max:60|min:2|unique:re_accounts,username,' . auth('account')->id(),
            'full_name' => 'required|string|max:120',
            'email' => [
                'required',
                'max:60',
                'min:6',
                'email',
                Rule::unique('re_accounts', 'email')->ignore(auth('account')->id(), 'id'),
            ],
            'phone' => [
                'required',
                Rule::unique('re_accounts', 'phone')->ignore(auth('account')->id(), 'id'),
                new SaudiArabianPhoneNumber,
            ],
            'dob' => 'max:20|sometimes',
        ];
        if (auth('account')->user()->IsBrokerOrDeveloperAccount) {
            $legalRules = [
                'val_license_number' => 'required',
                'commercial_registration' => 'required',
                'license_number' => 'required',
                'commercial_registration_file' => 'required|file|mimes:pdf',
            ];

            $rules = array_merge($rules, $legalRules);
        }

        return $rules;
    }
}
