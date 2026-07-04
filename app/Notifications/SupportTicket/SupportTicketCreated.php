<?php

namespace App\Notifications\SupportTicket;

use App\Models\SupportTicket;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class SupportTicketCreated extends BaseNotification
{
    public function __construct(public SupportTicket $ticket) {}

    public function getEventType(): string
    {
        return 'support_ticket.created';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'support_ticket.created',
            'title' => 'New Support Ticket',
            'body'  => "Ticket #{$this->ticket->ticket_number}: \"{$this->ticket->subject}\" — Priority: {$this->ticket->priority}.",
            'url'   => route('admin.tickets.show', $this->ticket),
            'meta'  => [
                'ticket_id'     => $this->ticket->id,
                'ticket_number' => $this->ticket->ticket_number,
                'priority'      => $this->ticket->priority,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Support Ticket #{$this->ticket->ticket_number} — " . config('app.name'))
            ->view('emails.notifications.support-ticket', [
                'user'       => $notifiable,
                'ticket'     => $this->ticket,
                'heading'    => 'New Support Ticket Submitted',
                'message'    => "A new support ticket has been submitted by {$this->ticket->user?->name}. Subject: \"{$this->ticket->subject}\". Priority: {$this->ticket->priority}.",
                'actionUrl'  => route('admin.tickets.show', $this->ticket),
                'actionText' => 'View Ticket',
            ]);
    }
}
