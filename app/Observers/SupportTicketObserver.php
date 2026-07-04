<?php

namespace App\Observers;

use App\Models\SupportTicket;
use App\Notifications\SupportTicket\SupportTicketAssigned;
use App\Notifications\SupportTicket\SupportTicketCreated;
use App\Notifications\SupportTicket\SupportTicketStatusChanged;
use App\Services\NotificationService;

class SupportTicketObserver
{
    public function __construct(private NotificationService $notifier) {}

    /**
     * When a new ticket is created, notify all admin/support users.
     */
    public function created(SupportTicket $ticket): void
    {
        $this->notifier->notifyAdmins(new SupportTicketCreated($ticket));
    }

    public function updated(SupportTicket $ticket): void
    {
        // Ticket assigned to someone
        if ($ticket->isDirty('assigned_to') && $ticket->assigned_to) {
            $assignee = $ticket->assignedTo;
            if ($assignee) {
                $this->notifier->send($assignee, new SupportTicketAssigned($ticket));
            }
        }

        // Status changed — notify the ticket owner
        if ($ticket->isDirty('status')) {
            $owner = $ticket->user;
            if ($owner) {
                $this->notifier->send(
                    $owner,
                    new SupportTicketStatusChanged($ticket, $ticket->getOriginal('status'), $ticket->status)
                );
            }
        }
    }
}
