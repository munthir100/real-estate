<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\custom\payments\PaymentController;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {

    $url = 'https://test-iamservices.semati.sa/nafath/api/v1/client/authorize/';
    $apiKey = '21c0f19a-fce5-4d4d-b30f-ffd1c3860731';

    $data = [
        'id' => '2345675322',
        'action' => 'SpRequest',
        'service' => 'DigitalServiceEnrollmentWithoutBio'
    ];

    $headers = [
        'Content-Type: Application/json',
        'Authorization: apikey ' . $apiKey
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode === 200) {
        // Request was successful
        echo "Response: $response";
    } else {
        // Request failed
        dd("Error: HTTP code $httpCode, Response: $response");
    }

    curl_close($ch);
});

Route::get('payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
