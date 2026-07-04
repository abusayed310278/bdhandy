<?php

namespace App\Notifications\SupportTicket;

use App\Models\SupportTicket;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class SupportTicketStatusChanged extends BaseNotification
{
    public function __construct(
        public SupportTicket $ticket,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function getEventType(): string
    {
        return 'support_ticket.status_changed';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'support_ticket.status_changed',
            'title' => 'Support Ticket Updated',
            'body'  => "Your ticket #{$this->ticket->ticket_number} status changed to {$this->newStatus}.",
            'url'   => $this->resolveUrl($notifiable),
            'meta'  => [
                'ticket_id'  => $this->ticket->id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket #{$this->ticket->ticket_number} Status Updated — " . config('app.name'))
            ->view('emails.notifications.support-ticket', [
                'user'       => $notifiable,
                'ticket'     => $this->ticket,
                'heading'    => 'Your Ticket Status Has Changed',
                'message'    => "Ticket #{$this->ticket->ticket_number} is now <strong>{$this->newStatus}</strong>.",
                'actionUrl'  => $this->resolveUrl($notifiable),
                'actionText' => 'View Ticket',
            ]);
    }

    private function resolveUrl(mixed $notifiable): string
    {
        if ($notifiable->hasRole(['super_admin', 'admin', 'support', 'moderator'])) {
            return route('admin.tickets.show', $this->ticket);
        }
        if ($notifiable->hasRole(['freelancer', 'business'])) {
            return route('provider.tickets.show', $this->ticket);
        }
        return route('customer.tickets.show', $this->ticket);
    }
}
