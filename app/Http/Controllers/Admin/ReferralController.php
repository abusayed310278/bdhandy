<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ReferralController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view referrals', only: ['index']),
            new Middleware('permission:edit referrals', only: ['markPaid']),
        ];
    }

    public function index(): View
    {
        // Provider-affiliate commissions are auto-credited to wallet instantly —
        // this queue is only for non-provider affiliates awaiting manual bank/mobile-banking payout.
        $referrals = Referral::with(['affiliate.user', 'referredUser'])
            ->where('commission_status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.referrals.index', compact('referrals'));
    }

    public function markPaid(Referral $referral): RedirectResponse
    {
        if ($referral->commission_status === 'paid') {
            return back()->with('info', 'This referral has already been paid.');
        }

        $referral->update([
            'commission_status' => 'paid',
            'paid_at'            => now(),
        ]);

        $referral->affiliate->increment('total_paid', $referral->commission_amount);

        return back()->with('success', 'Referral marked as paid via bank/mobile banking.');
    }
}
