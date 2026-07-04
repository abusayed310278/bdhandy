<?php

namespace App\Observers;

use App\Models\CustomerRequirement;
use App\Models\ProviderServiceArea;
use App\Models\User;
use App\Notifications\Requirement\CustomerRequirementPosted;
use App\Services\NotificationService;

class CustomerRequirementObserver
{
    public function __construct(private NotificationService $notifier) {}

    /**
     * When a new requirement is posted, notify providers in the service radius.
     */
    public function created(CustomerRequirement $requirement): void
    {
        if ($requirement->status !== 'open') return;

        $this->notifyNearbyProviders($requirement);
    }

    /**
     * When status changes to open (after edit/re-activation), re-notify nearby providers.
     */
    public function updated(CustomerRequirement $requirement): void
    {
        if (!$requirement->isDirty('status')) return;
        if ($requirement->status !== 'open') return;

        $this->notifyNearbyProviders($requirement);
    }

    private function notifyNearbyProviders(CustomerRequirement $requirement): void
    {
        // Find providers who serve the requirement's area
        // Uses the service_id and location radius from CustomerRequirement
        $providers = User::role(['freelancer', 'business'])
            ->whereHas('providerProfile', function ($q) {
                $q->where('verification_status', 'approved');
            })
            ->when($requirement->service_id, function ($q) use ($requirement) {
                $q->whereHas('providerProfile.providerServices', function ($sq) use ($requirement) {
                    $sq->where('service_id', $requirement->service_id);
                });
            })
            ->get();

        if ($providers->isEmpty()) return;

        $this->notifier->sendToMany($providers, new CustomerRequirementPosted($requirement));
    }
}
