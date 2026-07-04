<?php

namespace App\Notifications\Requirement;

use App\Models\RequirementProposal;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class RequirementProposalStatusChanged extends BaseNotification
{
    public function __construct(
        public RequirementProposal $proposal,
        public string $newStatus,  // 'accepted' | 'rejected'
    ) {}

    public function getEventType(): string
    {
        return 'requirement_proposal.status_changed';
    }

    public function toDatabase(mixed $notifiable): array
    {
        $isAccepted = $this->newStatus === 'accepted';

        return [
            'type'  => 'requirement_proposal.status_changed',
            'title' => $isAccepted ? 'Proposal Accepted! 🎉' : 'Proposal Not Selected',
            'body'  => $isAccepted
                ? "Your proposal for \"{$this->proposal->requirement?->title}\" was accepted. Get ready for work!"
                : "Your proposal for \"{$this->proposal->requirement?->title}\" was not selected this time.",
            'url'   => route('provider.leads.show', $this->proposal->requirement_id),
            'meta'  => [
                'proposal_id'    => $this->proposal->id,
                'requirement_id' => $this->proposal->requirement_id,
                'status'         => $this->newStatus,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $isAccepted = $this->newStatus === 'accepted';

        return (new MailMessage)
            ->subject(($isAccepted ? 'Proposal Accepted' : 'Proposal Update') . ' — ' . config('app.name'))
            ->view('emails.notifications.requirement', [
                'user'        => $notifiable,
                'requirement' => $this->proposal->requirement,
                'heading'     => $isAccepted ? 'Your Proposal Was Accepted!' : 'Proposal Not Selected',
                'message'     => $isAccepted
                    ? "Great news! Your proposal for \"{$this->proposal->requirement?->title}\" has been accepted."
                    : "Thank you for your proposal on \"{$this->proposal->requirement?->title}\". Unfortunately it was not selected.",
                'actionUrl'   => route('provider.leads.show', $this->proposal->requirement_id),
                'actionText'  => 'View Details',
            ]);
    }
}
