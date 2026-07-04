@extends('layouts.website')

@section('title', config('app.name') . ' — ' . __('web.hero.headline', ['span'=>'', 'end'=>'']))

@push('head')
<style>
@keyframes ticker-scroll {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
.ticker-track {
  animation: ticker-scroll 45s linear infinite;
  will-change: transform;
}
.ticker-track:hover { animation-play-state: paused; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-accent-50">
  <div class="absolute inset-0 opacity-40 pointer-events-none" aria-hidden="true">
    <svg class="absolute top-10 end-10 w-40 h-40 text-primary-200" viewBox="0 0 100 100" fill="none">
      <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="1" fill="currentColor"/></pattern>
      <rect width="100" height="100" fill="url(#dots)"/>
    </svg>
  </div>
  <div class="relative max-w-7xl mx-auto px-4 lg:px-6 py-12 md:py-16 lg:py-20 grid lg:grid-cols-12 gap-8 lg:gap-10 items-center">
    <div class="lg:col-span-7">
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-primary-100 text-primary-700 text-xs font-medium shadow-sm">
        <span class="w-1.5 h-1.5 rounded-full bg-accent-500"></span>
        {{ __('web.hero.badge') }}
      </span>
      <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 leading-tight tracking-tight">
        {!! __('web.hero.headline', ['span' => '<span class="text-primary-600">', 'end' => '</span>']) !!}
      </h1>
      <p class="mt-4 text-base sm:text-lg text-slate-500 max-w-xl">{{ __('web.hero.subtext') }}</p>

      {{-- Search widget --}}
      <form action="{{ route('providers') }}" method="GET" class="mt-6 bg-white rounded-2xl shadow-xl border border-slate-200 p-2 sm:p-3">
        <div class="flex flex-col sm:flex-row gap-2">
          <label class="flex-1 flex items-center gap-2 px-3 py-2.5 sm:py-2 rounded-xl sm:rounded-lg hover:bg-slate-50 focus-within:bg-slate-50 transition">
            <lottie-player src="{{ asset('lottie/magnifier.json') }}" background="transparent" speed="1" style="width: 24px; height: 24px;" loop autoplay class="shrink-0"></lottie-player>
            <div class="flex-1 min-w-0">
              <span class="block text-[11px] uppercase tracking-wide text-slate-500 font-medium">{{ __('web.hero.service_label') }}</span>
              <input type="text" name="q" placeholder="{{ __('web.hero.service_ph') }}" class="block w-full text-sm text-slate-900 placeholder-slate-400 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none">
            </div>
          </label>
          <div class="hidden sm:block w-px bg-slate-200"></div>
          <label class="flex-1 flex items-center gap-2 px-3 py-2.5 sm:py-2 rounded-xl sm:rounded-lg hover:bg-slate-50 focus-within:bg-slate-50 transition">
            <lottie-player src="{{ asset('lottie/location.json') }}" background="transparent" speed="1" style="width: 24px; height: 24px;" loop autoplay class="shrink-0"></lottie-player>
            <div class="flex-1 min-w-0">
              <span class="block text-[11px] uppercase tracking-wide text-slate-500 font-medium">{{ __('web.hero.location_label') }}</span>
              <input type="text" name="location" placeholder="{{ __('website/home.location_placeholder') }}" id="hero-location" class="block w-full text-sm text-slate-900 placeholder-slate-400 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none">
              <input type="hidden" name="lat" id="hero-lat">
              <input type="hidden" name="lng" id="hero-lng">
            </div>
          </label>
          <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl sm:rounded-lg bg-primary-500 text-white font-medium hover:bg-primary-600 active:bg-primary-700 transition shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            {{ __('web.hero.search') }}
          </button>
        </div>
      </form>

      <div class="mt-4 flex flex-wrap items-center gap-2">
        <span class="text-xs text-slate-500">{{ __('web.hero.popular') }}</span>
        @foreach([
          ['q' => 'AC Repair', 'label' => __('website/home.tags.ac_repair')],
          ['q' => 'Electrician', 'label' => __('website/home.tags.electrician')],
          ['q' => 'Deep Cleaning', 'label' => __('website/home.tags.deep_cleaning')],
          ['q' => 'CCTV Install', 'label' => __('website/home.tags.cctv_install')],
          ['q' => 'Plumbing', 'label' => __('website/home.tags.plumbing')],
        ] as $tag)
          <a href="{{ route('providers', ['q' => $tag['q']]) }}" class="px-2.5 py-1 rounded-full text-xs bg-white border border-slate-200 text-slate-600 hover:border-primary-300 hover:text-primary-600 transition">{{ $tag['label'] }}</a>
        @endforeach
      </div>
    </div>

    {{-- Hero illustration --}}
    <div class="lg:col-span-5 relative hidden md:block">
      <div class="relative w-full max-w-md mx-auto">
        <div class="absolute -top-6 -start-4 lg:start-0 bg-white rounded-2xl shadow-xl border border-slate-200 p-3 w-56 z-10 rotate-[-3deg]">
          <div class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden shrink-0">
              <lottie-player src="{{ asset('lottie/face.json') }}" background="transparent" speed="1" style="width: 36px; height: 36px;" loop autoplay></lottie-player>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-slate-900 truncate">Rahim Ahmed</p>
              <p class="text-xs text-slate-500">{{ __('website/home.illustration.ac_tech') }}</p>
            </div>
            <span class="text-[10px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded-full">✓</span>
          </div>
          <div class="mt-2 flex items-center gap-1 text-xs">
            <span class="text-accent-500">★★★★★</span>
            <span class="text-slate-600">4.9</span>
            <span class="text-slate-400">·</span>
            <span class="text-slate-500">2.4 km</span>
          </div>
        </div>
        <div class="relative aspect-square bg-gradient-to-br from-primary-100 via-primary-50 to-accent-100 rounded-full flex items-center justify-center">
          <div class="absolute inset-6 bg-white rounded-full shadow-soft flex items-center justify-center">
            <div class="text-center">
              <div class="w-32 h-32 mx-auto flex items-center justify-center">
                <lottie-player src="{{ asset('lottie/tools.json') }}" background="transparent" speed="1"  loop autoplay></lottie-player>
              </div>
              <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['providers']) }}+</p>
              <p class="text-xs text-slate-500 font-medium">{{ __('website/home.illustration.verified_pros') }}</p>
            </div>
          </div>
          <div class="absolute top-4 end-0 w-12 h-12 rounded-full bg-blue-100 text-white flex items-center justify-center shadow-warm">
            <lottie-player src="{{ asset('lottie/spark.json') }}" background="transparent" speed="1" style="width: 32px; height: 32px;" loop autoplay></lottie-player>
          </div>
          <div class="absolute bottom-4 start-0 w-12 h-12 rounded-full bg-primary-500 text-white flex items-center justify-center shadow-soft">
            <lottie-player src="{{ asset('lottie/snow.json') }}" background="transparent" speed="1" style="width: 32px; height: 32px;" loop autoplay></lottie-player>
          </div>
          <div class="absolute bottom-24 end-0 w-14 h-14 rounded-full bg-white border border-slate-200 flex items-center justify-center">
            <lottie-player src="{{ asset('lottie/clean.json') }}" background="transparent" speed="1" style="width: 26px; height: 26px;" loop autoplay></lottie-player>
          </div>
        </div>
        <div class="absolute -bottom-4 end-0 bg-white rounded-2xl shadow-xl border border-slate-200 p-3 w-64 z-10 rotate-[3deg] flex items-center gap-3">
          <div class="w-16 h-16 shrink-0 flex items-center justify-center">
            <lottie-player src="{{ asset('lottie/person.json') }}" background="transparent" speed="1" style="width: 64px; height: 64px;" loop autoplay></lottie-player>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1 text-xs text-accent-500 mb-0.5">★★★★★</div>
            <p class="text-xs text-slate-600 leading-snug">{{ __('website/home.illustration.quote') }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ __('website/home.illustration.author') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Live Activity Ticker --}}
