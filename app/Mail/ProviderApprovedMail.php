<?php

namespace App\Mail;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProviderApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?SubscriptionPlan $plan = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Profile Has Been Approved!');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.provider-approved');
    }
}
