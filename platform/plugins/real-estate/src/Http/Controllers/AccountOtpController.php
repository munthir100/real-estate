<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\RealEstate\Models\Account;
use Illuminate\Http\Request;

class AccountOtpController
{
    public function showOtpVerificationForm()
    {
        if (!session()->exists(['email', 'otp'])) {
            return redirect()->route('public.account.login');
        }

        return view('plugins/real-estate::account.auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string'
        ]);
        $email = session()->get('email');
        $otp = session()->get('otp');

        if ($otp == $request->otp) {
            $account = Account::where('email', $email)->first();
            $account->verified_at = now();
            $this->guard()->login($account);
            // forget session email and otp
            $this->saveIpAddress($account, $request->ip());
            session()->forget(['email', 'otp']);
            return to_route('public.account.dashboard')
                ->with('success', __('Registered successfully!'));
        } else {
            return back()->with('error_msg', 'invaild otp');
        }
    }

    protected function guard()
    {
        return auth('account');
    }

    protected function saveIpAddress($account, $ip)
    {
        $account->ipAddress()->create([
            'ip' => $ip
        ]);
    }
}
