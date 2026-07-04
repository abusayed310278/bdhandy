<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\CustomerRequirement;
use App\Models\Message;
use App\Models\Review;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user         = Auth::user();
        $profile      = $user->providerProfile;
        $subscription = $user->activeSubscription();
        $plan         = $subscription?->plan;

        // ── KPIs ──────────────────────────────────────────
        $serviceIds = $profile?->services()->pluck('service_id') ?? collect();

        $openLeads = CustomerRequirement::where('status', 'open')
            ->when($serviceIds->isNotEmpty(), fn($q) => $q->whereIn('service_id', $serviceIds))
            ->count();

        $pendingRequests = ServiceRequest::where('provider_id', $user->id)
            ->where('request_status', 'pending')
            ->count();

        $avgRating   = Review::where('provider_id', $user->id)->where('is_approved', true)->avg('rating');
        $totalReviews = Review::where('provider_id', $user->id)->where('is_approved', true)->count();

        // Unread messages count
        $unreadMessages = Message::whereHas('conversation', fn($q) =>
                $q->where('provider_id', $user->id)
            )
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        // Recent service requests (5 latest)
        $recentRequests = ServiceRequest::with(['customer', 'service', 'currency'])
            ->where('provider_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'open_leads'      => $openLeads,
            'pending_requests' => $pendingRequests,
            'avg_rating'      => $avgRating ? round($avgRating, 1) : null,
            'total_reviews'   => $totalReviews,
            'unread_messages' => $unreadMessages,
        ];

        return view('provider.dashboard', compact('user', 'profile', 'subscription', 'plan', 'stats', 'recentRequests'));
    }
}
