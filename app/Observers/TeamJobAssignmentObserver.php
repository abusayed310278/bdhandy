<?php

namespace App\Observers;

use App\Models\TeamJobAssignment;
use App\Notifications\TeamJob\TeamJobAssigned;
use App\Notifications\TeamJob\TeamJobCompleted;
use App\Notifications\TeamJob\TeamJobStatusChanged;
use App\Services\NotificationService;

class TeamJobAssignmentObserver
{
    public function __construct(private NotificationService $notifier) {}

    /**
     * When a job is assigned, notify the team member.
     */
    public function created(TeamJobAssignment $assignment): void
    {
        $member = $assignment->member?->user;
        if (!$member) return;

        $this->notifier->send($member, new TeamJobAssigned($assignment));
    }

    public function updated(TeamJobAssignment $assignment): void
    {
        if (!$assignment->isDirty('status')) return;

        $old = $assignment->getOriginal('status');
        $new = $assignment->status;

        // Completed — notify the business/provider
        if ($new === 'completed') {
            $provider = $assignment->business?->user;
            if ($provider) {
                $this->notifier->send($provider, new TeamJobCompleted($assignment));
            }
            return;
        }

        // Other status changes — notify team member and provider
        $member = $assignment->member?->user;
        if ($member) {
            $this->notifier->send($member, new TeamJobStatusChanged($assignment, $old, $new));
        }

        $provider = $assignment->business?->user;
        if ($provider && $provider->id !== ($member?->id ?? 0)) {
            $this->notifier->send($provider, new TeamJobStatusChanged($assignment, $old, $new));
        }
    }
}
