<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        $user             = Auth::user();
        $plans            = SubscriptionPlan::where('status', 'active')->orderBy('price')->get();
        $subscription     = $user->activeSubscription();
        $pastDue          = $user->pastDueSubscription();
        $invoices         = $user->subscriptions()->with('plan')->latest()->get();
        $walletBalance    = $user->wallet_balance;

        return view('provider.subscription.index', compact('plans', 'subscription', 'pastDue', 'invoices', 'walletBalance'));
    }
}
