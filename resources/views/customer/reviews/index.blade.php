@extends('layouts.dashboard')
@section('title', 'My Reviews')

@section('content')
<div class="space-y-5 text-sm">
  <div>
    <h2 class="text-xl font-bold text-slate-900">My Reviews</h2>
    <p class="text-slate-500 text-xs mt-0.5">Feedback you've left for service providers</p>
  </div>

  @if($reviews->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      <p class="text-slate-500 font-medium">No reviews yet</p>
      <p class="text-slate-400 text-xs mt-1">After completing a service request you can leave a review</p>
    </div>
  @else
    <div class="space-y-4">
      @foreach($reviews as $review)
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="flex items-start justify-between gap-4">
          <div class="flex items-center gap-3">
            @if($review->serviceRequest?->provider?->providerProfile?->logo)
              <img src="{{ asset('storage/'.$review->serviceRequest->provider->providerProfile->logo) }}" class="w-11 h-11 rounded-full object-cover shrink-0">
            @else
              <div class="w-11 h-11 rounded-full bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center shrink-0">
                {{ strtoupper(substr($review->serviceRequest?->provider?->providerProfile?->business_name ?? '?', 0, 2)) }}
              </div>
            @endif
            <div>
              <p class="font-semibold text-slate-900">{{ $review->serviceRequest?->provider?->providerProfile?->business_name ?? '—' }}</p>
              <p class="text-[11px] text-slate-500">{{ $review->serviceRequest?->title }}</p>
            </div>
          </div>
          <div class="text-right shrink-0">
            <div class="flex items-center justify-end gap-0.5">
              @for($i=1;$i<=5;$i++)
                <span class="text-base {{ $i <= $review->rating ? 'text-accent-400' : 'text-slate-200' }}">★</span>
              @endfor
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ $review->created_at->diffForHumans() }}</p>
          </div>
        </div>

        @if($review->review)
          <p class="mt-3 text-xs text-slate-600 leading-relaxed">{{ $review->review }}</p>
        @endif

        @if($review->reply)
          <div class="mt-3 ms-4 border-l-2 border-primary-100 pl-3">
            <p class="text-[11px] text-slate-500 font-medium mb-0.5">Provider's reply</p>
            <p class="text-xs text-slate-600 leading-relaxed">{{ $review->reply->reply }}</p>
          </div>
        @endif
      </div>
      @endforeach
    </div>

    @if($reviews->hasPages())
      <div class="py-4">{{ $reviews->links() }}</div>
    @endif
  @endif
</div>
@endsection
