<?php

namespace App\Observers;

use App\Models\RequirementProposal;
use App\Notifications\Requirement\RequirementProposalReceived;
use App\Notifications\Requirement\RequirementProposalStatusChanged;
use App\Services\NotificationService;

class RequirementProposalObserver
{
    public function __construct(private NotificationService $notifier) {}

    /**
     * When a provider submits a proposal, notify the customer.
     */
    public function created(RequirementProposal $proposal): void
    {
        $customer = $proposal->requirement?->customer;
        if (!$customer) return;

        $this->notifier->send($customer, new RequirementProposalReceived($proposal));
    }

    /**
     * When a proposal is accepted or rejected, notify the provider.
     */
    public function updated(RequirementProposal $proposal): void
    {
        if (!$proposal->isDirty('status')) return;

        $newStatus = $proposal->status;

        if (!in_array($newStatus, ['accepted', 'rejected'])) return;

        $provider = $proposal->provider;
        if (!$provider) return;

        $this->notifier->send($provider, new RequirementProposalStatusChanged($proposal, $newStatus));
    }
}
