<?php

namespace App\Observers;

use App\Models\SubscriptionInvoice;
use App\Notifications\Payment\SubscriptionInvoicePaid;
use App\Services\NotificationService;

class SubscriptionInvoiceObserver
{
    public function __construct(private NotificationService $notifier) {}

    public function updated(SubscriptionInvoice $invoice): void
    {
        if (!$invoice->isDirty('payment_status')) return;
        if ($invoice->payment_status !== 'paid') return;

        $provider = $invoice->subscription?->provider;
        if (!$provider) return;

        $this->notifier->send($provider, new SubscriptionInvoicePaid($invoice));
    }
}
