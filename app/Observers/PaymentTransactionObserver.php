<?php

namespace App\Observers;

use App\Models\PaymentTransaction;
use App\Notifications\Payment\PaymentTransactionSuccessful;
use App\Notifications\Payment\PaymentTransactionFailed;
use App\Services\NotificationService;

class PaymentTransactionObserver
{
    public function __construct(private NotificationService $notifier) {}

    public function updated(PaymentTransaction $transaction): void
    {
        if (!$transaction->isDirty('status')) return;

        $user = $transaction->user;
        if (!$user) return;

        match ($transaction->status) {
            'paid', 'completed', 'success' => $this->notifier->send($user, new PaymentTransactionSuccessful($transaction)),
            'failed'                        => $this->notifier->send($user, new PaymentTransactionFailed($transaction)),
            default                         => null,
        };
    }
}
