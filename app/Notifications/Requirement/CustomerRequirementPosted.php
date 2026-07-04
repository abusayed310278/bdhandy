<?php

namespace App\Notifications\Requirement;

use App\Models\CustomerRequirement;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerRequirementPosted extends BaseNotification
{
    public function __construct(public CustomerRequirement $requirement) {}

    public function getEventType(): string
    {
        return 'customer_requirement.posted';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'customer_requirement.posted',
            'title' => 'New Requirement in Your Area',
            'body'  => "A customer posted a new requirement: \"{$this->requirement->title}\". Submit your proposal now!",
            'url'   => route('provider.leads.show', $this->requirement),
            'meta'  => ['requirement_id' => $this->requirement->id],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Customer Requirement — " . config('app.name'))
            ->view('emails.notifications.requirement', [
                'user'        => $notifiable,
                'requirement' => $this->requirement,
                'heading'     => 'New Requirement Posted',
                'message'     => "A customer in your area posted a new requirement: \"{$this->requirement->title}\". Be the first to submit a proposal!",
                'actionUrl'   => route('provider.leads.show', $this->requirement),
                'actionText'  => 'View Requirement',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        return config('app.name') . ": New requirement near you — \"{$this->requirement->title}\". Propose now: " . route('provider.leads.show', $this->requirement);
    }
}
