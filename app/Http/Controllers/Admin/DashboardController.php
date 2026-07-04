<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_providers' => ProviderProfile::count(),
            'total_customers' => User::role('customer')->count(),
            'pending_verif'   => ProviderProfile::where('verification_status', 'in_review')->count(),
            'active_subs'     => Subscription::where('subscription_status', 'active')->count(),
        ];

        $recentProviders = ProviderProfile::with('user')
            ->where('verification_status', 'in_review')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentProviders'));
    }
}
