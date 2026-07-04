<?php

namespace App\Notifications\Payment;

use App\Models\ServiceRequestInvoice;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceRequestInvoicePaid extends BaseNotification
{
    public function __construct(public ServiceRequestInvoice $invoice) {}

    public function getEventType(): string
    {
        return 'service_request.invoice_paid';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'service_request.invoice_paid',
            'title' => 'Invoice Paid',
            'body'  => "Invoice #{$this->invoice->invoice_number} for {$this->invoice->total} has been paid.",
            'url'   => $this->resolveUrl($notifiable),
            'meta'  => [
                'invoice_id'         => $this->invoice->id,
                'invoice_number'     => $this->invoice->invoice_number,
                'service_request_id' => $this->invoice->service_request_id,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Invoice #{$this->invoice->invoice_number} Paid — " . config('app.name'))
            ->view('emails.notifications.payment', [
                'user'       => $notifiable,
                'invoice'    => $this->invoice,
                'heading'    => 'Invoice Payment Confirmed',
                'message'    => "Invoice #{$this->invoice->invoice_number} of {$this->invoice->total} has been successfully paid.",
                'actionUrl'  => $this->resolveUrl($notifiable),
                'actionText' => 'View Invoice',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        return config('app.name') . ": Invoice #{$this->invoice->invoice_number} paid ({$this->invoice->total}). Thank you!";
    }

    private function resolveUrl(mixed $notifiable): string
    {
        if ($notifiable->hasRole(['freelancer', 'business'])) {
            return route('provider.invoices.show', $this->invoice);
        }
        return route('customer.invoices.show', $this->invoice);
    }
}
