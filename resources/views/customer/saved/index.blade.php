@extends('layouts.dashboard')
@section('title', 'Saved Providers')

@section('content')
<div class="space-y-5 text-sm">
  <div>
    <h2 class="text-xl font-bold text-slate-900">Saved Providers</h2>
    <p class="text-slate-500 text-xs mt-0.5">Providers you've bookmarked for easy access</p>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  @if($saved->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
      <p class="text-slate-500 font-medium">No saved providers</p>
      <p class="text-slate-400 text-xs mt-1">Browse services and save providers you like</p>
      <a href="{{ route('categories') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">Browse Services</a>
    </div>
  @else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($saved as $sv)
      @php $profile = $sv->provider?->providerProfile; @endphp
      <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-200 transition">
        <div class="flex items-center gap-3 mb-3">
          @if($profile?->logo)
            <img src="{{ asset('storage/'.$profile->logo) }}" class="w-12 h-12 rounded-full object-cover shrink-0">
          @else
            <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center shrink-0">
              {{ strtoupper(substr($profile?->business_name ?? $sv->provider?->name ?? '?', 0, 2)) }}
            </div>
          @endif
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-slate-900 truncate">{{ $profile?->business_name ?? $sv->provider?->name }}</p>
            @if($profile?->avg_rating)
              <div class="flex items-center gap-1 mt-0.5">
                <span class="text-accent-400 text-xs">★</span>
                <span class="text-xs text-slate-600">{{ number_format($profile->avg_rating, 1) }}</span>
                <span class="text-[10px] text-slate-400">({{ $profile->total_reviews ?? 0 }})</span>
              </div>
            @endif
          </div>
        </div>

        @if($profile?->tagline)
          <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $profile->tagline }}</p>
        @endif

        @if($profile?->services->isNotEmpty())
          <div class="flex flex-wrap gap-1 mb-3">
            @foreach($profile->services->take(3) as $svc)
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                {{ ($svc->service?->getTranslation('translations','en')['name'] ?? null) ?: $svc->service?->slug }}
              </span>
            @endforeach
          </div>
        @endif

        <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
          @if($profile)
            <a href="{{ route('provider.profile.public', $profile->slug) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-bold transition">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
              View Profile
            </a>
          @endif
          <form action="{{ route('customer.saved.toggle', $profile) }}" method="POST" class="ms-auto">
            @csrf
            <button type="submit" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition" title="Remove">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            </button>
          </form>
        </div>
      </div>
      @endforeach
    </div>
    @if($saved->hasPages())
      <div class="py-4">{{ $saved->links() }}</div>
    @endif
  @endif
</div>
@endsection
