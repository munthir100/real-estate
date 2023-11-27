<?php

namespace Botble\RealEstate\Http\Requests;

use Illuminate\Validation\Rule;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Http\Requests\PropertyRequest as BaseRequest;

class AccountOrderRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'required|regex:/^\+\d+$/',
            'note' => 'nullable|string|max:100',
            'moderation_status' => Rule::in(ModerationStatusEnum::values()),
            'unique_id' => 'nullable|string|max:120|unique:re_orders,unique_id,' . $this->route('order'),
        ];
    }
}
