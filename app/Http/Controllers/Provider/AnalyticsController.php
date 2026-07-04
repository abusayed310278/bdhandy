<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $user    = Auth::user();
        $profile = $user->providerProfile;

        // Requests per month (last 6 months)
        $requestsByMonth = ServiceRequest::where('provider_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Build 6-month labels
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $key          = now()->subMonths($i)->format('Y-m');
            $months[$key] = $requestsByMonth->get($key)?->total ?? 0;
        }

        // Status breakdown
        $statusBreakdown = ServiceRequest::where('provider_id', $user->id)
            ->selectRaw('request_status, count(*) as cnt')
            ->groupBy('request_status')
            ->pluck('cnt', 'request_status')
            ->toArray();

        // Rating distribution
        $ratingDist = Review::where('provider_id', $user->id)
            ->where('is_approved', true)
            ->selectRaw('rating, count(*) as cnt')
            ->groupBy('rating')
            ->pluck('cnt', 'rating')
            ->toArray();

        $avgRating   = Review::where('provider_id', $user->id)->where('is_approved', true)->avg('rating');
        $totalReviews = Review::where('provider_id', $user->id)->where('is_approved', true)->count();

        // Total and completed requests
        $totalRequests     = ServiceRequest::where('provider_id', $user->id)->count();
        $completedRequests = ServiceRequest::where('provider_id', $user->id)->where('request_status', 'completed')->count();

        return view('provider.analytics.index', compact(
            'months',
            'statusBreakdown',
            'ratingDist',
            'avgRating',
            'totalReviews',
            'totalRequests',
            'completedRequests',
        ));
    }
}
