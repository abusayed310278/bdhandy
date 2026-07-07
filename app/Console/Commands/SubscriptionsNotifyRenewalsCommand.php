<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class SubscriptionsNotifyRenewalsCommand extends Command
{
    protected $signature   = 'subscriptions:notify-renewals';
    protected $description = 'Warns providers by email when their wallet balance won\'t cover an upcoming renewal (3 days and 6 hours out).';

    public function handle(): int
    {
        $notified = 0;
        $notified += $this->notifyWindow(days: 3, hours: null, column: 'notified_3_day_at');
        $notified += $this->notifyWindow(days: null, hours: 6, column: 'notified_6_hour_at');

        $this->info("Done. {$notified} reminder(s) sent.");
        return self::SUCCESS;
    }

    private function notifyWindow(?int $days, ?int $hours, string $column): int
    {
        $windowStart = $days ? now()->addDays($days) : now()->addHours($hours);
        $windowEnd   = (clone $windowStart)->addMinutes(35);

        $count = 0;

        Subscription::with('plan', 'provider')
            ->where('subscription_status', 'active')
            ->where('auto_renew', true)
            ->whereNull($column)
            ->whereBetween('next_billing_at', [$windowStart, $windowEnd])
            ->chunkById(100, function ($subscriptions) use ($column, &$count) {
                foreach ($subscriptions as $subscription) {
                    $plan = $subscription->plan;
                    $provider = $subscription->provider;
                    if (!$plan || !$provider) continue;

                    if ($provider->wallet_balance >= $plan->price) continue;

                    $subscription->update([$column => now()]);
                    $count++;
                }
            });

        return $count;
    }
}
