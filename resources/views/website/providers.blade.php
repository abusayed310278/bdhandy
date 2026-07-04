@extends('layouts.website')
@section('title', __('web.nav.find_providers') . ' — ' . config('app.name'))
@section('meta_description', __('website/providers.meta_description'))

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">

  {{-- Page header --}}
  <div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900">{{ __('web.nav.find_providers') }}</h1>
    <p class="mt-1 text-sm text-slate-500">
      {{ trans_choice('website/providers.providers_found', $providers->total(), ['count' => number_format($providers->total())]) }}
    </p>
  </div>

  <div class="flex gap-6 items-start">

    {{-- Filter sidebar --}}
    <aside class="w-64 shrink-0 hidden lg:block sticky top-24 bg-white rounded-2xl border border-slate-200 p-5 space-y-5">
      <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-900">{{ __('web.providers.filter_heading') }}</h2>
        <a href="{{ route('providers') }}" class="text-xs text-slate-400 hover:text-primary-600">{{ __('web.providers.clear') }}</a>
      </div>

      <form id="filter-form" method="GET" action="{{ route('providers') }}" class="space-y-4">
        {{-- Preserve search query --}}
        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
        @if(request('lat'))<input type="hidden" name="lat" value="{{ request('lat') }}">@endif
        @if(request('lng'))<input type="hidden" name="lng" value="{{ request('lng') }}">@endif

        {{-- Category --}}
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('web.providers.filter_category') }}</label>
          <select name="category" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none">
            <option value="">{{ __('web.providers.any') }}</option>
            @foreach($categories as $cat)
              @php $cname = $cat->getTranslation('translations', app()->getLocale()) ?: ($cat->getTranslation('translations','en') ?: $cat->slug); @endphp
              <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cname }}</option>
            @endforeach
          </select>
        </div>

        {{-- Min rating --}}
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('web.providers.filter_rating') }}</label>
          <select name="rating" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none">
            <option value="">{{ __('web.providers.any') }}</option>
            @foreach([4.5, 4.0, 3.5, 3.0] as $r)
              <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }}★ &amp; up</option>
            @endforeach
          </select>
        </div>

        {{-- Provider type --}}
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('web.providers.filter_type') }}</label>
          <select name="type" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none">
            <option value="">{{ __('web.providers.any') }}</option>
            <option value="freelancer" {{ request('type') === 'freelancer' ? 'selected' : '' }}>{{ __('web.providers.freelancer') }}</option>
            <option value="business" {{ request('type') === 'business' ? 'selected' : '' }}>{{ __('web.providers.business') }}</option>
          </select>
        </div>

        {{-- Checkboxes --}}
        <div class="space-y-2.5">
          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-primary-500 focus:ring-primary-400">
            <span class="text-sm text-slate-700">{{ __('web.providers.filter_verified') }}</span>
          </label>
          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" name="emergency" value="1" {{ request('emergency') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-primary-500 focus:ring-primary-400">
            <span class="text-sm text-slate-700">{{ __('web.providers.filter_emergency') }}</span>
          </label>
        </div>

        {{-- Location detect --}}
        <div>
          <button type="button" id="detect-location" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 hover:bg-slate-50 hover:border-primary-300 transition">
            <svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span id="detect-label">{{ __('website/providers.use_my_location') }}</span>
          </button>
          <input type="hidden" name="lat" id="filter-lat" value="{{ request('lat') }}">
          <input type="hidden" name="lng" id="filter-lng" value="{{ request('lng') }}">
        </div>

        <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition">
          {{ __('web.providers.apply_filters') }}
        </button>
      </form>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 min-w-0">

      {{-- Top bar: search + sort --}}
      <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <form method="GET" action="{{ route('providers') }}" class="flex-1 flex gap-2">
          @foreach(request()->except('q') as $k => $v)
            @if($v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
          @endforeach
          <div class="flex-1 flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2 focus-within:ring-2 focus-within:ring-primary-100 focus-within:border-primary-400">
            <svg class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('web.hero.service_ph') }}" class="flex-1 text-sm bg-transparent border-0 p-0 focus:ring-0 focus:outline-none text-slate-900 placeholder-slate-400">
          </div>
          <button type="submit" class="px-4 py-2 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition">{{ __('web.hero.search') }}</button>
        </form>
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-xs text-slate-500">{{ __('web.providers.sort_label') }}:</span>
          <select onchange="this.form.submit()" form="filter-form" name="sort" class="text-sm rounded-lg border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none bg-white">
            <option value="rating" {{ request('sort','rating') === 'rating' ? 'selected' : '' }}>{{ __('web.providers.sort_rating') }}</option>
            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>{{ __('web.providers.sort_newest') }}</option>
          </select>
        </div>
      </div>

      {{-- Active filters chips --}}
      @if(request()->hasAny(['category','rating','verified','emergency','type','q']))
      <div class="flex flex-wrap gap-2 mb-4">
        @if(request('q'))<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 text-xs font-medium">{{ __('website/providers.search_query', ['query' => request('q')]) }}<a href="{{ request()->fullUrlWithoutQuery('q') }}" class="hover:text-primary-900">✕</a></span>@endif
        @if(request('category'))<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 text-xs font-medium">{{ __('website/providers.category_label') }}<a href="{{ request()->fullUrlWithoutQuery('category') }}" class="hover:text-primary-900 ms-1">✕</a></span>@endif
        @if(request('rating'))<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 text-xs font-medium">{{ request('rating') }}★+<a href="{{ request()->fullUrlWithoutQuery('rating') }}" class="hover:text-primary-900 ms-1">✕</a></span>@endif
        @if(request('verified'))<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium">{{ __('website/providers.verified_label') }}<a href="{{ request()->fullUrlWithoutQuery('verified') }}" class="hover:text-green-900 ms-1">✕</a></span>@endif
        @if(request('emergency'))<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-medium">{{ __('website/providers.emergency_label') }}<a href="{{ request()->fullUrlWithoutQuery('emergency') }}" class="hover:text-red-900 ms-1">✕</a></span>@endif
      </div>
      @endif

      {{-- Provider grid --}}
      @if($providers->count())
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($providers as $profile)
          @include('website.partials.provider-card', ['profile' => $profile])
        @endforeach
      </div>

      {{-- Pagination --}}
      @if($providers->hasPages())
      <div class="mt-8">{{ $providers->links() }}</div>
      @endif

      @else
      <div class="text-center py-20 bg-white rounded-2xl border border-slate-200">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4 text-2xl">🔍</div>
        <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/providers.no_providers_found') }}</h3>
        <p class="text-sm text-slate-500 mb-5">{{ __('web.providers.no_results') }}</p>
        <a href="{{ route('providers') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600">{{ __('website/providers.clear_filters') }}</a>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- Mobile filter sheet trigger --}}
