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
use App\Http\Controllers\TestController;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Route;

Route::view('/test', 'test');
Route::post('/test', [TestController::class, 'test']);
Route::get('payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
