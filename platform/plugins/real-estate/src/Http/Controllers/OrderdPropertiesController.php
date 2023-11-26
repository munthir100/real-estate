<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\OrderForm;
use Botble\RealEstate\Http\Requests\OrderRequest;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Order;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Services\StoreOrderCategoryService;
use Botble\RealEstate\Services\StorePropertyCategoryService;
use Botble\RealEstate\Tables\OrderTable;
use Exception;
use Illuminate\Http\Request;

class OrderdPropertiesController extends BaseController
{
    public function index(OrderTable $dataTable)
    {
        PageTitle::setTitle(trans('plugins/real-estate::order.name'));

        return $dataTable->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/real-estate::order.create'));

        return $formBuilder->create(OrderForm::class)->renderForm();
    }

    public function store(
        OrderRequest $request,
        BaseHttpResponse $response,
        StoreOrderCategoryService $orderCategoryService,
    ) {
        $request->merge([
            'author_type' => Account::class,
        ]);

        $order = new Order();
        $order = $order->fill($request->validated());
        $order->moderation_status = $request->input('moderation_status');
        $order->save();

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $order));
        $orderCategoryService->execute($request, $order);



        return $response
            ->setPreviousUrl(route('order.index'))
            ->setNextUrl(route('order.edit', $order->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, Request $request, FormBuilder $formBuilder)
    {
        $order = Order::query()->with('author')->findOrFail($id);

        Assets::addScriptsDirectly(['vendor/core/plugins/real-estate/js/duplicate-order.js']);

        PageTitle::setTitle(trans('plugins/real-estate::order.edit') . ' "' . $order->name . '"');

        event(new BeforeEditContentEvent($request, $order));

        return $formBuilder->create(OrderForm::class, ['model' => $order])->renderForm();
    }

    public function update(
        int|string $id,
        OrderRequest $request,
        BaseHttpResponse $response,
        StoreOrderCategoryService $orderCategoryService,
    ) {
        $order = Order::query()->findOrFail($id);
        $order->fill($request->validated());

        $order->author_type = Account::class;
        $order->moderation_status = $request->input('moderation_status');

        $order->save();

        event(new UpdatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $order));


        $orderCategoryService->execute($request, $order);

        return $response
            ->setPreviousUrl(route('order.index'))
            ->setNextUrl(route('order.edit', $order->id))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $order = Order::query()->findOrFail($id);
            $order->delete();

            event(new DeletedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $order));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
