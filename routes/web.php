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

Route::get('/send-sms', function () {
    // Access your Twilio credentials from the .env file
    $account_sid = env('TWILIO_ACCOUNT_SID');
    $auth_token = env('TWILIO_AUTH_TOKEN');
    $twilio_phone_number = env('TWILIO_PHONE_NUMBER'); // Your Twilio phone number

    // Create a Twilio client
    $twilio = new Client($account_sid, $auth_token);

    // Recipient's phone number (in E.164 format)
    $recipient_number = '+966544254974'; // Make sure it's in the correct format

    try {
        // Send the message
        $message = $twilio->messages->create(
            $recipient_number,
            [
                'from' => $twilio_phone_number,
                'body' => 'This is a test message from Laravel and Twilio!'
            ]
        );

        // Display success message
        return 'Message sent successfully!';
    } catch (\Twilio\Exceptions\TwilioException $e) {
        // Handle specific Twilio exceptions
        return 'Error sending message: ' . $e->getMessage();
    } catch (\Exception $e) {
        // Handle other exceptions
        return 'Error sending message: ' . $e->getMessage();
    }
});

Route::get('payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