@php
  $locale = app()->getLocale();

  // Merge leads (public) + requests (anonymised) then shuffle for variety
  $tickerAll = collect();
  foreach ($tickerLeads    as $item) { $tickerAll->push(['type' => 'lead',    'obj' => $item]); }
  foreach ($tickerRequests as $item) { $tickerAll->push(['type' => 'request', 'obj' => $item]); }
  $tickerAll = $tickerAll->shuffle()->values();

  // Pad to minimum 8 so the loop always looks full
  $pad = $tickerAll->count();
  if ($pad > 0) {
    while ($tickerAll->count() < 8) { $tickerAll = $tickerAll->concat($tickerAll->take($pad)); }
  }
@endphp

@if($tickerAll->count())
<div class="relative bg-white border-b border-slate-100 overflow-hidden" style="height:48px;">

  {{-- Left: LIVE badge + gradient mask --}}
  <div class="absolute start-0 top-0 bottom-0 z-10 flex items-center ps-4 pe-10 pointer-events-none"
       style="background:linear-gradient(to right,#fff 78%,transparent);">
    <div class="flex items-center gap-1.5 shrink-0">
      <span class="relative flex h-2 w-2 shrink-0">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
        <span class="relative inline-flex h-2 w-2 rounded-full bg-red-500"></span>
      </span>
      <span class="text-[10px] font-bold tracking-[0.15em] uppercase text-slate-500 whitespace-nowrap">Live</span>
    </div>
  </div>

  {{-- Right gradient mask --}}
  <div class="absolute end-0 top-0 bottom-0 w-16 z-10 pointer-events-none"
       style="background:linear-gradient(to left,#fff 60%,transparent);"></div>

  {{-- Scrolling track — items duplicated for seamless infinite loop --}}
  <div class="ticker-track flex items-center h-full ps-28" style="width:max-content;">
    @php
      $avatarPalette = [
        'bg-blue-100 text-blue-700',
        'bg-emerald-100 text-emerald-700',
        'bg-violet-100 text-violet-700',
        'bg-amber-100 text-amber-700',
        'bg-rose-100 text-rose-700',
        'bg-teal-100 text-teal-700',
        'bg-indigo-100 text-indigo-700',
        'bg-orange-100 text-orange-700',
      ];
    @endphp

    @foreach ([$tickerAll, $tickerAll] as $pass)
      @foreach ($pass as $item)
        @php
          $obj      = $item['obj'];
          $customer = $obj->customer;

          // Name → "Rahim A."
          $fullName  = trim($customer?->name ?? '');
          $parts     = $fullName ? explode(' ', $fullName) : ['User'];
          $shortName = count($parts) > 1
              ? $parts[0] . ' ' . strtoupper(substr($parts[1], 0, 1)) . '.'
              : ($parts[0] ?: 'User');
          $initial     = strtoupper(substr($fullName ?: 'U', 0, 1));
          $avatarClass = $avatarPalette[abs((int)($customer?->id ?? 0)) % count($avatarPalette)];

          // Area → primary customer address area name
          $addresses   = $customer?->customerAddresses ?? collect();
          $primaryAddr = $addresses->firstWhere('is_primary', true) ?? $addresses->first();
          $areaName    = $primaryAddr?->area?->name ?? '';

          // Service name from translations JSON: { "en": { "name": "..." }, "bn": {...} }
          $svcTrans    = $obj->service ? $obj->service->getTranslation('translations', $locale) : [];
          $serviceName = $svcTrans['name'] ?? '';
          if (!$serviceName) {
              $svcTrans    = $obj->service ? $obj->service->getTranslation('translations', 'en') : [];
              $serviceName = $svcTrans['name'] ?? '';
          }
        @endphp

        <div class="flex items-center gap-2 mx-2.5 px-3 py-1.5 rounded-full border border-slate-200 bg-white shrink-0 cursor-default hover:border-slate-300 hover:shadow-sm transition-shadow">

          {{-- Avatar --}}
          <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 {{ $avatarClass }}">{{ $initial }}</span>

          {{-- Name --}}
          <span class="text-xs font-semibold text-slate-800 whitespace-nowrap">{{ $shortName }}</span>

          {{-- Area --}}
          @if($areaName)
            <span class="text-slate-300 text-xs shrink-0">·</span>
            <span class="text-[11px] text-slate-500 whitespace-nowrap">{{ $areaName }}</span>
          @endif

          {{-- Service --}}
          @if($serviceName)
            <span class="text-slate-300 text-xs shrink-0">·</span>
            <span class="text-[11px] font-medium text-slate-600 whitespace-nowrap">{{ $serviceName }}</span>
          @endif

          {{-- Time ago --}}
          <span class="text-slate-300 text-xs shrink-0">·</span>
          <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $obj->created_at->diffForHumans(null, true) }}</span>

        </div>

        <span class="w-1 h-1 rounded-full bg-slate-200 shrink-0 mx-0.5"></span>

      @endforeach
    @endforeach
  </div>

