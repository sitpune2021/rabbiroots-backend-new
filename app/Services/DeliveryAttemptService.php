<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Order;

class DeliveryAttemptService
{
    public function sendSmsFallback(Order $order)
    {
        $message = "Delivery agent trying to reach you. Please contact immediately.";

        $numbers = [$order->primary_phone];

        if ($order->secondary_phone) {
            $numbers[] = $order->secondary_phone;
        }

        foreach ($numbers as $number) {

            // Here you can integrate Twilio (you already use OTP 👍)
            Log::info("SMS sent to ".$number);

            /*
            app(\App\Services\TwilioService::class)->sendSms($number, $message);
            */
        }
    }
}
