<?php

namespace Botble\RealEstate\Services\Abstracts;

use Botble\RealEstate\Models\Order;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Illuminate\Http\Request;

abstract class StoreOrderCategoryServiceAbstract
{
    public function __construct(protected CategoryInterface $categoryRepository)
    {
    }

    abstract public function execute(Request $request, Order $order);
}