</div>
@endif

{{-- Trust Strip --}}
<section class="bg-primary-50 border-y border-primary-100">
  <div class="max-w-7xl mx-auto px-4 lg:px-6 py-6 grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-white text-primary-500 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div><p class="text-lg sm:text-xl font-bold text-slate-900 leading-none">{{ number_format($stats['providers']) }}+</p><p class="text-xs text-slate-600 mt-1">{{ __('web.trust.providers') }}</p></div>
    </div>
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-white text-primary-500 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      </div>
      <div><p class="text-lg sm:text-xl font-bold text-slate-900 leading-none">{{ $stats['cities'] }}+</p><p class="text-xs text-slate-600 mt-1">{{ __('web.trust.cities') }}</p></div>
    </div>
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-white text-primary-500 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div><p class="text-lg sm:text-xl font-bold text-slate-900 leading-none">{{ $stats['verified_rate'] }}%</p><p class="text-xs text-slate-600 mt-1">{{ __('web.trust.verified_rate') }}</p></div>
    </div>
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-white text-accent-500 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <div><p class="text-lg sm:text-xl font-bold text-slate-900 leading-none">{{ $stats['avg_rating'] }}★</p><p class="text-xs text-slate-600 mt-1">{{ __('web.trust.avg_rating') }}</p></div>
    </div>
  </div>
