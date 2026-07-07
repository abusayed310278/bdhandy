<?php

namespace App\Notifications\Payment;

use App\Models\Subscription;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionRenewalUpcoming extends BaseNotification
{
    /**
     * @param string $window Human-readable lead time, e.g. "3 days" or "6 hours".
     */
    public function __construct(public Subscription $subscription, public string $window) {}

    public function getEventType(): string
    {
        return 'subscription.renewal_upcoming';
    }

    public function toDatabase(mixed $notifiable): array
    {
        $plan = $this->subscription->plan;

        return [
            'type'  => 'subscription.renewal_upcoming',
            'title' => 'Add Balance to Continue Your Plan',
            'body'  => "Your {$plan->name} plan renews in {$this->window} but your wallet balance isn't enough to cover it ({$plan->price}). Top up now to avoid losing access.",
            'url'   => route('provider.wallet.index'),
            'meta'  => [
                'subscription_id' => $this->subscription->id,
                'plan_id'         => $plan->id,
                'window'          => $this->window,
            ],
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $plan = $this->subscription->plan;

        return (new MailMessage)
            ->subject("Renewal in {$this->window} — Add Balance to Continue")
            ->view('emails.notifications.payment', [
                'user'       => $notifiable,
                'heading'    => 'Add Balance to Continue Your Plan',
                'message'    => "Your {$plan->name} plan renews in {$this->window}. Your current wallet balance isn't enough to cover the renewal amount of {$plan->price}. Please top up your wallet before then to keep your plan active.",
                'actionUrl'  => route('provider.wallet.index'),
                'actionText' => 'Add Balance',
            ]);
    }

    public function toSms(mixed $notifiable): string
    {
        $plan = $this->subscription->plan;
        return config('app.name') . ": Your {$plan->name} plan renews in {$this->window}. Add balance now to avoid losing access.";
    }
}
