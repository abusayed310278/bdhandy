<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

class SmsChannel
{
    /**
     * Send an SMS via the already-configured Twilio SDK.
     * Credentials are read from config/services.php under 'twilio'.
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        // Build the E.164 number from phone_country_code + phone
        $to = trim(($notifiable->phone_country_code ?? '') . ($notifiable->phone ?? ''));

        if (!$to) {
            return; // No phone number — skip silently
        }

        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');

        if (!$sid || !$token || !$from) {
            Log::warning('SmsChannel: Twilio credentials not configured in services.twilio.');
            return;
        }

        try {
            $client = new TwilioClient($sid, $token);
            $client->messages->create($to, [
                'from' => $from,
                'body' => $notification->toSms($notifiable),
            ]);
        } catch (\Throwable $e) {
            Log::error('SmsChannel: Failed to send SMS.', [
                'to'      => $to,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