</section>

{{-- Popular Categories --}}
<section class="py-12 lg:py-16">
  <div class="max-w-7xl mx-auto px-4 lg:px-6">
    <div class="flex items-end justify-between mb-8 gap-4">
      <div>
        <span class="block text-xs font-medium uppercase tracking-wider text-primary-600 mb-1">{{ __('web.categories.label') }}</span>
        <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ __('web.categories.heading') }}</h2>
      </div>
      <a href="{{ route('categories') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('web.categories.view_all') }} <span class="rtl-flip">→</span></a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
      @forelse($categories as $category)
        @php $catName = $category->getTranslation('translations', app()->getLocale()) ?: ($category->getTranslation('translations', 'en') ?: $category->slug); @endphp
        <a href="{{ route('providers', ['category' => $category->id]) }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md hover:border-primary-200 transition relative overflow-hidden">
          @if($category->icon)
            <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center overflow-hidden group-hover:bg-primary-100 transition">
              <img src="{{ asset('storage/'.$category->icon) }}" class="w-8 h-8 object-contain">
            </div>
          @else
            <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-2xl group-hover:bg-primary-100 transition">🔧</div>
          @endif
          <h3 class="mt-4 font-semibold text-slate-900 text-sm group-hover:text-primary-700 transition">{{ $catName }}</h3>
          <p class="mt-1 text-xs text-slate-500">{{ $category->services_count }} {{ __('web.categories.services') }}</p>
          <span class="absolute bottom-0 start-0 end-0 h-0.5 bg-accent-500 scale-x-0 group-hover:scale-x-100 transition-transform origin-start"></span>
        </a>
      @empty
        <p class="col-span-4 text-slate-400 text-sm py-8 text-center">No categories available yet.</p>
      @endforelse
    </div>
    <div class="sm:hidden mt-6 text-center">
      <a href="{{ route('categories') }}" class="inline-flex items-center gap-1 text-sm font-medium text-primary-600">{{ __('web.categories.view_all') }} <span class="rtl-flip">→</span></a>
    </div>
  </div>
