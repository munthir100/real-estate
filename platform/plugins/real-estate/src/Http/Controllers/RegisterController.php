<?php

namespace Botble\RealEstate\Http\Controllers;

use Carbon\Carbon;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Botble\Theme\Facades\Theme;
use Botble\Base\Facades\BaseHelper;
use Botble\Captcha\Facades\Captcha;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Facades\EmailHandler;
use Botble\RealEstate\Models\Account;
use Illuminate\Auth\Events\Registered;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\RealEstate\Models\AccountType;
use Illuminate\Support\Facades\Validator;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Http\Requests\RegisterRequest;
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

        $this->sendConfirmationToUser($account, 1234); // temp

        return $response
            ->setMessage(__('We sent you another confirmation email. You should receive it shortly.'));
    }



    public function register(RegisterRequest $request, BaseHttpResponse $response, OtpService $otpService)
    {
        if (!RealEstateHelper::isRegisterEnabled()) {
            abort(404);
        }
        $data = $request->validated();
        $request->validateEmailAndPhone($data);
        $data['password'] = Hash::make($data['password']);
        $account = Account::create($data);
        $this->createAccountType($account);
        event(new Registered($account));
        Auth::guard('account')->loginUsingId($account->id);
        return to_route('public.account.dashboard');

        // $otp = $otpService->generateOtp();
        // if ($data['email']) {
        //     $otpService->sendOtpToEmail($account, $otp);
        // }else{
        //     $otpService->sendOtpToPhone($account, $otp);
        // }

        // return to_route('public.account.otp.form');
    }

    function createAccountType($account)
    {
        if ($account->is_developer_account) {
            $account->broker()->create(['is_developer' => true]);
        } elseif ($account->is_broker_account) {
            $account->broker()->create();
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
