<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\SubscriptionBillingService;
use Illuminate\Console\Command;

class SubscriptionsProcessBillingCommand extends Command
{
    protected $signature   = 'subscriptions:process-billing';
    protected $description = 'Charges monthly renewals due today from wallet balance, and retries past_due subscriptions.';

    public function handle(SubscriptionBillingService $billing): int
    {
        $charged = 0;
        $failed  = 0;

        Subscription::with('plan', 'provider')
            ->where('auto_renew', true)
            ->where(function ($query) {
                $query->where('subscription_status', 'past_due')
                    ->orWhere(function ($q) {
                        $q->where('subscription_status', 'active')
                          ->where('next_billing_at', '<=', now());
                    });
            })
            ->chunkById(100, function ($subscriptions) use ($billing, &$charged, &$failed) {
                foreach ($subscriptions as $subscription) {
                    if (!$subscription->plan || !$subscription->provider) continue;

                    if ($billing->renew($subscription)) {
                        $charged++;
                        $this->line("  ✓ Renewed subscription #{$subscription->id} ({$subscription->provider->name})");
                    } else {
                        $failed++;
                        $this->line("  ✗ Insufficient balance for subscription #{$subscription->id} ({$subscription->provider->name}) — marked past_due");
                    }
                }
            });

        $this->info("Done. {$charged} renewed, {$failed} past_due.");
        return self::SUCCESS;
    }
}
