<?php

namespace Botble\RealEstate\Services;

use Illuminate\Http\Request;
use Botble\RealEstate\Models\Order;
use Botble\RealEstate\Services\Abstracts\StoreOrderCategoryServiceAbstract;

class StoreOrderCategoryService extends StoreOrderCategoryServiceAbstract
{
    public function execute(Request $request, Order $order): void
    {
        $categories = $request->input('categories', []);
        if (is_array($categories)) {
            if ($categories) {
                $order->categories()->sync($categories);
            } else {
                $order->categories()->detach();
            }
        }
    }
}
