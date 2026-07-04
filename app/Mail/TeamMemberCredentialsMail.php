<?php

namespace App\Mail;

use App\Models\TeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamMemberCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TeamMember $member,
        public string $password,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        $business = $this->member->business?->business_name ?? config('app.name');
        return new Envelope(subject: "Your {$business} team account is ready");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.team-member-credentials');
    }
}
