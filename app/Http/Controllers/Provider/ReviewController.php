<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $reviews = Review::with(['customer', 'serviceRequest', 'reply'])
            ->where('provider_id', $user->id)
            ->where('is_approved', true)
            ->latest()
            ->paginate(20);

        $stats = [
            'total'  => Review::where('provider_id', $user->id)->where('is_approved', true)->count(),
            'avg'    => Review::where('provider_id', $user->id)->where('is_approved', true)->avg('rating'),
            'counts' => Review::where('provider_id', $user->id)
                ->where('is_approved', true)
                ->selectRaw('rating, count(*) as cnt')
                ->groupBy('rating')
                ->pluck('cnt', 'rating')
                ->toArray(),
        ];

        return view('provider.reviews.index', compact('reviews', 'stats'));
    }

    public function reply(Request $request, Review $review): RedirectResponse
    {
        $user = Auth::user();

        if ($review->provider_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'reply' => ['required', 'string', 'max:1000'],
        ]);

        ReviewReply::updateOrCreate(
            ['review_id' => $review->id, 'provider_id' => $user->id],
            ['reply' => $request->reply]
        );

        return back()->with('success', 'Reply posted.');
    }
}
