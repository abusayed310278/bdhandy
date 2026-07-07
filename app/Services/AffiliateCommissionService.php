<?php

namespace App\Services;

use App\Models\AffiliateSystem;
use App\Models\Currency;
use App\Models\Referral;
use App\Models\Subscription;
use App\Models\User;

class AffiliateCommissionService
{
    public function __construct(private WalletService $wallet) {}

    /**
     * Credits the referring affiliate on a referred user's FIRST qualifying
     * transaction — either their first paid subscription or their first
     * wallet top-up, whichever happens first (enforced by the one-referral
     * guard below).
     *
     * Provider affiliates are credited instantly to their wallet balance
     * (usable toward their own subscription). Non-provider affiliates keep
     * the existing manual bank/mobile-banking payout flow.
     */
    public function creditForFirstQualifyingTransaction(User $referredUser, float $amount, int $currencyId, ?Subscription $subscription = null): void
    {
        if (empty($referredUser->referred_by)) {
            return;
        }

        // Only ever credit once per referred user
        if (Referral::where('referred_user_id', $referredUser->id)->exists()) {
            return;
        }

        $affiliateSystem = AffiliateSystem::where('referral_code', $referredUser->referred_by)
            ->where('status', 'active')
            ->first();

        if (!$affiliateSystem) {
            return;
        }

        $commission = round($amount * $affiliateSystem->commission_value / 100, 2);

        $cap = Currency::find($currencyId)?->affiliate_commission_cap;
        if ($cap !== null) {
            $commission = min($commission, (float) $cap);
        }

        $isProviderAffiliate = $affiliateSystem->user?->isProvider() ?? false;

        $referral = Referral::create([
            'affiliate_id'      => $affiliateSystem->id,
            'referred_user_id'  => $referredUser->id,
            'subscription_id'   => $subscription?->id,
            'commission_amount' => $commission,
            'commission_status' => $isProviderAffiliate ? 'paid' : 'pending',
            'paid_at'           => $isProviderAffiliate ? now() : null,
        ]);

        $affiliateSystem->increment('total_earnings', $commission);

        if ($isProviderAffiliate) {
            $this->wallet->credit(
                $affiliateSystem->user,
                $commission,
                'affiliate_commission',
                "Referral commission from {$referredUser->name}",
                $referral
            );
            $affiliateSystem->increment('total_paid', $commission);
        }
    }
}
