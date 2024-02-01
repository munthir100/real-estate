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
    $headers = array(
        'Content-Type: Application/json',
        'Authorization: apikey 21c0f19a-fce5-4d4d-b30f-ffd1c3860731'
    );
    
    $data = array(
        'id' => '2345432345',
        'action' => 'SpRequest',
        'service' => 'DigitalServiceEnrollmentWithoutBio'
    );
    
    $options = array(
        'http' => array(
            'header'  => implode("\r\n", $headers),
            'method'  => 'POST',
            'content' => json_encode($data),
        ),
    );
    
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    // Output the response
    echo $response;
    

});

Route::get('payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
