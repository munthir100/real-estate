<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Http\Requests\PropertyRequest as BaseRequest;
use Illuminate\Validation\Rule;

class AccountPropertyRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:220',
            'description' => 'nullable|string|max:350',
            'content' => 'required|string',
            'number_bedroom' => 'numeric|min:0|max:100000|nullable',
            'number_bathroom' => 'numeric|min:0|max:100000|nullable',
            'number_floor' => 'numeric|min:0|max:100000|nullable',
            'price' => 'numeric|min:0|nullable',
            'status' => Rule::in(PropertyStatusEnum::values()),
            'latitude' => ['max:20', 'nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => [
                'max:20',
                'nullable',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            'property_license_number' => 'required|string|max:255',
            'property_license_date' => 'required|date',
            'building_number' => 'required|string|max:255',
            'additional_number' => 'nullable|string|max:255',
            'has_restriction' => 'boolean',
            'has_mortgage' => 'boolean',
        ];
    }
}
