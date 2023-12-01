<?php

namespace Botble\RealEstate\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Routing\Controller;
use Botble\Base\Facades\EmailHandler;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Forms\AccountOrderForm;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\RealEstate\Models\CustomFieldValue;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Forms\AccountPropertyForm;
use Botble\RealEstate\Models\AccountActivityLog;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Http\Requests\AccountOrderRequest;
use Botble\RealEstate\Tables\AccountPropertyTable;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Http\Requests\AccountPropertyRequest;
use Botble\RealEstate\Models\Order;
use Botble\RealEstate\Services\StorePropertyCategoryService;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Botble\RealEstate\Services\StoreOrderCategoryService;
use Botble\RealEstate\Tables\AccountOrderTable;

class AccountOrderController extends Controller
{
    public function __construct(
        protected AccountInterface $accountRepository,
        protected PropertyInterface $propertyRepository,
        protected AccountActivityLogInterface $activityLogRepository
    ) {
        OptimizerHelper::disable();
        $this->middleware('completed_account');
    }

    public function index(AccountOrderTable $orderTable)
    {
        SeoHelper::setTitle(trans('plugins/real-estate::order.name'));

        return $orderTable->render('plugins/real-estate::account.table.base');
    }

    public function create(FormBuilder $formBuilder)
    {
        SeoHelper::setTitle(trans('plugins/real-estate::order.create'));

        return $formBuilder->create(AccountOrderForm::class)->renderForm();
    }

    public function store(
        AccountOrderRequest $request,
        BaseHttpResponse $response,
        AccountInterface $accountRepository,
        StoreOrderCategoryService $orderCategoryService,
    ) {
        $order = new Order();

        $order->fill(array_merge($this->processRequestData($request), [
            'author_id' => auth('account')->id(),
            'author_type' => Account::class,
        ]));

        if (setting('enable_post_approval', 1) == 0) {
            $order->moderation_status = ModerationStatusEnum::APPROVED;
        }

        $order->save();

        $orderCategoryService->execute($request, $order);

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $order));

        AccountActivityLog::query()->create([
            'action' => 'request_property',
            'reference_name' => $order->phone,
            'reference_url' => route('public.account.orders.edit', $order->id),
        ]);

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'post_name' => $order->phone,
                'post_url' => route('order.edit', $order->id),
                'post_author' => $order->author->name,
            ])
            ->sendUsingTemplate('new-pending-property');

        return $response
            ->setPreviousUrl(route('public.account.orders.index'))
            ->setNextUrl(route('public.account.orders.edit', $order->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder, Request $request)
    {
        $order = Order::query()->where('author_id', auth('account')->id())
        ->with('author')
        ->findOrFail($id);

        if (!$order) {
            abort(404);
        }

        event(new BeforeEditContentEvent($request, $order));

        SeoHelper::setTitle(trans('plugins/real-estate::property.edit') . ' "' . $order->phone . '"');

        return $formBuilder
            ->create(AccountOrderForm::class, ['model' => $order])
            ->renderForm();
    }

    public function update(
        int|string $id,
        AccountOrderRequest $request,
        BaseHttpResponse $response,
        StoreOrderCategoryService $orderCategoryService,
    ) {
        $order = Order::query()->where('author_id', auth('account')->id())
            ->with('author')
            ->findOrFail($id);

        if (!$order) {
            abort(404);
        }

        $order->fill($this->processRequestData($request));

        $order->save();

        $orderCategoryService->execute($request, $order);

        event(new UpdatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $request, $order));

        AccountActivityLog::query()->create([
            'action' => 'update_requested_property',
            'reference_name' => $order->phone,
            'reference_url' => route('public.account.orders.edit', $order->id),
        ]);

        return $response
            ->setPreviousUrl(route('public.account.orders.index'))
            ->setNextUrl(route('public.account.orders.edit', $order->id))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    protected function processRequestData(Request $request): array
    {
        $except = [
            'author_id',
            'author_type',
        ];

        foreach ($except as $item) {
            $request->request->remove($item);
        }

        return $request->input();
    }

    public function destroy(int|string $id, BaseHttpResponse $response)
    {
        $order = Order::query()->where('author_id', auth('account')->id())
            ->with('author')
            ->findOrFail($id);

        if (!$order) {
            abort(404);
        }

        $order->delete();

        AccountActivityLog::query()->create([
            'action' => 'delete_order',
            'reference_name' => $order->phone,
        ]);

        return $response->setMessage(__('Delete order successfully!'));
    }
}
