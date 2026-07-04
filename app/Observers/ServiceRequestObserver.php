<?php

namespace App\Observers;

use App\Models\ServiceRequest;
use App\Notifications\ServiceRequest\ServiceRequestCreated;
use App\Notifications\ServiceRequest\ServiceRequestStatusChanged;
use App\Notifications\ServiceRequest\ServiceRequestCompleted;
use App\Notifications\ServiceRequest\ServiceRequestCancelled;
use App\Services\NotificationService;

class ServiceRequestObserver
{
    public function __construct(private NotificationService $notifier) {}

    /**
     * Notify the provider when a new service request is created.
     */
    public function created(ServiceRequest $serviceRequest): void
    {
        if (!$serviceRequest->provider) return;

        $this->notifier->send(
            $serviceRequest->provider,
            new ServiceRequestCreated($serviceRequest)
        );
    }

    /**
     * Watch for request_status changes and route the right notification.
     */
    public function updated(ServiceRequest $serviceRequest): void
    {
        if (!$serviceRequest->isDirty('request_status')) return;

        $old = $serviceRequest->getOriginal('request_status');
        $new = $serviceRequest->request_status;

        // Completed — notify both parties
        if ($new === 'completed') {
            if ($serviceRequest->customer) {
                $this->notifier->send($serviceRequest->customer, new ServiceRequestCompleted($serviceRequest));
            }
            if ($serviceRequest->provider) {
                $this->notifier->send($serviceRequest->provider, new ServiceRequestCompleted($serviceRequest));
            }
            return;
        }

        // Cancelled — notify the other party (not who cancelled)
        if ($new === 'cancelled') {
            $reason = $serviceRequest->cancellation_reason;

            if ($serviceRequest->customer) {
                $this->notifier->send($serviceRequest->customer, new ServiceRequestCancelled($serviceRequest, $reason));
            }
            if ($serviceRequest->provider) {
                $this->notifier->send($serviceRequest->provider, new ServiceRequestCancelled($serviceRequest, $reason));
            }
            return;
        }

        // All other status changes — notify customer primarily, provider if relevant
        if ($serviceRequest->customer) {
            $this->notifier->send(
                $serviceRequest->customer,
                new ServiceRequestStatusChanged($serviceRequest, $old, $new)
            );
        }

        // Also inform provider on status changes they initiate confirmation of
        if (in_array($new, ['confirmed', 'in_progress', 'on_the_way']) && $serviceRequest->provider) {
            $this->notifier->send(
                $serviceRequest->provider,
                new ServiceRequestStatusChanged($serviceRequest, $old, $new)
            );
        }
    }
}
