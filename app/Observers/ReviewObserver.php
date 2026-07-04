<?php

namespace App\Observers;

use App\Models\Review;
use App\Notifications\Review\ReviewReceived;
use App\Services\NotificationService;

class ReviewObserver
{
    public function __construct(private NotificationService $notifier) {}

    public function created(Review $review): void
    {
        $provider = $review->provider;
        if (!$provider) return;

        $this->notifier->send($provider, new ReviewReceived($review));
    }
}
