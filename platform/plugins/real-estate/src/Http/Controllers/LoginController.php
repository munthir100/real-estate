<?php

namespace Botble\RealEstate\Http\Controllers;

use Illuminate\Http\Request;
use Botble\Theme\Facades\Theme;
use App\Http\Controllers\Controller;
use App\Models\AccountIp;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Botble\RealEstate\Models\Account;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\ACL\Traits\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Botble\RealEstate\Facades\RealEstateHelper;

class LoginController extends Controller
{
    use AuthenticatesUsers, LogoutGuardTrait {
        AuthenticatesUsers::attemptLogin as baseAttemptLogin;
    }

    public string $redirectTo = '/';

    public function __construct()
    {
        $this->redirectTo = route('public.account.dashboard');
    }

    public function showLoginForm()
    {
        if (!RealEstateHelper::isLoginEnabled()) {
            abort(404);
        }

        SeoHelper::setTitle(trans('plugins/real-estate::account.login'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.account.auth.login')) {
            return Theme::scope('real-estate.account.auth.login')->render();
        }

        return view('plugins/real-estate::account.auth.login');
    }

    protected function guard()
    {
        return auth('account');
    }

    public function login(Request $request, OtpService $otpService)
    {
        if (!RealEstateHelper::isLoginEnabled()) {
            abort(404);
        }

        $request->validate([
            'email' => 'required|string'
        ]);
        $email = $request->input('email');
        $account = Account::where('email', $email)
            ->orWhere('phone', $email)->first();

        if ($account && Hash::check(request()->input('password'), $account->password)) {
            Auth::guard('account')->loginUsingId($account->id);
            return to_route('public.account.dashboard');
        } else {
            return $this->sendFailedLoginResponse();
        }
        // if ($account && Hash::check(request()->input('password'), $account->password)) {
        //     $savedIp = $this->checkIfSavedIp($account, $request->ip());
        //     if ($savedIp) {
        //         Auth::guard('account')->loginUsingId($account->id);

        //         return to_route('public.account.dashboard');
        //     } else {
        //         $otp = $otpService->generateOtp();
        //         if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //             $otpService->sendOtpToEmail($account, $otp);
        //         } else {
        //             $otpService->sendOtpToPhone($account, $otp);
        //         }

        //         return to_route('public.account.otp.form');
        //     }
        // }

        // return $this->sendFailedLoginResponse();
    }

    protected function checkIfSavedIp($account, $ip)
    {
        return AccountIp::where('account_id', $account->id)->where('ip', $ip)->first();
    }


    public function logout(Request $request)
    {
        if (!RealEstateHelper::isLoginEnabled()) {
            abort(404);
        }

        $activeGuards = 0;
        $this->guard()->logout();

        foreach (config('auth.guards', []) as $guard => $guardConfig) {
            if ($guardConfig['driver'] !== 'session') {
                continue;
            }
            if ($this->isActiveGuard($request, $guard)) {
                $activeGuards++;
            }
        }

        if (!$activeGuards) {
            $request->session()->flush();
            $request->session()->regenerate();
        }

        $this->loggedOut($request);

        return redirect(route('public.index'));
    }
}
