<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalChannel
{
    /**
     * Send a push notification via OneSignal REST API.
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        $playerId = $notifiable->onesignal_player_id ?? null;

        if (!$playerId) {
            return; // User has no registered device — skip silently
        }

        if (!method_exists($notification, 'toOneSignal')) {
            return;
        }

        $appId      = config('notifications.onesignal.app_id');
        $restApiKey = config('notifications.onesignal.rest_api_key');

        if (!$appId || !$restApiKey) {
            Log::warning('OneSignalChannel: ONESIGNAL_APP_ID or ONESIGNAL_REST_API_KEY not configured.');
            return;
        }

        $payload = $notification->toOneSignal($notifiable);

        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $restApiKey,
            'Content-Type'  => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', array_merge($payload, [
            'app_id'             => $appId,
            'include_player_ids' => [$playerId],
        ]));

        if (!$response->successful()) {
            Log::error('OneSignalChannel: Failed to send push.', [
                'player_id' => $playerId,
                'status'    => $response->status(),
                'body'      => $response->body(),
            ]);
        }
    }
}
