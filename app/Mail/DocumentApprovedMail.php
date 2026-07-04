<?php

namespace App\Mail;

use App\Models\ProviderDocument;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ProviderDocument $document,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Document Approved — ' . ($this->document->documentType->name ?? 'Document'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.document-approved');
    }
}
