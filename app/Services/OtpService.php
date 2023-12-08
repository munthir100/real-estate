<?php

namespace App\Services;

use Botble\RealEstate\Notifications\ConfirmEmailNotification;
use Illuminate\Support\Facades\Session;

class OtpService
{
    function generateOtp()
    {
        return  rand(100000, 999999);
    }

    function sendOtpToEmail($account, $otp)
    {
        $account->notify(new ConfirmEmailNotification($otp));
        Session::put(['otp' => $otp, 'email' => $account->email]);
    }

    function sendOtpToPhone($account, $otp)
    {
        Session::put(['otp' => 1234, 'phone' => $account->phone]);
    }
}
