<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Media\Models\MediaFile;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Forms\AccountForm;
use Botble\RealEstate\Http\Requests\AccountCreateRequest;
use Botble\RealEstate\Http\Requests\AccountEditRequest;
use Botble\RealEstate\Http\Resources\AccountResource;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Tables\AccountTable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends BaseController
{
    public function __construct()
    {
        OptimizerHelper::disable();
    }

    public function index(AccountTable $dataTable)
    {
        PageTitle::setTitle(trans('plugins/real-estate::account.name'));

        return $dataTable->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/real-estate::account.create'));

        return $formBuilder
            ->create(AccountForm::class)
            ->remove('is_change_password')
            ->renderForm();
    }

    public function store(AccountCreateRequest $request, BaseHttpResponse $response)
    {
        $account = new Account();
        $account->fill($request->input());
        $account->is_featured = $request->input('is_featured');
        $account->is_public_profile = $request->input('is_public_profile');
        $account->confirmed_at = Carbon::now();

        $account->password = Hash::make($request->input('password'));
        $account->dob = Carbon::parse($request->input('dob'))->toDateString();

        if ($request->input('avatar_image')) {
            $image = MediaFile::query()
                ->where('url', $request->input('avatar_image'))
                ->first();

            if ($image) {
                $account->avatar_id = $image->id;
            }
        }

        $account->save();

        event(new CreatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

        return $response
            ->setPreviousUrl(route('account.index'))
            ->setNextUrl(route('account.edit', $account->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int|string $id, FormBuilder $formBuilder)
    {
        $account = Account::query()->findOrFail($id);

        PageTitle::setTitle(trans('plugins/real-estate::account.edit', ['name' => $account->name]));

        $account->password = null;

        return $formBuilder
            ->create(AccountForm::class, ['model' => $account])
            ->renderForm();
    }

    public function update(int|string $id, AccountEditRequest $request, BaseHttpResponse $response)
    {
        $account = Account::query()->findOrFail($id);

        $account->fill($request->except('password'));

        if ($request->input('is_change_password') == 1) {
            $account->password = Hash::make($request->input('password'));
        }

        $account->dob = Carbon::parse($request->input('dob'))->toDateString();

        if ($request->input('avatar_image')) {
            $image = app(MediaFileInterface::class)->getFirstBy(['url' => $request->input('avatar_image')]);
            if ($image) {
                $account->avatar_id = $image->id;
            }
        }

        $account->is_featured = $request->input('is_featured');
        $account->is_public_profile = $request->input('is_public_profile');
        $account->save();

        event(new UpdatedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

        return $response
            ->setPreviousUrl(route('account.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $account = Account::query()->findOrFail($id);
            $account->delete();
            event(new DeletedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getList(Request $request, BaseHttpResponse $response)
    {
        $keyword = BaseHelper::stringify($request->input('q'));

        if (! $keyword) {
            return $response->setData([]);
        }

        $data = Account::query()
            ->where('full_name', 'LIKE', '%' . $keyword . '%')
            ->select(['id', 'full_name'])
            ->take(10)
            ->get();

        return $response->setData(AccountResource::collection($data));
    }
}