</section>

{{-- Latest Providers --}}
@if($latestProviders->count())
<section class="py-12 lg:py-16 bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 lg:px-6">
    <div class="flex items-end justify-between mb-8 gap-4">
        <div>
          <span class="block text-xs font-medium uppercase tracking-wider text-primary-600 mb-1">{{ __('website/home.latest.label') }}</span>
          <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ __('website/home.latest.heading') }}</h2>
        </div>
        <a href="{{ route('providers', ['sort' => 'newest']) }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('website/home.latest.browse') }} <span class="rtl-flip">→</span></a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      @foreach($latestProviders as $profile)
        @include('website.partials.provider-card', [
          'profile' => $profile
        ])
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Featured Providers --}}
@if($featuredProviders->count())
<section class="py-12 lg:py-16">
  <div class="max-w-7xl mx-auto px-4 lg:px-6">
    <div class="flex items-end justify-between mb-8 gap-4">
      <div>
        <span class="block text-xs font-medium uppercase tracking-wider text-accent-600 mb-1">{{ __('web.providers.label') }}</span>
        <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ __('web.providers.heading') }}</h2>
      </div>
      <a href="{{ route('providers') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('web.providers.browse_all') }} <span class="rtl-flip">→</span></a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      @foreach($featuredProviders as $profile)
        @include('website.partials.provider-card', [
          'profile' => $profile
        ])
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Freelancer Providers --}}
@if($topFreelancers->count())
<section class="py-12 lg:py-16 bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 lg:px-6">
    <div class="flex items-end justify-between mb-8 gap-4">
        <div>
          <span class="block text-xs font-medium uppercase tracking-wider text-primary-600 mb-1">{{ __('website/home.freelancers.label') }}</span>
          <h2 class="text-2xl md:text-3xl font-bold text-slate-900 flex items-center gap-2.5">
            <img src="{{ asset('freelancer.png') }}" class="w-8 h-8 md:w-9 md:h-9 object-contain" alt="{{ __('website/home.freelancers.label') }}">
            <span>{{ __('website/home.freelancers.heading') }}</span>
          </h2>
        </div>
        <a href="{{ route('providers', ['type' => 'freelancer']) }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('website/home.freelancers.browse') }} <span class="rtl-flip">→</span></a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      @foreach($topFreelancers as $profile)
        @include('website.partials.provider-card', [
          'profile' => $profile
        ])
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- Business Providers --}}
@if($topBusinesses->count())
<section class="py-12 lg:py-16">
  <div class="max-w-7xl mx-auto px-4 lg:px-6">
    <div class="flex items-end justify-between mb-8 gap-4">
        <div>
          <span class="block text-xs font-medium uppercase tracking-wider text-accent-600 mb-1">{{ __('website/home.businesses.label') }}</span>
          <h2 class="text-2xl md:text-3xl font-bold text-slate-900 flex items-center gap-2.5">
            <img src="{{ asset('business.png') }}" class="w-8 h-8 md:w-9 md:h-9 object-contain" alt="{{ __('website/home.businesses.label') }}">
            <span>{{ __('website/home.businesses.heading') }}</span>
          </h2>
        </div>
        <a href="{{ route('providers', ['type' => 'business']) }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('website/home.businesses.browse') }} <span class="rtl-flip">→</span></a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      @foreach($topBusinesses as $profile)
        @include('website.partials.provider-card', [
          'profile' => $profile
        ])
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- How It Works --}}
<section class="py-12 lg:py-16 bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 lg:px-6">
    <div class="max-w-2xl mb-10 lg:mb-12">
      <span class="block text-xs font-medium uppercase tracking-wider text-primary-600 mb-1">{{ __('web.how_it_works.label') }}</span>
      <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ __('web.how_it_works.heading') }}</h2>
      <p class="mt-3 text-slate-500">{{ __('web.how_it_works.subtext') }}</p>
    </div>
    <div class="grid md:grid-cols-3 gap-6 lg:gap-8 relative">
      <div class="hidden md:block absolute top-7 start-[16.67%] end-[16.67%] h-px bg-gradient-to-r from-primary-200 via-accent-200 to-primary-200" aria-hidden="true"></div>
      <div class="relative">
        <div class="w-14 h-14 rounded-full bg-white border-2 border-primary-200 text-primary-600 font-bold text-xl flex items-center justify-center shadow-soft relative z-10">1</div>
        <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ __('web.how_it_works.step1_title') }}</h3>
        <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ __('web.how_it_works.step1_desc') }}</p>
      </div>
      <div class="relative">
        <div class="w-14 h-14 rounded-full bg-white border-2 border-accent-200 text-accent-600 font-bold text-xl flex items-center justify-center shadow-warm relative z-10">2</div>
        <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ __('web.how_it_works.step2_title') }}</h3>
        <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ __('web.how_it_works.step2_desc') }}</p>
      </div>
      <div class="relative">
        <div class="w-14 h-14 rounded-full bg-white border-2 border-primary-200 text-primary-600 font-bold text-xl flex items-center justify-center shadow-soft relative z-10">3</div>
        <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ __('web.how_it_works.step3_title') }}</h3>
        <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ __('web.how_it_works.step3_desc') }}</p>
      </div>
    </div>
  </div>
