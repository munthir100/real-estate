<?php

namespace App\Http\Controllers\custom\payments;

use Illuminate\Http\Request;
use Botble\Payment\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Botble\Base\Facades\EmailHandler;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\PayPal\Services\Gateways\PayPalPaymentService;
use Botble\RealEstate\Models\Account;

class PaymentController extends Controller
{
    // create callback method
    public function callback(Request $request, BaseHttpResponse $response)
    {
        $apiKey = 'sk_test_osN7g4jFCxPguJbetRhQnCnDTZQiDmdMgJ4xS8wL';
        $id = $request->get('id'); // You can directly use $request to get parameters

        $callbackResponse = Http::withBasicAuth($apiKey, '')
            ->get("https://api.moyasar.com/v1/payments/$id");
        $statusCode = $callbackResponse->status(); // Get the status code
        if ($statusCode != 200) {
            abort(404);
        }

        $paymentResponse = $callbackResponse->json();
        $payment = Payment::where('charge_id', $paymentResponse['id'])->first();
        
        $package = Package::query()->findOrFail($request->packageId);
        $account = auth('account')->user();
        $payment = Payment::create([
            'amount' => $paymentResponse['amount'],
            'currency' => $paymentResponse['currency'],
            'user_id',
            'charge_id' => $paymentResponse['id'],
            'payment_channel' => $paymentResponse['source']['type'],
            'description' => $paymentResponse['description'],
            'order_id' => $package->id,
            'customer_id' => $account->id,
            'customer_type' => 'Botble\RealEstate\Models\Account',
        ]);


        $this->savePayment($package, $account, $payment->id);

        if (!$request->has('success') || $request->input('success')) {
            return $response
                ->setNextUrl(route('public.account.packages'))
                ->setMessage(session()->get('success_msg') ?: trans('plugins/real-estate::package.add_credit_success'));
        }

        return $response
            ->setError()
            ->setNextUrl(route('public.account.packages'))
            ->setMessage(__('Payment failed!'));
    }




    protected function savePayment(Package $package, $account, int $paymentId)
    {
        if (!RealEstateHelper::isEnabledCreditsSystem()) {
            abort(404);
        }


        Transaction::query()->create([
            'user_id' => 0,
            'account_id' => $account->id,
            'credits' => $package->number_of_listings,
            'payment_id' => $paymentId,
        ]);

        if (!$package->price) {
            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'account_name' => $account->name,
                    'account_email' => $account->email,
                ])
                ->sendUsingTemplate('free-credit-claimed');
        } else {
            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'account_name' => $account->name,
                    'account_email' => $account->email,
                    'package_name' => $package->name,
                    'package_price' => $package->price,
                    'package_percent_discount' => $package->percent_save,
                    'package_number_of_listings' => $package->number_of_listings,
                    'package_price_per_credit' => $package->price ? $package->price / ($package->number_of_listings ?: 1) : 0,
                ])
                ->sendUsingTemplate('payment-received');
        }

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'account_name' => $account->name,
                'account_email' => $account->email,
                'package_name' => $package->name,
                'package_price' => $package->price ?: 0,
                'package_percent_discount' => $package->percent_save,
                'package_number_of_listings' => $package->number_of_listings,
                'package_price_per_credit' => $package->price ? $package->price / ($package->number_of_listings ?: 1) : 0,
            ])
            ->sendUsingTemplate('payment-receipt', auth('account')->user()->email);

        return true;
    }
}
