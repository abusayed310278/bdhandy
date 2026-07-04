<?php

namespace App\Mail;

use App\Models\TeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamMemberInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TeamMember $member, public string $setupUrl) {}

    public function envelope(): Envelope
    {
        $business = $this->member->business?->business_name ?? config('app.name');
        return new Envelope(subject: "You've been invited to join {$business} on " . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.team-member-invite');
    }
}