</section>

{{-- Become Provider CTA --}}
<section id="become-provider" class="py-12 lg:py-16 bg-accent-50">
  <div class="max-w-7xl mx-auto px-4 lg:px-6 grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
    <div>
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white text-accent-700 text-xs font-medium ring-1 ring-accent-200">
        <span class="w-1.5 h-1.5 rounded-full bg-accent-500"></span>
        {{ __('web.cta.label') }}
      </span>
      <h2 class="mt-4 text-2xl md:text-3xl lg:text-4xl font-bold text-slate-900 leading-tight">
        {!! __('web.cta.heading', ['span' => '<span class="text-accent-600">', 'end' => '</span>']) !!}
      </h2>
      <p class="mt-4 text-slate-600 max-w-lg">{{ __('web.cta.subtext') }}</p>
      <ul class="mt-6 space-y-2.5">
        @foreach(['benefit1','benefit2','benefit3','benefit4'] as $b)
        <li class="flex items-start gap-2.5 text-sm text-slate-700">
          <span class="mt-0.5 w-5 h-5 rounded-full bg-accent-500 text-white flex items-center justify-center shrink-0">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
          {{ __('web.cta.'.$b) }}
        </li>
        @endforeach
      </ul>
      <div class="mt-7 flex flex-wrap gap-3">
        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-accent-500 text-white font-medium hover:bg-accent-600 transition shadow-warm">{{ __('web.cta.start_trial') }} <span class="rtl-flip">→</span></a>
        <a href="{{ route('pricing') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-white border border-slate-300 text-slate-700 font-medium hover:bg-slate-50">{{ __('web.cta.see_pricing') }}</a>
      </div>
    </div>

    {{-- Plan preview card --}}
    <div class="relative">
      <div class="absolute -top-3 -end-3 w-24 h-24 rounded-full bg-accent-200/40 blur-2xl"></div>
      <div class="absolute -bottom-3 -start-3 w-24 h-24 rounded-full bg-primary-200/40 blur-2xl"></div>
      <div class="relative bg-white rounded-2xl shadow-xl border border-slate-200 p-6 lg:p-7">
        @if($featuredPlan)
        <div class="flex items-center justify-between mb-5">
          <div>
            <p class="text-xs font-medium uppercase tracking-wide text-primary-600">{{ __('web.pricing.most_popular') }}</p>
            <h3 class="text-xl font-bold text-slate-900 mt-1">{{ $featuredPlan->name }}</h3>
          </div>
          @if($featuredPlan->discount_percent)
            <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-accent-100 text-accent-700 ring-1 ring-accent-200">SAVE {{ $featuredPlan->discount_percent }}%</span>
          @endif
        </div>
        <div class="flex items-baseline gap-1 mb-5">
          <span class="text-4xl font-bold text-slate-900">{{ $featuredPlan->currency?->symbol ?? '৳' }} {{ number_format($featuredPlan->price) }}</span>
          <span class="text-sm text-slate-500">{{ __('web.pricing.per_month') }}</span>
        </div>
        <ul class="space-y-2.5 text-sm text-slate-700 border-t border-slate-100 pt-5">
          <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ $featuredPlan->lead_limit ? __('web.pricing.leads_limit', ['n' => $featuredPlan->lead_limit]) : __('web.pricing.leads_unlimited') }}
          </li>
          <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ __('web.pricing.areas_limit', ['n' => $featuredPlan->service_area_limit]) }}
          </li>
          @if($featuredPlan->is_featured)
          <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('web.pricing.featured') }}</li>
          @endif
          @if($featuredPlan->is_verified_badge_included)
          <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('web.pricing.badge') }}</li>
          @endif
        </ul>
        @else
        <p class="text-xs font-medium uppercase tracking-wide text-primary-600">{{ __('web.pricing.most_popular') }}</p>
        <h3 class="text-xl font-bold text-slate-900 mt-1">{{ __('website/home.fallback_plan.name') }}</h3>
        <ul class="mt-5 space-y-2.5 text-sm text-slate-700 border-t border-slate-100 pt-5">
          <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('website/home.fallback_plan.unlimited_leads') }}</li>
          <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('website/home.fallback_plan.service_zones') }}</li>
          <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('website/home.fallback_plan.featured_verified') }}</li>
          <li class="flex items-center gap-2"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('website/home.fallback_plan.priority_support') }}</li>
        </ul>
        @endif
      </div>
    </div>
  </div>
