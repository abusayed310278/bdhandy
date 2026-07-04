<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'active_requests'   => $user->serviceRequests()->whereIn('request_status', ['pending', 'accepted', 'in_progress'])->count(),
            'open_requirements' => $user->customerRequirements()->where('status', 'open')->count(),
            'saved_providers'   => $user->savedProviders()->count(),
            'addresses'         => $user->customerAddresses()->count(),
        ];

        $recentRequests = $user->serviceRequests()
            ->with(['service.category', 'provider.providerProfile'])
            ->latest()
            ->take(5)
            ->get();

        $recentRequirements = $user->customerRequirements()
            ->with(['category', 'proposals'])
            ->where('status', 'open')
            ->latest()
            ->take(3)
            ->get();

        $unreadMessages = \App\Models\Conversation::where('customer_id', $user->id)
            ->whereHas('messages', fn($q) => $q->where('sender_id', '!=', $user->id)->where('is_read', false))
            ->count();

        return view('customer.dashboard', compact('user', 'stats', 'recentRequests', 'recentRequirements', 'unreadMessages'));
    }
}
