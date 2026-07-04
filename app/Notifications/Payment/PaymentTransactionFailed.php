<?php

namespace App\Notifications\Payment;

use App\Models\PaymentTransaction;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentTransactionFailed extends BaseNotification
{
    public function __construct(public PaymentTransaction $transaction) {}

    public function getEventType(): string
    {
        return 'payment.failed';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'payment.failed',
            'title' => 'Payment Failed',
            'body'  => "Your payment of {$this->transaction->amount} via {$this->transaction->gateway} failed. Please try again.",
            'url'   => route('customer.dashboard'),
            'meta'  => [
                'transaction_id' => $this->transaction->id,
                'amount'         => $this->transaction->amount,
                'gateway'        => $this->transaction->gateway,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Payment Failed — " . config('app.name'))
            ->view('emails.notifications.payment', [
                'user'        => $notifiable,
                'transaction' => $this->transaction,
                'heading'     => 'Payment Failed',
                'message'     => "Unfortunately your payment of {$this->transaction->amount} via {$this->transaction->gateway} could not be processed. Please check your payment details and try again.",
                'actionUrl'   => route('customer.dashboard'),
                'actionText'  => 'Try Again',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        return config('app.name') . ": Payment of {$this->transaction->amount} FAILED. Please retry or contact support.";
    }
}