</section>

{{-- Testimonials --}}
<section class="py-12 lg:py-16">
  <div class="max-w-7xl mx-auto px-4 lg:px-6">
    <div class="max-w-2xl mb-10">
      <span class="block text-xs font-medium uppercase tracking-wider text-primary-600 mb-1">{{ __('web.testimonials.label') }}</span>
      <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ __('web.testimonials.heading') }}</h2>
    </div>
    <div class="grid md:grid-cols-3 gap-4 lg:gap-6">
      @foreach([
        [
          'q' => __('website/home.testimonials.item1.q', ['app' => config('app.name')]),
          'name' => __('website/home.testimonials.item1.name'),
          'loc' => __('website/home.testimonials.item1.loc'),
          'initials' => 'SM',
          'color' => 'primary'
        ],
        [
          'q' => __('website/home.testimonials.item2.q', ['app' => config('app.name')]),
          'name' => __('website/home.testimonials.item2.name'),
          'loc' => __('website/home.testimonials.item2.loc'),
          'initials' => 'KH',
          'color' => 'accent'
        ],
        [
          'q' => __('website/home.testimonials.item3.q', ['app' => config('app.name')]),
          'name' => __('website/home.testimonials.item3.name'),
          'loc' => __('website/home.testimonials.item3.loc'),
          'initials' => 'NJ',
          'color' => 'primary'
        ],
      ] as $t)
      <figure class="bg-white border border-slate-200 rounded-2xl p-5 lg:p-6 shadow-sm">
        <div class="flex gap-0.5 text-accent-500 text-base mb-3">★★★★★</div>
        <blockquote class="text-sm text-slate-700 leading-relaxed">{{ $t['q'] }}</blockquote>
        <figcaption class="mt-5 flex items-center gap-3 pt-4 border-t border-slate-100">
          <div class="w-10 h-10 rounded-full bg-{{ $t['color'] }}-100 text-{{ $t['color'] }}-700 font-semibold flex items-center justify-center text-sm">{{ $t['initials'] }}</div>
          <div>
            <p class="text-sm font-semibold text-slate-900">{{ $t['name'] }}</p>
            <p class="text-xs text-slate-500">{{ $t['loc'] }}</p>
          </div>
        </figcaption>
      </figure>
      @endforeach
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<script>
// Attempt geolocation for hero search location field
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(function(pos) {
    document.getElementById('hero-lat').value = pos.coords.latitude.toFixed(6);
    document.getElementById('hero-lng').value = pos.coords.longitude.toFixed(6);
  }, function(){});
}
</script>
@endpush
