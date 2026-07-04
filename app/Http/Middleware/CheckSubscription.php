<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isProvider()) {
            return $next($request);
        }

        $subscription = $user->activeSubscription();

        if (!$subscription) {
            return redirect()->route('provider.subscription.index')
                ->with('info', 'Please select a subscription plan to access your dashboard.');
        }

        // For paid (time-limited) plans, check expiry
        if ($subscription->end_date && $subscription->end_date->isPast()) {
            if ($subscription->subscription_status !== 'grace') {
                return redirect()->route('provider.subscription.index')
                    ->with('warning', 'Your subscription has expired. Please renew to continue.');
            }
        }

        return $next($request);
    }
}