<div class="lg:hidden fixed bottom-5 start-1/2 -translate-x-1/2 z-30">
  <button onclick="document.getElementById('mobile-filter').classList.remove('translate-y-full')" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-slate-900 text-white text-sm font-medium shadow-xl">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
    {{ __('website/providers.filters_button') }}
  </button>
</div>

{{-- Mobile filter sheet --}}
<div id="mobile-filter" class="lg:hidden fixed inset-x-0 bottom-0 z-50 transform translate-y-full transition-transform duration-300">
  <div onclick="document.getElementById('mobile-filter').classList.add('translate-y-full')" class="absolute inset-0 -top-96 bg-slate-900/50"></div>
  <div class="relative bg-white rounded-t-2xl p-5 max-h-[85vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-semibold text-slate-900">{{ __('web.providers.filter_heading') }}</h2>
      <button onclick="document.getElementById('mobile-filter').classList.add('translate-y-full')" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form method="GET" action="{{ route('providers') }}" class="space-y-4">
      @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('web.providers.filter_category') }}</label>
        <select name="category" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:outline-none">
          <option value="">{{ __('web.providers.any') }}</option>
          @foreach($categories as $cat)
            @php $cname = $cat->getTranslation('translations', app()->getLocale()) ?: ($cat->getTranslation('translations','en') ?: $cat->slug); @endphp
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cname }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('web.providers.filter_rating') }}</label>
        <select name="rating" class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2.5 focus:outline-none">
          <option value="">{{ __('web.providers.any') }}</option>
          @foreach([4.5,4.0,3.5,3.0] as $r)
            <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }}★ &amp; up</option>
          @endforeach
        </select>
      </div>
      <div class="space-y-2.5">
        <label class="flex items-center gap-2.5"><input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }} class="w-4 h-4 rounded"><span class="text-sm">{{ __('web.providers.filter_verified') }}</span></label>
        <label class="flex items-center gap-2.5"><input type="checkbox" name="emergency" value="1" {{ request('emergency') ? 'checked' : '' }} class="w-4 h-4 rounded"><span class="text-sm">{{ __('web.providers.filter_emergency') }}</span></label>
      </div>
      <button type="submit" class="w-full px-4 py-3 rounded-xl bg-primary-500 text-white font-medium hover:bg-primary-600">{{ __('web.providers.apply_filters') }}</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('detect-location')?.addEventListener('click', function() {
  const label = document.getElementById('detect-label');
  label.textContent = '{{ __('website/providers.detecting') }}';
  navigator.geolocation.getCurrentPosition(function(pos) {
    document.getElementById('filter-lat').value = pos.coords.latitude.toFixed(6);
    document.getElementById('filter-lng').value = pos.coords.longitude.toFixed(6);
    label.textContent = '{{ __('website/providers.location_set') }}';
    document.getElementById('filter-form').submit();
  }, function() {
    label.textContent = '{{ __('website/providers.unable_to_detect') }}';
  });
});
</script>
@endpush
