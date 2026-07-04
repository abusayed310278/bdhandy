<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Auth::user()->reviews()
            ->with(['serviceRequest.service', 'serviceRequest.provider.providerProfile', 'reply'])
            ->latest()
            ->paginate(10);

        return view('customer.reviews.index', compact('reviews'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'service_request_id' => ['required', 'exists:service_requests,id'],
            'rating'             => ['required', 'integer', 'min:1', 'max:5'],
            'review'             => ['nullable', 'string', 'max:1000'],
        ]);

        $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);

        if ($serviceRequest->customer_id !== Auth::id()) abort(403);
        if ($serviceRequest->request_status !== 'completed') {
            return back()->with('error', 'Only completed requests can be reviewed.');
        }

        $exists = Review::where('service_request_id', $serviceRequest->id)
            ->where('customer_id', Auth::id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already reviewed this request.');
        }

        Review::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => Auth::id(),
            'provider_id'        => $serviceRequest->provider_id,
            'rating'             => $request->rating,
            'review'             => $request->review,
            'is_approved'        => true,
        ]);

        return redirect()->route('customer.requests.show', $serviceRequest)->with('success', 'Review submitted!');
    }
}
