<?php

namespace App\Notifications\SupportTicket;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class SupportTicketReplied extends BaseNotification
{
    public function __construct(
        public SupportTicket $ticket,
        public SupportTicketMessage $ticketMessage,
    ) {}

    public function getEventType(): string
    {
        return 'support_ticket.replied';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'support_ticket.replied',
            'title' => 'New Reply on Your Ticket',
            'body'  => "{$this->ticketMessage->user?->name} replied to ticket #{$this->ticket->ticket_number}.",
            'url'   => $this->resolveUrl($notifiable),
            'meta'  => [
                'ticket_id'  => $this->ticket->id,
                'message_id' => $this->ticketMessage->id,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Reply on Ticket #{$this->ticket->ticket_number} — " . config('app.name'))
            ->view('emails.notifications.support-ticket', [
                'user'       => $notifiable,
                'ticket'     => $this->ticket,
                'heading'    => 'New Reply on Your Ticket',
                'message'    => "{$this->ticketMessage->user?->name} replied to ticket #{$this->ticket->ticket_number}.",
                'actionUrl'  => $this->resolveUrl($notifiable),
                'actionText' => 'View Reply',
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
