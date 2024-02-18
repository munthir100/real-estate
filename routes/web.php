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

Route::view('/test', 'test');
Route::post('/dd', function () {
    return response()->json('hi i am work');
});

Route::get('payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
