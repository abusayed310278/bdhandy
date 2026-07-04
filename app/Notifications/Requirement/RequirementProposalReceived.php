<?php

namespace App\Notifications\Requirement;

use App\Models\RequirementProposal;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class RequirementProposalReceived extends BaseNotification
{
    public function __construct(public RequirementProposal $proposal) {}

    public function getEventType(): string
    {
        return 'requirement_proposal.received';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'requirement_proposal.received',
            'title' => 'New Proposal Received',
            'body'  => "{$this->proposal->provider?->name} submitted a proposal for your requirement \"{$this->proposal->requirement?->title}\".",
            'url'   => route('customer.requirements.show', $this->proposal->requirement_id),
            'meta'  => [
                'proposal_id'    => $this->proposal->id,
                'requirement_id' => $this->proposal->requirement_id,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Proposal on Your Requirement — " . config('app.name'))
            ->view('emails.notifications.requirement', [
                'user'        => $notifiable,
                'requirement' => $this->proposal->requirement,
                'heading'     => 'You Have a New Proposal',
                'message'     => "{$this->proposal->provider?->name} submitted a proposal at {$this->proposal->proposed_price}. Review it now.",
                'actionUrl'   => route('customer.requirements.show', $this->proposal->requirement_id),
                'actionText'  => 'Review Proposal',
            ]);
    }
}
