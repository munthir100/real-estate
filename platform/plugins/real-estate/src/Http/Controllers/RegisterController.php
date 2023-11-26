<?php

namespace Botble\RealEstate\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Botble\Theme\Facades\Theme;
use Botble\Base\Facades\BaseHelper;
use Botble\Captcha\Facades\Captcha;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Facades\EmailHandler;
use Botble\RealEstate\Models\Account;
use Illuminate\Auth\Events\Registered;
use Botble\SeoHelper\Facades\SeoHelper;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\auth\RegisterRequest;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Models\AccountType;
use Botble\RealEstate\Notifications\ConfirmEmailNotification;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected string $redirectTo = '/';

    public function __construct()
    {
        $this->redirectTo = route('public.account.dashboard');
    }

    public function showRegistrationForm()
    {
        if (!RealEstateHelper::isRegisterEnabled()) {
            abort(404);
        }

        SeoHelper::setTitle(__('Register'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.account.auth.register')) {
            return Theme::scope('real-estate.account.auth.register')->render();
        }

        return view('plugins/real-estate::account.auth.register');
    }

    public function confirm(int|string $id, Request $request, BaseHttpResponse $response)
    {
        if (!RealEstateHelper::isRegisterEnabled()) {
            abort(404);
        }

        if (!URL::hasValidSignature($request)) {
            abort(404);
        }

        $account = Account::query()->findOrFail($id);

        $account->confirmed_at = Carbon::now();
        $account->save();

        $this->guard()->login($account);

        return $response
            ->setNextUrl(route('public.account.dashboard'))
            ->setMessage(__('You successfully confirmed your email address.'));
    }

    protected function guard()
    {
        return auth('account');
    }

    public function resendConfirmation(Request $request, BaseHttpResponse $response)
    {
        if (!RealEstateHelper::isRegisterEnabled()) {
            abort(404);
        }

        $account = Account::query()->where('email', $request->input('email'))->first();

        if (!$account) {
            return $response
                ->setError()
                ->setMessage(__('Cannot find this account!'));
        }

        $this->sendConfirmationToUser($account);

        return $response
            ->setMessage(__('We sent you another confirmation email. You should receive it shortly.'));
    }

    protected function sendConfirmationToUser(Account $account)
    {
        $account->notify(app(ConfirmEmailNotification::class));
    }

    public function register(RegisterRequest $request, BaseHttpResponse $response)
    {
        if (!RealEstateHelper::isRegisterEnabled()) {
            abort(404);
        }
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $account = Account::create($data);
        $this->createAccountType($account);
        event(new Registered($account));
        $account->verified_at = now(); // temp
        $this->guard()->login($account);


        return to_route('public.account.settings')
            ->with('success', __('Registered successfully!'));
    }

    function createAccountType($account)
    {
        if ($account->is_developer_account) {
            $account->broker()->create(['is_developer' => true]);
        } elseif ($account->is_broker_account) {
            $account->broker()->create();
        } else {
            $account->seeker()->create();
        }
    }


    public function getVerify()
    {
        if (!RealEstateHelper::isRegisterEnabled()) {
            abort(404);
        }

        return view('plugins/real-estate::account.auth.verify');
    }
}
