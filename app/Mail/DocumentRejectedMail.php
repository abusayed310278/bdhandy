<?php

namespace App\Mail;

use App\Models\ProviderDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ProviderDocument $document,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required — ' . ($this->document->documentType->name ?? 'Document') . ' Needs Update',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.document-rejected');
    }
}
