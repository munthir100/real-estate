<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Order;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DuplicateOrderController extends BaseController
{
    public function __invoke(int|string $id, BaseHttpResponse $response)
    {
        $order = Order::query()->findOrFail($id);

        $categories = $order->categories->pluck('id')->all();

        $newOrder = $order->replicate();

        if ($newOrder->unique_id) {
            $newOrder->unique_id = $newOrder->unique_id . '-' . Str::random(5);
        }

        $newOrder->views = 0;
        $newOrder->created_at = Carbon::now();
        $newOrder->updated_at = Carbon::now();


        $newOrder->save();

        $newOrder->categories()->sync($categories);


        return $response
            ->setData(['url' => route('order.edit', $newOrder->getKey())])
            ->setMessage(trans('plugins/real-estate::order.duplicate_property_successfully'));
    }
}
