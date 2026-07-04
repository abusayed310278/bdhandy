<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProviderRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $reason = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Action Required: Document Verification Update');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.provider-rejected');
    }
}
