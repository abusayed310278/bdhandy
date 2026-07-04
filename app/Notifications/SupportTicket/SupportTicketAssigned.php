<?php

namespace App\Notifications\SupportTicket;

use App\Models\SupportTicket;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class SupportTicketAssigned extends BaseNotification
{
    public function __construct(public SupportTicket $ticket) {}

    public function getEventType(): string
    {
        return 'support_ticket.assigned';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'support_ticket.assigned',
            'title' => 'Support Ticket Assigned to You',
            'body'  => "Ticket #{$this->ticket->ticket_number}: \"{$this->ticket->subject}\" has been assigned to you.",
            'url'   => route('admin.tickets.show', $this->ticket),
            'meta'  => [
                'ticket_id'     => $this->ticket->id,
                'ticket_number' => $this->ticket->ticket_number,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket #{$this->ticket->ticket_number} Assigned to You — " . config('app.name'))
            ->view('emails.notifications.support-ticket', [
                'user'       => $notifiable,
                'ticket'     => $this->ticket,
                'heading'    => 'Ticket Assigned to You',
                'message'    => "Support ticket #{$this->ticket->ticket_number}: \"{$this->ticket->subject}\" has been assigned to you. Please review it.",
                'actionUrl'  => route('admin.tickets.show', $this->ticket),
                'actionText' => 'View Ticket',
            ]);
    }
}
