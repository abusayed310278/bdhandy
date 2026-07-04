<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\AffiliateSystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $affiliate = AffiliateSystem::firstOrCreate(
            ['user_id' => $user->id],
            [
                'referral_code'   => strtoupper(Str::random(8)),
                'commission_type' => 'percentage',
                'commission_value' => 50,
                'minimum_payout'  => 500,
                'total_earnings'  => 0,
                'total_paid'      => 0,
                'status'          => 'active',
            ]
        );

        $referrals = $affiliate->referrals()
            ->with(['referredUser', 'subscription.plan'])
            ->latest()
            ->paginate(20);

        $totalReferrals  = $affiliate->referrals()->count();
        $pendingEarnings = $affiliate->referrals()
            ->where('commission_status', 'pending')
            ->sum('commission_amount');
        $balance = $affiliate->total_earnings - $affiliate->total_paid;

        return view('provider.affiliate.index', compact(
            'affiliate', 'referrals', 'totalReferrals', 'pendingEarnings', 'balance'
        ));
    }
}
