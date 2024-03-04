<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    function test() {
        $url = 'https://test-iamservices.semati.sa/nafath/api/v1/client/authorize/';
        $headers = array(
            'Content-Type: Application/json',
            'Authorization: apikey 21c0f19a-fce5-4d4d-b30f-ffd1c3860731'
        );
    
        $data = array(
            'id' => '2345432677',
            'action' => 'SpRequest',
            'service' => 'DigitalServiceEnrollmentWithoutBio'
        );
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
    
        curl_close($ch);
    
        // Handle $response as needed
        return $response;
    }
    
}
