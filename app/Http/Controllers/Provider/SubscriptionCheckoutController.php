<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionBillingService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SubscriptionCheckoutController extends Controller
{
    public function __construct(
        private WalletService $wallet,
        private SubscriptionBillingService $billing,
    ) {}

    public function checkout(Request $request): RedirectResponse
    {
        $request->validate(['plan_id' => ['required', 'exists:subscription_plans,id']]);

        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Free plan — activate immediately, no Stripe
        if ($plan->price <= 0) {
            $this->billing->activateFree($user, $plan);

            return redirect()->route('provider.dashboard')
                ->with('success', "You're now on the {$plan->name} plan.");
        }

        // Enough wallet balance — activate/switch instantly, no Stripe.
        // Covers upgrades, downgrades, and renewals paid from balance.
        if ($this->wallet->hasSufficientBalance($user, $plan->price)) {
            $this->billing->activatePaid($user, $plan, 'wallet', 'WALLET-' . Str::uuid());

            return redirect()->route('provider.dashboard')
                ->with('success', "You're now on the {$plan->name} plan.");
        }

        // Insufficient balance — direct payment via Stripe (first purchase,
        // or topping the shortfall for an upgrade/downgrade).
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode'        => 'payment',
            'line_items'  => [[
                'price_data' => [
                    'currency'     => strtolower($plan->currency->code ?? 'usd'),
                    'unit_amount'  => (int) ($plan->price * 100),
                    'product_data' => [
                        'name' => $plan->name . ($plan->duration_months ? " ({$plan->duration_months} months)" : ''),
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('provider.subscription.checkout.success', ['session_id' => '{CHECKOUT_SESSION_ID}', 'plan_id' => $plan->id]),
            'cancel_url'  => route('provider.subscription.index'),
            'metadata'    => [
                'provider_id' => $user->id,
                'plan_id'     => $plan->id,
            ],
        ]);

        // Store pending subscription
        Subscription::updateOrCreate(
            ['provider_id' => $user->id],
            [
                'plan_id'                    => $plan->id,
                'stripe_checkout_session_id' => $session->id,
                'start_date'                 => now(),
                'end_date'                   => null,
                'subscription_status'        => 'pending',
                'payment_status'             => 'pending',
                'auto_renew'                 => false,
            ]
        );

        return redirect($session->url);
    }

    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');
        $planId    = $request->query('plan_id');

        if (!$sessionId) {
            return redirect()->route('provider.subscription.index');
        }

        $user = Auth::user();

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect()->route('provider.subscription.index')
                ->with('error', 'Payment was not completed. Please try again.');
        }

        $plan = SubscriptionPlan::findOrFail($planId);

        $this->billing->activatePaid($user, $plan, 'stripe', $session->payment_intent ?? $session->id);

        return redirect()->route('provider.dashboard')
            ->with('success', "Payment successful! You're now on the {$plan->name} plan.");
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('provider.subscription.index')
            ->with('info', 'Checkout cancelled. Choose a plan when you\'re ready.');
    }
}
