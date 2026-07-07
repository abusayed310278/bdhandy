<?php

namespace App\Observers;

use App\Models\Subscription;
use App\Notifications\Payment\SubscriptionRenewalUpcoming;
use App\Services\NotificationService;

class SubscriptionObserver
{
    public function __construct(private NotificationService $notifier) {}

    public function updated(Subscription $subscription): void
    {
        $provider = $subscription->provider;
        if (!$provider) return;

        if ($subscription->isDirty('notified_3_day_at') && $subscription->notified_3_day_at) {
            $this->notifier->send($provider, new SubscriptionRenewalUpcoming($subscription, '3 days'));
        }

        if ($subscription->isDirty('notified_6_hour_at') && $subscription->notified_6_hour_at) {
            $this->notifier->send($provider, new SubscriptionRenewalUpcoming($subscription, '6 hours'));
        }
    }
}
