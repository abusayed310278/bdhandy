@php
  // Default responsive values if none passed explicitly
  $logoSizeClass = (isset($logoSize) && $logoSize !== 'w-16 h-16') ? $logoSize : 'w-10 h-10 sm:w-16 sm:h-16';
  $logoMarginClass = (isset($logoMargin) && $logoMargin !== '-mt-12') ? $logoMargin : '-mt-8 sm:-mt-12';
  $logoTextSizeClass = (isset($logoTextSize) && $logoTextSize !== 'text-xl') ? $logoTextSize : 'text-sm sm:text-xl';

  $profileUrl = route('provider.profile.public', $profile->slug);
  $areaLabel = $profile->area_label
            ?? ($profile->serviceAreas->first()?->area?->name
            ?? ($profile->serviceAreas->first()?->district?->name ?? null));

  $isSaved = false;
  if (auth()->check()) {
      $isSaved = auth()->user()->savedProviders()->where('provider_id', $profile->user_id)->exists();
  }
@endphp

<article class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 {{ $profile->is_featured ? 'ring-1 ring-primary-200' : '' }} relative group">
  {{-- Cover photo --}}
  <a href="{{ $profileUrl }}" class="block relative w-full aspect-[1600/890] overflow-hidden bg-gradient-to-br from-primary-100 to-primary-200">
    @if($profile->cover_photo)
      <img src="{{ asset('storage/'.$profile->cover_photo) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
    @else
      <img src="{{ asset('images/default.jpg') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
    @endif

    {{-- Bottom right: Year and Level --}}
    @if($profile->years_of_experience || $profile->experience_level)
      <div class="absolute bottom-2 end-2 flex gap-1 items-center z-10">
        @if($profile->years_of_experience)
          <span class="px-1 py-0.5 sm:px-1.5 sm:py-0.5 rounded bg-black/60 text-white text-[8px] sm:text-[10px] font-medium backdrop-blur-xs">
            💼 {{ __('web.providers.yrs_exp', ['n' => $profile->years_of_experience]) }}
          </span>
        @endif
        @if($profile->experience_level)
          <span class="px-1 py-0.5 sm:px-1.5 sm:py-0.5 rounded bg-black/60 text-white text-[8px] sm:text-[10px] font-medium backdrop-blur-xs">
            ⭐ {{ ucfirst($profile->experience_level) }}
          </span>
        @endif
      </div>
    @endif
  </a>

  {{-- Top left: Featured & Emergency Badges --}}
  <div class="absolute top-2 start-2 flex flex-col gap-1 items-start z-20 pointer-events-none">
    @if($profile->is_featured)
      <span class="px-1.5 py-0.5 sm:px-2 sm:py-0.5 rounded-full text-[8px] sm:text-[10px] font-semibold bg-accent-500 text-white shadow-sm pointer-events-auto">⭐ {{ __('web.providers.featured') }}</span>
    @endif
    @if($profile->emergency_available)
      <span class="px-1 py-0.5 sm:px-1.5 sm:py-0.5 rounded-full text-[8px] sm:text-[10px] font-semibold bg-red-500 text-white shadow-sm pointer-events-auto">⚡ {{ __('website/provider_card.emergency') }}</span>
    @endif
  </div>

  {{-- Top right: Heart Icon for Saved Provider --}}
  <button 
    x-data="{ 
      saved: {{ $isSaved ? 'true' : 'false' }}, 
      loading: false,
      toggleSave() {
        if (!{{ auth()->check() ? 'true' : 'false' }}) {
          if (typeof PNotify !== 'undefined') {
            PNotify.error({
              title: '{{ addslashes(__('website/provider_card.login_required')) }}',
              text: '{{ addslashes(__('website/provider_card.login_required_desc')) }}',
              delay: 3000,
              styling: 'brighttheme'
            });
          }
          return;
        }
        
        this.loading = true;
        fetch('{{ auth()->check() ? route('customer.saved.toggle', $profile) : '#' }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => {
          if (response.status === 403) {
            throw new Error('Only customer accounts can save providers.');
          }
          if (!response.ok) {
            throw new Error('Something went wrong.');
          }
          return response.json();
        })
        .then(data => {
          this.saved = data.saved;
          if (typeof PNotify !== 'undefined') {
            PNotify.success({
              title: this.saved ? '{{ addslashes(__('website/provider_card.saved')) }}' : '{{ addslashes(__('website/provider_card.removed')) }}',
              text: data.message,
              delay: 2000,
              styling: 'brighttheme'
            });
          }
        })
        .catch(error => {
          if (typeof PNotify !== 'undefined') {
            PNotify.error({
              title: '{{ addslashes(__('website/provider_card.error')) }}',
              text: error.message || '{{ addslashes(__('website/provider_card.unable_to_save')) }}',
              delay: 3000,
              styling: 'brighttheme'
            });
          }
        })
        .finally(() => {
          this.loading = false;
        });
      }
    }"
    @click.prevent="toggleSave()"
    class="absolute top-2 end-2 w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full bg-white/95 hover:bg-white shadow-md transition-all duration-200 z-20 hover:scale-110"
    :class="loading ? 'opacity-50 pointer-events-none' : ''"
    aria-label="Save provider"
  >
    <i 
      class="transition-colors duration-200 text-xs sm:text-base" 
      :class="saved ? 'fa-solid fa-heart text-red-500' : 'fa-regular fa-heart text-slate-500 hover:text-red-500'"
    ></i>
  </button>

  {{-- Logo --}}
  <div class="relative z-10 px-3 sm:px-4 {{ $logoMarginClass }} flex items-end">
    <a href="{{ $profileUrl }}" class="relative inline-block group/logo">
      @if($profile->logo)
        <img src="{{ asset('storage/'.$profile->logo) }}" class="{{ $logoSizeClass }} rounded-xl border-2 border-white shadow-md object-cover">
      @else
        <div class="{{ $logoSizeClass }} rounded-xl border-2 border-white shadow-md bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold {{ $logoTextSizeClass }}">
          {{ strtoupper(substr($profile->business_name ?? 'P', 0, 1)) }}
        </div>
      @endif

      @php
        $role = $profile->provider_type ?? 'freelancer';
        $badgeImage = $role === 'business' ? 'business.png' : 'freelancer.png';
      @endphp
      <img src="{{ asset($badgeImage) }}" class="absolute -top-1 -end-1 w-4 h-4 sm:w-6 sm:h-6 object-contain rounded-full bg-white border border-slate-100 p-0.5 shadow-sm" alt="{{ __('layout/dashboard.roles.' . $role) }}" title="{{ __('layout/dashboard.roles.' . $role) }}">
    </a>
  </div>

  {{-- Content --}}
  <div class="px-3 sm:px-4 pt-1.5 sm:pt-2 pb-3 sm:pb-4">
    <div class="flex items-center gap-1.5 min-w-0">
      <a href="{{ $profileUrl }}" class="font-bold text-xs sm:text-sm md:text-base text-slate-900 hover:text-primary-600 transition truncate leading-tight">
        {{ $profile->business_name }}
      </a>
      @if($profile->hasVerifiedBadge())
        <span class="inline-flex items-center justify-center shrink-0 w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full bg-green-500 text-white" title="{{ __('web.providers.verified') }}">
          <svg class="w-2 sm:w-2.5 h-2 sm:h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </span>
      @endif
    </div>
    @if($profile->tagline)
      <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5 truncate">{{ Str::limit($profile->tagline, 50) }}</p>
    @endif

    {{-- Description in one line --}}
    @if($profile->description)
      <p class="text-[10px] sm:text-xs text-slate-500 mt-1 sm:mt-1.5 truncate line-clamp-1" title="{{ strip_tags($profile->description) }}">{{ strip_tags($profile->description) }}</p>
    @endif
    
    {{-- Re-arranged responsive grid rows --}}
    <div class="mt-2 flex flex-col gap-1.5 text-[10px] sm:text-xs text-slate-500 border-t border-slate-100/80 pt-2">
      <div class="flex items-center justify-between gap-2">
        <span class="flex items-center gap-0.5 font-semibold text-slate-700">
          <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-accent-400 fill-current" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          {{ number_format($profile->avg_rating ?? 0.0, 1) }}
          <span class="font-normal text-slate-400">({{ $profile->total_reviews ?? 0 }})</span>
        </span>
        @if($priceRange = $profile->getPriceRange())
          <span class="font-bold text-slate-800 shrink-0">{{ $priceRange }}</span>
        @endif
      </div>

      @if($areaLabel)
        <div class="flex items-center gap-0.5 min-w-0">
          <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span class="truncate">{{ $areaLabel }}</span>
          @if(isset($profile->distance_km) && $profile->distance_km !== null)
            <span class="text-slate-400 shrink-0">· {{ $profile->distance_km }} km</span>
          @endif
        </div>
      @endif
    </div>

    @if($profile->services->count())
      <div class="mt-2.5 flex flex-wrap gap-1">
        @foreach($profile->services->take(3) as $ps)
          <span class="px-1.5 py-0.5 sm:px-2 rounded-md text-[9px] sm:text-[11px] bg-slate-100 text-slate-600 truncate max-w-[85px] sm:max-w-[120px]" title="{{ data_get($ps->service->getTranslation('translations', app()->getLocale()), 'name') }}">
            {{ data_get($ps->service->getTranslation('translations', app()->getLocale()), 'name') ?? (data_get($ps->service->getTranslation('translations', 'en'), 'name') ?? $ps->service->slug) }}
          </span>
        @endforeach
      </div>
    @endif

    <a href="{{ $profileUrl }}" class="mt-3 flex items-center justify-center gap-1 w-full py-1.5 sm:py-2 rounded-lg bg-primary-50 text-primary-700 text-[10px] sm:text-xs font-semibold hover:bg-primary-100 transition">
      {{ __('web.providers.view') }} <span class="rtl-flip">→</span>
    </a>
  </div>
</article>
