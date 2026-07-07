<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionBillingService
{
    public function __construct(
        private WalletService $wallet,
        private AffiliateCommissionService $affiliate,
    ) {}

    public function activateFree(User $user, SubscriptionPlan $plan): Subscription
    {
        $endDate = $plan->duration_months > 0 ? now()->addMonths($plan->duration_months) : null;

        return Subscription::updateOrCreate(
            ['provider_id' => $user->id],
            [
                'plan_id'             => $plan->id,
                'start_date'          => now(),
                'end_date'            => $endDate,
                'next_billing_at'     => null,
                'notified_3_day_at'   => null,
                'notified_6_hour_at'  => null,
                'subscription_status' => 'active',
                'payment_status'      => 'paid',
                'auto_renew'          => false,
            ]
        );
    }

    /**
     * Activates a paid plan — either a first purchase, or an upgrade/downgrade.
     * $gateway is 'wallet' (this method performs the debit) or 'stripe' (already charged).
     */
    public function activatePaid(User $user, SubscriptionPlan $plan, string $gateway, string $transactionId): Subscription
    {
        return DB::transaction(function () use ($user, $plan, $gateway, $transactionId) {
            if ($gateway === 'wallet') {
                $debited = $this->wallet->debit(
                    $user,
                    $plan->price,
                    'subscription_charge',
                    "Plan activation: {$plan->name}"
                );

                if (!$debited) {
                    throw new \RuntimeException('Insufficient wallet balance.');
                }
            }

            $endDate = $plan->duration_months > 0 ? now()->addMonths($plan->duration_months) : null;

            $subscription = Subscription::updateOrCreate(
                ['provider_id' => $user->id],
                [
                    'plan_id'             => $plan->id,
                    'start_date'          => now(),
                    'end_date'            => $endDate,
                    'next_billing_at'     => now()->addMonth(),
                    'notified_3_day_at'   => null,
                    'notified_6_hour_at'  => null,
                    'subscription_status' => 'active',
                    'payment_status'      => 'paid',
                    'auto_renew'          => true,
                ]
            );

            $invoice = $this->recordPayment($user, $subscription, $plan, $gateway, $transactionId);

            $this->affiliate->creditForFirstQualifyingTransaction($user, $plan->price, $plan->currency_id, $subscription);

            return $subscription;
        });
    }

    /**
     * Attempts the monthly recurring charge (or a recovery retry for a
     * past_due subscription). Returns true on success, false if the wallet
     * still doesn't have enough balance.
     */
    public function renew(Subscription $subscription): bool
    {
        return DB::transaction(function () use ($subscription) {
            $user = $subscription->provider;
            $plan = $subscription->plan;

            $debited = $this->wallet->debit(
                $user,
                $plan->price,
                'subscription_charge',
                "Monthly renewal: {$plan->name}"
            );

            if (!$debited) {
                $this->recordFailedPayment($user, $plan);
                $subscription->update(['subscription_status' => 'past_due']);
                return false;
            }

            $this->recordPayment($user, $subscription, $plan, 'wallet', 'WALLET-' . Str::uuid());

            $subscription->update([
                'next_billing_at'     => now()->addMonth(),
                'notified_3_day_at'   => null,
                'notified_6_hour_at'  => null,
                'subscription_status' => 'active',
            ]);

            return true;
        });
    }

    private function recordPayment(User $user, Subscription $subscription, SubscriptionPlan $plan, string $gateway, string $transactionId): SubscriptionInvoice
    {
        $invoice = SubscriptionInvoice::create([
            'subscription_id' => $subscription->id,
            'invoice_number'  => 'INV-' . strtoupper(Str::random(10)),
            'subtotal'        => $plan->price,
            'discount'        => 0,
            'total'           => $plan->price,
            'currency_id'     => $plan->currency_id,
            'payment_method'  => $gateway,
            'payment_status'  => 'paid',
            'paid_at'         => now(),
        ]);

        PaymentTransaction::create([
            'user_id'        => $user->id,
            'invoice_id'     => $invoice->id,
            'gateway'        => $gateway,
            'transaction_id' => $transactionId,
            'amount'         => $plan->price,
            'currency_id'    => $plan->currency_id,
            'status'         => 'success',
            'paid_at'        => now(),
        ]);

        return $invoice;
    }

    private function recordFailedPayment(User $user, SubscriptionPlan $plan): void
    {
        PaymentTransaction::create([
            'user_id'          => $user->id,
            'invoice_id'       => null,
            'gateway'          => 'wallet',
            'transaction_id'   => 'WALLET-' . Str::uuid(),
            'amount'           => $plan->price,
            'currency_id'      => $plan->currency_id,
            'status'           => 'failed',
            'gateway_response' => ['reason' => 'insufficient_balance'],
        ]);
    }
}
