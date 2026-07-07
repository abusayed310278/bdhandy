<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Services\AffiliateCommissionService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class WalletController extends Controller
{
    public function __construct(
        private WalletService $wallet,
        private AffiliateCommissionService $affiliate,
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        $transactions = $user->walletTransactions()->latest()->paginate(20);

        return view('provider.wallet.index', [
            'balance'      => $user->wallet_balance,
            'transactions' => $transactions,
        ]);
    }

    public function topup(Request $request): RedirectResponse
    {
        $request->validate(['amount' => ['required', 'numeric', 'min:1']]);

        $user     = Auth::user();
        $amount   = round((float) $request->amount, 2);
        $currency = $user->providerProfile?->currency;

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode'        => 'payment',
            'line_items'  => [[
                'price_data' => [
                    'currency'     => strtolower($currency?->code ?? 'usd'),
                    'unit_amount'  => (int) ($amount * 100),
                    'product_data' => [
                        'name' => 'Wallet Top-up',
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('provider.wallet.topup.success', ['session_id' => '{CHECKOUT_SESSION_ID}', 'amount' => $amount]),
            'cancel_url'  => route('provider.wallet.index'),
            'metadata'    => [
                'provider_id' => $user->id,
                'amount'      => $amount,
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');
        $amount    = round((float) $request->query('amount'), 2);

        if (!$sessionId || $amount <= 0) {
            return redirect()->route('provider.wallet.index');
        }

        $user = Auth::user();

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect()->route('provider.wallet.index')
                ->with('error', 'Payment was not completed. Please try again.');
        }

        $currencyId = $user->providerProfile?->currency_id;

        $this->wallet->credit($user, $amount, 'topup', 'Wallet top-up via Stripe');

        PaymentTransaction::create([
            'user_id'        => $user->id,
            'invoice_id'     => null,
            'gateway'        => 'stripe',
            'transaction_id' => $session->payment_intent ?? $session->id,
            'amount'         => $amount,
            'currency_id'    => $currencyId,
            'status'         => 'success',
            'paid_at'        => now(),
        ]);

        if ($currencyId) {
            $this->affiliate->creditForFirstQualifyingTransaction($user, $amount, $currencyId);
        }

        return redirect()->route('provider.wallet.index')
            ->with('success', "৳{$amount} has been added to your wallet balance.");
    }
}
