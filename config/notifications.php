<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OneSignal Push Notifications
    |--------------------------------------------------------------------------
    | SMS credentials live in config/services.php under 'twilio'.
    */

    'onesignal' => [
        'app_id'       => env('ONESIGNAL_APP_ID'),
        'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-Event Channel Defaults
    |--------------------------------------------------------------------------
    | These are the system-wide defaults for each notification event type.
    | Users can override these in their NotificationPreference.event_preferences.
    |
    | 'database' is always ON and cannot be disabled.
    | Keys: 'email', 'sms', 'push'
    */

    'defaults' => [
        // Service Requests — high priority, SMS on
        'service_request.submitted'           => ['email' => true,  'sms' => true,  'push' => true],
        'service_request.status_changed'      => ['email' => true,  'sms' => true,  'push' => true],
        'service_request.completed'           => ['email' => true,  'sms' => true,  'push' => true],
        'service_request.cancelled'           => ['email' => true,  'sms' => true,  'push' => true],

        // Customer Requirements — high priority, SMS on
        'customer_requirement.posted'         => ['email' => true,  'sms' => true,  'push' => true],

        // Requirement Proposals — informational, no SMS
        'requirement_proposal.received'       => ['email' => true,  'sms' => false, 'push' => true],
        'requirement_proposal.status_changed' => ['email' => true,  'sms' => false, 'push' => true],

        // Payments — critical, SMS on
        'payment.successful'                  => ['email' => true,  'sms' => true,  'push' => true],
        'payment.failed'                      => ['email' => true,  'sms' => true,  'push' => true],
        'service_request.invoice_paid'        => ['email' => true,  'sms' => true,  'push' => true],
        'subscription.invoice_paid'           => ['email' => true,  'sms' => true,  'push' => true],
        'subscription.renewal_upcoming'       => ['email' => true,  'sms' => true,  'push' => true],

        // Reviews — informational, no SMS
        'review.received'                     => ['email' => true,  'sms' => false, 'push' => true],

        // Support Tickets — admin-facing, no push for creation
        'support_ticket.created'              => ['email' => true,  'sms' => false, 'push' => false],
        'support_ticket.assigned'             => ['email' => true,  'sms' => false, 'push' => true],
        'support_ticket.status_changed'       => ['email' => true,  'sms' => false, 'push' => true],
        'support_ticket.replied'              => ['email' => true,  'sms' => false, 'push' => true],

        // Team Jobs — field workers, SMS on for assignments
        'team_job.assigned'                   => ['email' => true,  'sms' => true,  'push' => true],
        'team_job.status_changed'             => ['email' => true,  'sms' => false, 'push' => true],
        'team_job.completed'                  => ['email' => true,  'sms' => true,  'push' => true],
    ],

];
