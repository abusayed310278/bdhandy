<?php

namespace App\Notifications\Payment;

use App\Models\PaymentTransaction;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentTransactionSuccessful extends BaseNotification
{
    public function __construct(public PaymentTransaction $transaction) {}

    public function getEventType(): string
    {
        return 'payment.successful';
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'type'  => 'payment.successful',
            'title' => 'Payment Successful',
            'body'  => "Your payment of {$this->transaction->amount} was processed successfully via {$this->transaction->gateway}.",
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
            ->subject("Payment Confirmed — " . config('app.name'))
            ->view('emails.notifications.payment', [
                'user'        => $notifiable,
                'transaction' => $this->transaction,
                'heading'     => 'Payment Successful',
                'message'     => "Your payment of {$this->transaction->amount} has been confirmed. Transaction ID: {$this->transaction->transaction_id}.",
                'actionUrl'   => route('customer.dashboard'),
                'actionText'  => 'Go to Dashboard',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        return config('app.name') . ": Payment of {$this->transaction->amount} confirmed. Ref: {$this->transaction->transaction_id}";
    }
}
