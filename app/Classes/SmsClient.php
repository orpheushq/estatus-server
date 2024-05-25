<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsClient
{
    private string $apiEndpoint = "https://app.notify.lk/api/v1/send";

    public function __construct()
    {
        $this->apiEndpoint .= "?user_id=" . env('NOTIFYLK_USERID') . "&api_key=" . env('NOTIFYLK_APIKEY')
            . "&sender_id=" . env("NOTIFYLK_SENDERID");
    }

    public function sendSms($phoneNumber, $message): void
    {
        $encodedMessage = urlencode($message);
        $transformedPhoneNumber = (strlen($phoneNumber) < 11 ? "94" : "") . $phoneNumber;

        $response = Http::get("{$this->apiEndpoint}&to=${transformedPhoneNumber}&message={$encodedMessage}");
        Log::channel("sms")->info("Sent an SMS to ${transformedPhoneNumber}. Response: {$response->body()}");
    }
}
