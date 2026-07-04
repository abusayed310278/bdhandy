@extends('layouts.dashboard')
@section('title', 'Reviews')

@section('content')
<div class="space-y-5 text-sm">

  <div>
    <h2 class="text-xl font-bold text-slate-900">Reviews</h2>
    <p class="text-slate-500 text-xs mt-0.5">Customer feedback for your services</p>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  {{-- Stats summary --}}
  @if($stats['total'] > 0)
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    <div class="flex flex-col sm:flex-row sm:items-center gap-6">
      <div class="text-center sm:text-left">
        <p class="text-5xl font-bold text-slate-900">{{ number_format($stats['avg'] ?? 0, 1) }}</p>
        <div class="flex items-center justify-center sm:justify-start gap-0.5 mt-1">
          @for($i = 1; $i <= 5; $i++)
            <svg class="w-4 h-4 {{ ($i <= round($stats['avg'] ?? 0)) ? 'text-yellow-400' : 'text-slate-200' }}" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          @endfor
        </div>
        <p class="text-xs text-slate-400 mt-1">{{ $stats['total'] }} review{{ $stats['total'] !== 1 ? 's' : '' }}</p>
      </div>
      <div class="flex-1 space-y-1.5">
        @for($i = 5; $i >= 1; $i--)
        @php $cnt = $stats['counts'][$i] ?? 0; $pct = $stats['total'] > 0 ? round($cnt / $stats['total'] * 100) : 0; @endphp
        <div class="flex items-center gap-2">
          <span class="text-xs text-slate-500 w-4 text-right">{{ $i }}</span>
          <svg class="w-3 h-3 text-yellow-400 shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          <div class="flex-1 bg-slate-100 rounded-full h-2">
            <div class="bg-yellow-400 h-2 rounded-full" style="width:{{ $pct }}%"></div>
          </div>
          <span class="text-xs text-slate-400 w-6 text-right">{{ $cnt }}</span>
        </div>
        @endfor
      </div>
    </div>
  </div>
  @endif

  {{-- Review list --}}
  @if($reviews->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      <p class="text-slate-500 font-medium">No reviews yet</p>
      <p class="text-slate-400 text-xs mt-1">Complete service requests to receive reviews from customers</p>
    </div>
  @else
    <div class="space-y-4">
      @foreach($reviews as $review)
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="flex items-start justify-between gap-3">
          <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center shrink-0">
              {{ strtoupper(substr($review->customer?->name ?? '?', 0, 2)) }}
            </div>
            <div>
              <p class="font-semibold text-slate-900">{{ $review->customer?->name ?? 'Customer' }}</p>
              <div class="flex items-center gap-0.5 mt-0.5">
                @for($i = 1; $i <= 5; $i++)
                  <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                @endfor
              </div>
            </div>
          </div>
          <p class="text-[11px] text-slate-400 shrink-0">{{ $review->created_at->diffForHumans() }}</p>
        </div>

        @if($review->review)
          <p class="text-xs text-slate-700 leading-relaxed mt-3 ml-12">{{ $review->review }}</p>
        @endif

        {{-- Existing reply --}}
        @if($review->reply)
        <div class="mt-3 ml-12 bg-slate-50 rounded-xl border border-slate-100 p-3">
          <p class="text-[11px] font-semibold text-primary-700 mb-1">Your Reply</p>
          <p class="text-xs text-slate-700">{{ $review->reply->reply }}</p>
        </div>
        @else
        {{-- Reply form --}}
        <div x-data="{ show: false }" class="mt-3 ml-12">
          <button @click="show = !show" class="text-xs text-primary-600 hover:underline font-medium">Reply to this review</button>
          <div x-show="show" x-cloak class="mt-2">
            <form action="{{ route('provider.reviews.reply', $review) }}" method="POST" class="space-y-2">
              @csrf
              <textarea name="reply" rows="3" required
                placeholder="Write a professional response…"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition resize-none"></textarea>
              <button type="submit" class="px-4 py-2 rounded-lg bg-primary-500 text-white text-xs font-semibold hover:bg-primary-600 transition">Post Reply</button>
            </form>
          </div>
        </div>
        @endif
      </div>
      @endforeach
    </div>

    @if($reviews->hasPages())
      <div class="py-2">{{ $reviews->links() }}</div>
    @endif
  @endif
</div>
@endsection
