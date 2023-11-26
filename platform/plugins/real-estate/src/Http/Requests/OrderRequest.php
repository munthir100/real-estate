<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class OrderRequest extends Request
{
    public function rules(): array
    {
        return [
            'phone' => 'required|regex:/^\+\d+$/',
            'note' => 'nullable|string|max:100',
            'status' => Rule::in(PropertyStatusEnum::values()),
            'moderation_status' => Rule::in(ModerationStatusEnum::values()),
            'unique_id' => 'nullable|string|max:120|unique:re_orders,unique_id,' . $this->route('order'),
        ];
    }
}
