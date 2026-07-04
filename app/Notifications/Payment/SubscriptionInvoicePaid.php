<?php

namespace App\Notifications\Payment;

use App\Models\SubscriptionInvoice;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionInvoicePaid extends BaseNotification
{
    public function __construct(public SubscriptionInvoice $invoice) {}

    public function getEventType(): string
    {
        return 'subscription.invoice_paid';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'subscription.invoice_paid',
            'title' => 'Subscription Renewed',
            'body'  => "Your subscription invoice #{$this->invoice->invoice_number} of {$this->invoice->total} has been paid. Your plan is active.",
            'url'   => route('provider.subscription.index'),
            'meta'  => [
                'invoice_id'      => $this->invoice->id,
                'invoice_number'  => $this->invoice->invoice_number,
                'subscription_id' => $this->invoice->subscription_id,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Subscription Invoice #{$this->invoice->invoice_number} Paid — " . config('app.name'))
            ->view('emails.notifications.payment', [
                'user'       => $notifiable,
                'invoice'    => $this->invoice,
                'heading'    => 'Subscription Renewed',
                'message'    => "Your subscription has been renewed. Invoice #{$this->invoice->invoice_number} of {$this->invoice->total} has been paid successfully.",
                'actionUrl'  => route('provider.subscription.index'),
                'actionText' => 'View Subscription',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        return config('app.name') . ": Subscription renewed. Invoice #{$this->invoice->invoice_number} paid ({$this->invoice->total}).";
    }
}
