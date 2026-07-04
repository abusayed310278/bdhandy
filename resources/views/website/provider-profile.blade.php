@extends('layouts.website')
@section('title', __('website/provider_profile.title', ['business_name' => $profile->business_name, 'app' => config('app.name')]))
@section('meta_description', Str::limit(strip_tags($profile->description ?? $profile->tagline ?? $profile->business_name), 160))

@php
  $locale   = app()->getLocale();
  $avgRating = $profile->avg_rating;
  $reviewCount = $profile->total_reviews;

  $todayName = now()->format('l'); // e.g. "Monday"
  $todayHour = $profile->businessHours
    ->first(fn($h) => strtolower($h->dayOfWeek?->getTranslation('translations','en') ?? '') === strtolower($todayName));

  function ytId($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url ?? '', $m);
    return $m[1] ?? null;
  }
  function isYt($url) { return str_contains($url ?? '', 'youtube') || str_contains($url ?? '', 'youtu.be'); }

  $photos = $profile->gallery->where('is_video', false);
  $videos = $profile->gallery->where('is_video', true);

  $firstServiceArea = $profile->serviceAreas->first();
@endphp

@php
  $providerServicesForJs = $profile->services->map(fn($ps) => [
      'id'    => $ps->id,
      'title' => $ps->title,
  ])->values();

  $customerAddressesForJs = $customerAddresses->map(fn($a) => [
      'id'           => $a->id,
      'label'        => $a->label ?: ucfirst($a->address_type),
      'address_type' => $a->address_type,
      'address'      => $a->address,
      'latitude'     => $a->latitude,
      'longitude'    => $a->longitude,
      'is_primary'   => $a->is_primary,
  ])->values();
@endphp

<script>
  window._providerServices    = @json($providerServicesForJs);
  window._customerAddresses   = @json($customerAddressesForJs);
</script>

@section('content')
<div x-data="{
    showContact: false,
    loadingContact: false,
    primaryPhone: '',
    whatsappNumber: '',
    website: '',
    facebookUrl: '',
    instagramUrl: '',
    youtubeUrl: '',
    showRequestModal: false,
    showMessageModal: false,
    showLoginModal: false,
    submitting: false,
    isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
    isCustomer: {{ (auth()->check() && auth()->user()->isCustomer()) ? 'true' : 'false' }},
    isProfileComplete: {{ ($profileComplete ?? false) ? 'true' : 'false' }},
    showProfileIncompleteModal: false,

    // Request modal — preselected service
    reqPreselectedServiceId: '',

    // Request modal — address
    reqAddresses: window._customerAddresses || [],
    reqSelectedAddressId: '',
    reqAddress: '',
    reqShowManualAddress: true,
    reqInitAddress() {
        const primary = this.reqAddresses.find(a => a.is_primary);
        if (primary) {
            this.reqSelectedAddressId = primary.id;
            this.reqPickAddress(primary.id);
        } else if (this.reqAddresses.length > 0) {
            this.reqSelectedAddressId = this.reqAddresses[0].id;
            this.reqPickAddress(this.reqAddresses[0].id);
        } else {
            this.reqSelectedAddressId = 'other';
            this.reqShowManualAddress = true;
        }
    },
    reqPickAddress(id) {
        if (id === 'other') {
            this.reqShowManualAddress = true;
            this.reqAddress = '';
        } else {
            const addr = this.reqAddresses.find(a => a.id == id);
            if (addr) {
                this.reqShowManualAddress = false;
                this.reqAddress = addr.address;
            }
        }
    },
    chatMessages: [],
    hasMoreChat: false,
    chatPage: 1,
    loadingChat: false,
    currentUserId: null,
    chatMessageText: '',
    openRequest(serviceId = '') {
        if (!this.isLoggedIn) { this.showLoginModal = true; return; }
        if (!this.isProfileComplete) { this.showProfileIncompleteModal = true; return; }
        this.reqPreselectedServiceId = serviceId ? String(serviceId) : '';
        this.showRequestModal = true;
        this.$nextTick(() => {
            this.reqInitAddress();
            if (serviceId) {
                const sel = document.getElementById('req_provider_service_select');
                if (sel) sel.value = String(serviceId);
            }
        });
    },
    openMessage() {
        if (!this.isLoggedIn) {
            this.showLoginModal = true;
            return;
        }
        if (!this.isProfileComplete) {
            this.showProfileIncompleteModal = true;
            return;
        }
        this.showMessageModal = true;
        this.loadChatMessages(1, false);
    },
    async loadChatMessages(page = 1, append = false) {
        this.loadingChat = true;
        try {
            const r = await fetch('{{ route('provider.profile.messages.get', $profile) }}?page=' + page);
            const d = await r.json();
            if (d.logged_in) {
                this.currentUserId = d.current_user_id;
                this.chatPage = d.current_page;
                this.hasMoreChat = d.has_more;
                if (append) {
                    this.chatMessages = [...d.messages, ...this.chatMessages];
                } else {
                    this.chatMessages = d.messages;
                    this.$nextTick(() => {
                        const container = document.getElementById('chat-messages-container');
                        if (container) container.scrollTop = container.scrollHeight;
                    });
                }
            }
        } catch(e) {
            console.error('Failed to load chat history', e);
        }
        this.loadingChat = false;
    },
    async submitRequest(form) {
        this.submitting = true;
        try {
            const r = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const d = await r.json();
            if (d.ok) {
                this.showRequestModal = false; form.reset();
                if (typeof PNotify !== 'undefined') PNotify.success({ title: '{{ __('website/provider_profile.modals.request.heading') }}', text: '{{ __('website/provider_profile.actions.copied') }}', delay: 4000, styling: 'brighttheme' });
            }
        } catch(e) {
            if (typeof PNotify !== 'undefined') PNotify.error({ title: '{{ __('website/provider_card.error') }}', text: '{{ __('website/provider_profile.save.error_unable') }}', delay: 3000, styling: 'brighttheme' });
        }
        this.submitting = false;
    },
    async submitMessage(form) {
        if (!this.chatMessageText.trim()) return;
        this.submitting = true;
        const tempId = Date.now();
        const optimisticMsg = {
            id: tempId,
            sender_id: this.currentUserId,
            message: this.chatMessageText,
            created_at: new Date().toISOString()
        };
        this.chatMessages.push(optimisticMsg);
        
        this.$nextTick(() => {
            const container = document.getElementById('chat-messages-container');
            if (container) container.scrollTop = container.scrollHeight;
        });

        const formData = new FormData(form);
        formData.set('message', this.chatMessageText);
        this.chatMessageText = '';

        try {
            const r = await fetch(form.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const d = await r.json();
            if (d.ok) {
                this.loadChatMessages(1, false);
            }
        } catch(e) {
            this.chatMessages = this.chatMessages.filter(m => m.id !== tempId);
            if (typeof PNotify !== 'undefined') PNotify.error({ title: '{{ __('website/provider_card.error') }}', text: '{{ __('website/provider_profile.save.error_unable') }}', delay: 3000, styling: 'brighttheme' });
        }
        this.submitting = false;
    },
    viewContactDetails() {
        if (this.showContact) return;
        this.loadingContact = true;
        fetch('{{ route('provider.profile.contact', $profile->slug) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                this.primaryPhone = data.primary_phone;
                this.whatsappNumber = data.whatsapp_number;
                this.website = data.website;
                this.facebookUrl = data.facebook_url;
                this.instagramUrl = data.instagram_url;
                this.youtubeUrl = data.youtube_url;
                this.showContact = true;
            }
        })
        .catch(err => console.error(err))
        .finally(() => { this.loadingContact = false; });
    }
}">
  {{-- ════════════════════════════════════════════════════════ HERO ════════════════════════════════════════════════════════ --}}
  @php $role = $profile->provider_type ?? 'freelancer'; $badgeImage = $role === 'business' ? 'business.png' : 'freelancer.png'; @endphp

  {{-- Mobile cover strip --}}
  <div class="lg:hidden relative overflow-hidden h-44 sm:h-52" x-data="{ shareOpen: false, copied: false }">
    @if($profile->cover_photo)
      <img src="{{ asset('storage/'.$profile->cover_photo) }}" class="w-full h-full object-cover object-top select-none" alt="{{ $profile->business_name }}">
    @else
      <div class="absolute inset-0 bg-gradient-to-tr from-primary-600 via-primary-500 to-accent-500"></div>
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/20 via-transparent to-transparent"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
    <div class="absolute top-3 right-3 z-10 flex items-center gap-2">
      <button @click="navigator.clipboard?.writeText('{{ url()->current() }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/60 hover:bg-slate-900/80 backdrop-blur-md text-white border border-white/20 text-xs font-bold transition shadow-sm select-none active:scale-95">
        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-5 10h6m-8-4h8"/></svg>
        <svg x-show="copied" class="w-3.5 h-3.5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
        <span x-text="copied ? '{{ __('website/provider_profile.actions.copied') }}' : '{{ __('website/provider_profile.actions.copy_link') }}'"></span>
      </button>
      <div class="relative">
        <button @click="shareOpen = !shareOpen" @click.away="shareOpen = false"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/60 hover:bg-slate-900/80 backdrop-blur-md text-white border border-white/20 text-xs font-bold transition shadow-sm select-none active:scale-95">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
          <span>{{ __('website/provider_profile.actions.share') }}</span>
        </button>
        <div x-show="shareOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 mt-2 w-48 rounded-2xl bg-white/95 backdrop-blur-lg border border-slate-200/80 p-1.5 shadow-xl z-30 space-y-0.5" style="display: none;">
          <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"><svg class="w-4 h-4 text-blue-600 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg> Facebook</a>
          <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($profile->business_name) }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"><svg class="w-4 h-4 text-slate-900 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg> Twitter / X</a>
          <a href="https://api.whatsapp.com/send?text={{ urlencode($profile->business_name . ' - ' . url()->current()) }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"><svg class="w-4 h-4 text-green-500 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> WhatsApp</a>
        </div>
      </div>
    </div>
  </div>

  {{-- Hero: split info-left / cover-right --}}
  <div class="bg-white border-b border-slate-200/70">
    <div class="max-w-6xl mx-auto px-4 lg:px-6">
      <div class="lg:grid lg:grid-cols-[1fr_400px] lg:gap-10 items-stretch">

        {{-- ─── LEFT: Provider Info ─── --}}
        <div class="pt-6 pb-6 lg:py-10 space-y-5">

          {{-- Logo + Name --}}
          <div class="flex items-start gap-4 sm:gap-5">
            <div class="relative shrink-0">
              @if($profile->logo)
                <img src="{{ asset('storage/'.$profile->logo) }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl border-[3px] border-white shadow-xl object-cover bg-white" alt="{{ $profile->business_name }}">
              @else
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl border-[3px] border-white shadow-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-2xl sm:text-3xl font-bold">
                  {{ strtoupper(substr($profile->business_name ?? 'P', 0, 1)) }}
                </div>
              @endif
              <img src="{{ asset($badgeImage) }}" class="absolute -top-1.5 -end-1.5 w-7 h-7 sm:w-8 sm:h-8 object-contain rounded-full bg-white border border-slate-100 p-0.5 shadow-md" alt="{{ $role }}" title="{{ ucfirst($role) }}">
            </div>
            <div class="flex-1 min-w-0 pt-1">
              @if($profile->languages && count($profile->languages))
                <div class="flex flex-wrap items-center gap-1 mb-2">
                  @foreach($profile->languages as $lang)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-50 text-primary-600 border border-primary-100 tracking-wide uppercase">{{ $lang }}</span>
                  @endforeach
                </div>
              @endif
              <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">{{ $profile->business_name }}</h1>
                @if($profile->hasVerifiedBadge())
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-green-500 text-white text-xs font-bold shadow-sm">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg> {{ __('website/provider_profile.verified') }}
                  </span>
                @endif
                @if($profile->emergency_available)
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-red-500 text-white text-xs font-bold shadow-sm">⚡ {{ __('website/provider_profile.emergency') }}</span>
                @endif
                <button
                  x-data="{
                    saved: {{ $isSaved ? 'true' : 'false' }},
                    loading: false,
                    toggleSave() {
                      if (!{{ auth()->check() ? 'true' : 'false' }}) {
                        if (typeof PNotify !== 'undefined') PNotify.error({ title: 'Login Required', text: 'You need to login to save providers.', delay: 3000, styling: 'brighttheme' });
                        return;
                      }
                      this.loading = true;
                      fetch('{{ auth()->check() ? route('customer.saved.toggle', $profile) : '#' }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                      })
                      .then(r => { if (r.status === 403) throw new Error('Only customer accounts can save providers.'); if (!r.ok) throw new Error('Something went wrong.'); return r.json(); })
                      .then(data => { this.saved = data.saved; if (typeof PNotify !== 'undefined') PNotify.success({ title: this.saved ? 'Saved' : 'Removed', text: data.message, delay: 2000, styling: 'brighttheme' }); })
                      .catch(error => { if (typeof PNotify !== 'undefined') PNotify.error({ title: '{{ __('website/provider_card.error') }}', text: error.message || '{{ __('website/provider_profile.save.error_unable') }}', delay: 3000, styling: 'brighttheme' }); })
                      .finally(() => { this.loading = false; });
                    }
                  }"
                  @click.prevent="toggleSave()"
                  class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-red-50 border border-slate-200 shadow-xs transition-all duration-200 hover:scale-110 cursor-pointer"
                  :class="loading ? 'opacity-50 pointer-events-none' : ''"
                  aria-label="Save provider">
                  <i class="transition-colors duration-200 text-sm" :class="saved ? 'fa-solid fa-heart text-red-500' : 'fa-regular fa-heart text-slate-400 hover:text-red-500'"></i>
                </button>
              </div>
              @if($profile->tagline)
                <p class="mt-1.5 text-sm text-slate-500 font-medium leading-relaxed">{{ $profile->tagline }}</p>
              @endif
            </div>
          </div>

          {{-- Meta stats row --}}
          <div class="flex flex-wrap items-center gap-x-5 gap-y-2.5">
            @if($avgRating)
              <div class="flex items-center gap-1.5">
                <div class="flex items-center gap-0.5">
                  @for($i=1; $i<=5; $i++)
                    <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-accent-400' : 'text-slate-200' }} fill-current" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  @endfor
                </div>
                <span class="font-bold text-slate-900 text-sm">{{ number_format($avgRating, 1) }}</span>
                <span class="text-slate-400 text-sm">({{ trans_choice('website/provider_profile.reviews.review_count', $reviewCount, ['count' => $reviewCount]) }})</span>
              </div>
            @else
              <span class="text-slate-400 text-xs font-semibold italic">{{ __('website/provider_profile.sections.no_reviews_yet') }}</span>
            @endif
            @if($firstServiceArea)
              <div class="flex items-center gap-1.5 text-slate-600 text-sm font-medium">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                {{ $firstServiceArea->area?->name ?? $firstServiceArea->district?->name ?? $firstServiceArea->division?->name ?? 'Bangladesh' }}
                @if($firstServiceArea->district)<span class="text-slate-400">· {{ $firstServiceArea->district->name }}</span>@endif
              </div>
            @endif
            @if($profile->years_of_experience)
              <div class="flex items-center gap-1.5 text-slate-600 text-sm font-medium">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('website/provider_profile.sections.years_experience', ['years' => $profile->years_of_experience]) }}
              </div>
            @endif
            @if($profile->experience_level)
              <span class="capitalize text-xs font-bold px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 border border-primary-100">{{ $profile->experience_level }}</span>
            @endif
            @if($todayHour)
              @if($todayHour->is_closed)
                <div class="flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                  <span class="text-xs font-bold text-red-600">{{ __('website/provider_profile.sections.closed_today') }}</span>
                </div>
              @else
                <div class="flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse shrink-0"></span>
                  <span class="text-xs font-bold text-green-600">{{ __('website/provider_profile.sections.open_today') }}</span>
                  <span class="text-slate-400 text-xs">{{ substr($todayHour->start_time ?? '09:00', 0, 5) }} – {{ substr($todayHour->end_time ?? '18:00', 0, 5) }}</span>
                </div>
              @endif
            @endif
          </div>

          {{-- Action buttons --}}
          <div class="flex flex-wrap gap-2.5">
            <template x-if="!showContact">
              <button @click="viewContactDetails()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold transition shadow-sm select-none">
                <svg x-show="loadingContact" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg x-show="!loadingContact" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span x-text="loadingContact ? '{{ __('website/provider_profile.contact.fetching') }}' : '{{ __('website/provider_profile.contact.show_info') }}'"></span>
              </button>
            </template>
            <button @click="openRequest()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-primary-50 hover:border-primary-200 text-slate-700 hover:text-primary-700 text-sm font-bold transition shadow-sm">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              {{ __('website/provider_profile.actions.submit_request') }}
            </button>
            <button @click="openMessage()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold transition shadow-sm">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
              {{ __('website/provider_profile.actions.send_message') }}
            </button>
          </div>

          {{-- Revealed contact details --}}
          <div x-show="showContact" x-transition class="flex flex-wrap gap-2 pt-1">
            <template x-if="primaryPhone">
              <a :href="'tel:' + primaryPhone" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-primary-50 hover:border-primary-100 text-slate-700 text-xs font-bold transition shadow-2xs">
                <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span x-text="primaryPhone"></span>
              </a>
            </template>
            <template x-if="whatsappNumber">
              <a :href="'https://wa.me/' + whatsappNumber.replace(/[^0-9]/g, '')" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-green-500 hover:bg-green-600 text-white text-xs font-bold transition shadow-2xs">
                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span x-text="whatsappNumber"></span>
              </a>
            </template>
            <template x-if="website">
              <a :href="website" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-[11px] font-semibold text-slate-700 hover:bg-slate-100 transition shadow-3xs">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                {{ __('website/provider_profile.contact.website') }}
              </a>
            </template>
            <template x-if="facebookUrl">
              <a :href="facebookUrl" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-blue-100 bg-blue-50 text-[11px] font-semibold text-blue-700 hover:bg-blue-100 transition shadow-3xs">
                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg> Facebook
              </a>
            </template>
            <template x-if="instagramUrl">
              <a :href="instagramUrl" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-pink-100 bg-pink-50 text-[11px] font-semibold text-pink-700 hover:bg-pink-100 transition shadow-3xs">
                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3.2"/><path d="M17.5 2.5h-11A5 5 0 001.5 7.5v11a5 5 0 005 5h11a5 5 0 005-5v-11a5 5 0 00-5-5zm3.5 16a3.5 3.5 0 01-3.5 3.5h-11A3.5 3.5 0 013 18.5v-11A3.5 3.5 0 016.5 4h11A3.5 3.5 0 0121 7.5v11z"/><circle cx="18.5" cy="5.5" r="1"/></svg> Instagram
              </a>
            </template>
            <template x-if="youtubeUrl">
              <a :href="youtubeUrl" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-red-100 bg-red-50 text-[11px] font-semibold text-red-700 hover:bg-red-100 transition shadow-3xs">
                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.518 3.5 12 3.5 12 3.5s-7.518 0-9.388.503a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.87.503 9.388.503 9.388.503s7.518 0 9.388-.503a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg> YouTube
              </a>
            </template>
          </div>

        </div>{{-- /left --}}

        {{-- ─── RIGHT: Cover Photo (desktop only) ─── --}}
        <div class="hidden lg:flex items-stretch py-7" x-data="{ shareOpen: false, copied: false }">
          <div class="relative w-full rounded-2xl overflow-hidden shadow-md border border-slate-200/60">
            @if($profile->cover_photo)
              <img src="{{ asset('storage/'.$profile->cover_photo) }}" class="w-full h-full object-cover select-none" alt="{{ $profile->business_name }}">
            @else
              <div class="absolute inset-0 bg-gradient-to-tr from-primary-600 via-primary-500 to-accent-500"></div>
              <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/20 via-transparent to-transparent"></div>
            @endif
            <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/40 to-transparent pointer-events-none"></div>
            {{-- Copy + Share --}}
            <div class="absolute top-3 right-3 flex items-center gap-2 z-10">
              <button @click="navigator.clipboard?.writeText('{{ url()->current() }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900/60 hover:bg-slate-900/80 backdrop-blur-md text-white border border-white/20 text-xs font-bold transition shadow-sm select-none active:scale-95">
                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-5 10h6m-8-4h8"/></svg>
                <svg x-show="copied" class="w-3.5 h-3.5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                <span x-text="copied ? '{{ __('website/provider_profile.actions.copied') }}' : '{{ __('website/provider_profile.actions.copy_link') }}'"></span>
              </button>
              <div class="relative">
                <button @click="shareOpen = !shareOpen" @click.away="shareOpen = false"
                  class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900/60 hover:bg-slate-900/80 backdrop-blur-md text-white border border-white/20 text-xs font-bold transition shadow-sm select-none active:scale-95">
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                  <span>{{ __('website/provider_profile.actions.share') }}</span>
                </button>
                <div x-show="shareOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 rounded-2xl bg-white/95 backdrop-blur-lg border border-slate-200/80 p-1.5 shadow-xl z-30 space-y-0.5" style="display: none;">
                  <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"><svg class="w-4 h-4 text-blue-600 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg> Facebook</a>
                  <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($profile->business_name) }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"><svg class="w-4 h-4 text-slate-900 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg> Twitter / X</a>
                  <a href="https://api.whatsapp.com/send?text={{ urlencode($profile->business_name . ' - ' . url()->current()) }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"><svg class="w-4 h-4 text-green-500 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> WhatsApp</a>
                </div>
              </div>
            </div>
          </div>
        </div>{{-- /right --}}

      </div>{{-- /grid --}}
    </div>{{-- /container --}}
  </div>{{-- /hero --}}

  {{-- ════════════════════════════════════════════════════════ MAIN ════════════════════════════════════════════════════════ --}}
  <div class="max-w-6xl mx-auto px-4 lg:px-6 pb-20 relative z-20">
    <div class="lg:grid lg:grid-cols-[1fr_320px] lg:gap-8 mt-4">

      {{-- ─── LEFT COLUMN ─── --}}
      <div class="min-w-0">

        {{-- Tab separator --}}
        <div class="border-b border-slate-200"></div>

        {{-- ─── SCROLLSPY TABS ─── --}}
        <div x-data="{ 

                activeSection: 'about',
                updateActive() {
                    let active = 'about';
                    const scrollPos = window.scrollY + 140;
                    document.querySelectorAll('.scroll-section').forEach(el => {
                        const top = el.getBoundingClientRect().top + window.scrollY;
                        if (top <= scrollPos) {
                            active = el.id;
                        }
                    });
                    if ((window.innerHeight + window.scrollY) >= document.documentElement.scrollHeight - 50) {
                        active = 'reviews';
                    }
                    this.activeSection = active;
                }
             }"
             x-init="updateActive()"
             @scroll.window="updateActive()"
             class="-mt-6">
             
           <nav class="sticky top-16 lg:top-[72px] z-30 bg-white/70 backdrop-blur-xl border border-white/50 shadow-sm pt-4 pb-2 sm:py-1.5 -mx-4 px-4 lg:mx-0 lg:px-0 transition-all border-b border-slate-200/40 rounded-xl">
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
              @foreach([
                ['id' => 'about',    'label' => __('website/provider_profile.tabs.about')],
                ['id' => 'services', 'label' => __('website/provider_profile.tabs.services')],
                ['id' => 'gallery',  'label' => __('website/provider_profile.tabs.gallery')],
                ['id' => 'hours',    'label' => __('website/provider_profile.tabs.hours')],
                ['id' => 'reviews',  'label' => __('website/provider_profile.tabs.reviews', ['count' => $reviewCount])],
              ] as $t)
              <button @click="
                  activeSection = '{{ $t['id'] }}';
                  const el = document.getElementById('{{ $t['id'] }}');
                  if (el) {
                      const y = el.getBoundingClientRect().top + window.scrollY - 120;
                      window.scrollTo({ top: y, behavior: 'smooth' });
                  }
                "
                :class="activeSection === '{{ $t['id'] }}' 
                  ? 'bg-primary-600 text-white shadow-xs font-black' 
                  : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-semibold'"
                class="whitespace-nowrap px-4 py-1.5 text-xs sm:text-sm rounded-lg transition-all focus:outline-none select-none">
                {{ $t['label'] }}
              </button>
              @endforeach
            </div>
          </nav>

          {{-- Sections Stack --}}
          <div class="space-y-12 pt-6">
            {{-- About Section --}}
            <section id="about" class="scroll-section scroll-mt-32">
              <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('website/provider_profile.sections.about') }}
              </h2>
              <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-xs space-y-6">
                @if($profile->description)
                  <div class="text-sm text-slate-600 leading-relaxed prose max-w-none">
                    {!! nl2br(e($profile->description)) !!}
                  </div>
                @endif

                {{-- Tagline tags --}}
                @if($profile->tagline)
                  <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5">{{ __('website/provider_profile.sections.specialties') }}</h3>
                    <div class="flex flex-wrap gap-2">
                      @foreach(array_filter(array_map('trim', explode(',', $profile->tagline))) as $tag)
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-primary-50 text-primary-700 ring-1 ring-primary-100">{{ $tag }}</span>
                      @endforeach
                    </div>
                  </div>
                @endif

                {{-- Quick stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                  @if($profile->years_of_experience)
                  <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                    <p class="text-2xl font-black text-primary-600">{{ $profile->years_of_experience }}+</p>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mt-1">{{ __('website/provider_profile.sections.years_exp_caps') }}</p>
                  </div>
                  @endif
                  @if($avgRating)
                  <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                    <p class="text-2xl font-black text-accent-500">{{ number_format($avgRating,1) }}</p>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mt-1">{{ __('website/provider_profile.sections.avg_rating_caps') }}</p>
                  </div>
                  @endif
                  <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                    <p class="text-2xl font-black text-slate-800">{{ $reviewCount }}</p>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mt-1">{{ __('website/provider_profile.sections.reviews_caps') }}</p>
                  </div>
                  <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                    <p class="text-2xl font-black text-slate-800">{{ $profile->services->count() }}</p>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mt-1">{{ __('website/provider_profile.sections.services_caps') }}</p>
                  </div>
                </div>

              </div>
            </section>

            {{-- Services Offered --}}
            <section id="services" class="scroll-section scroll-mt-32">
              <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ __('website/provider_profile.sections.services_offered') }}
              </h2>
              <div class="space-y-3">
                @forelse($profile->services as $ps)
                <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-primary-200 hover:shadow-sm transition">
                  <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-semibold text-slate-900 text-sm sm:text-base">{{ $ps->title }}</h3>
                        @if($ps->is_emergency)
                          <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 ring-1 ring-red-200">⚡ {{ __('website/provider_profile.emergency') }}</span>
                        @endif
                      </div>
                      @if($ps->description)
                        <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $ps->description }}</p>
                      @endif
                      @if($ps->duration_minutes)
                        @php
                          $durationHours = floor($ps->duration_minutes / 60);
                          $durationMins = $ps->duration_minutes % 60;
                          $durationString = '';
                          if ($durationHours > 0) {
                              $durationString .= $durationHours . 'h';
                          }
                          if ($durationMins > 0) {
                              $durationString .= ($durationHours > 0 ? ' ' : '') . $durationMins . 'm';
                          }
                          if ($ps->duration_minutes == 0) {
                              $durationString = '0m';
                          }
                        @endphp
                        <p class="mt-2 text-xs text-slate-400 flex items-center gap-1">
                          <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                          <span>{{ $durationString }}</span>
                        </p>
                      @endif
                    </div>
                    <div class="flex flex-col items-end gap-2 shrink-0">
                      <div class="text-end">
                        @if($ps->pricing_type === 'fixed')
                          <p class="text-base sm:text-lg font-bold text-slate-900">{{ $ps->currency?->symbol }}{{ number_format($ps->price_fixed, 0) }}</p>
                          <p class="text-[11px] text-slate-400">fixed price</p>
                        @elseif($ps->pricing_type === 'range')
                          <p class="text-base sm:text-lg font-bold text-slate-900">{{ $ps->currency?->symbol }}{{ number_format($ps->price_min, 0) }} – {{ number_format($ps->price_max, 0) }}</p>
                          <p class="text-[11px] text-slate-400">price range</p>
                        @elseif($ps->pricing_type === 'hourly')
                          <p class="text-base sm:text-lg font-bold text-slate-900">{{ $ps->currency?->symbol }}{{ number_format($ps->price_fixed, 0) }}</p>
                          <p class="text-[11px] text-slate-400">per hour</p>
                        @else
                          <p class="text-xs sm:text-sm font-semibold text-primary-600">Quote on request</p>
                        @endif
                      </div>
                      <button type="button" @click="openRequest({{ $ps->id }})"
                              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow-sm transition whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Book
                      </button>
                    </div>
                  </div>
                </div>
                @empty
                  <div class="bg-white border border-slate-100 rounded-2xl py-12 text-center text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm">{{ __('website/provider_profile.sections.no_services') }}</p>
                  </div>
                @endforelse
              </div>
            </section>

            {{-- Work Gallery --}}
            <section id="gallery" class="scroll-section scroll-mt-32">
              <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ __('website/provider_profile.sections.work_gallery') }}
              </h2>
              <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-xs">
                @if($profile->gallery->isEmpty())
                  <div class="text-center py-8 text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <p class="text-sm">{{ __('website/provider_profile.sections.no_gallery') }}</p>
                  </div>
                @else
                  {{-- Photos slider layout with bottom thumbnails --}}
                  @if($photos->count())
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">{{ __('website/provider_profile.sections.photos') }}</h3>
                    <div x-data="{ 
                        activeIndex: 0, 
                        photos: [
                          @foreach($photos as $item)
                            { url: '{{ asset('storage/'.$item->url) }}', caption: '{{ addslashes($item->caption ?? '') }}' },
                          @endforeach
                        ],
                        next() {
                            this.activeIndex = (this.activeIndex + 1) % this.photos.length;
                        },
                        prev() {
                            this.activeIndex = (this.activeIndex - 1 + this.photos.length) % this.photos.length;
                        }
                    }" class="mb-6">
                      
                      {{-- Big Featured Image Container --}}
                      <div class="relative w-full aspect-[16/10] sm:aspect-[16/9] rounded-2xl overflow-hidden bg-black shadow-md border border-slate-100/60 group">
                        <img :src="photos[activeIndex].url" :alt="photos[activeIndex].caption"
                             class="w-full h-full object-contain sm:object-cover transition-all duration-500 ease-in-out select-none">
                        
                        {{-- Caption Overlay --}}
                        <div x-show="photos[activeIndex].caption" 
                             class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4 pt-12 text-white">
                          <p class="text-xs sm:text-sm font-semibold tracking-wide" x-text="photos[activeIndex].caption"></p>
                        </div>
                        
                        {{-- Prev / Next Buttons --}}
                        <button @click="prev()" type="button"
                          class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md border border-white/30 text-white flex items-center justify-center hover:scale-105 active:scale-95 transition shadow-sm opacity-0 group-hover:opacity-100 focus:opacity-100">
                          <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        
                        <button @click="next()" type="button"
                          class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md border border-white/30 text-white flex items-center justify-center hover:scale-105 active:scale-95 transition shadow-sm opacity-0 group-hover:opacity-100 focus:opacity-100">
                          <svg class="w-5 h-5 stroke-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                      </div>

                      {{-- Thumbnails Scrollable Row --}}
                      <div class="mt-3 flex gap-2.5 overflow-x-auto pb-2 no-scrollbar scroll-smooth">
                        <template x-for="(photo, index) in photos" :key="index">
                          <button @click="activeIndex = index" type="button"
                                  class="shrink-0 aspect-square w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden relative transition-all border-2 duration-200 select-none outline-none focus:outline-none"
                                  :class="activeIndex === index ? 'border-primary-500 ring-2 ring-primary-100 scale-95 shadow-sm opacity-100' : 'border-slate-200 hover:border-slate-400 opacity-60 hover:opacity-100'">
                            <img :src="photo.url" class="w-full h-full object-cover">
                          </button>
                        </template>
                      </div>
                    </div>
                  @endif

                  {{-- Videos --}}
                  @if($videos->count())
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-8 mb-3">{{ __('website/provider_profile.sections.videos') }}</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                      @foreach($videos as $item)
                      @php $ytVideoId = isYt($item->url) ? ytId($item->url) : null; @endphp
                      <div class="rounded-xl overflow-hidden bg-slate-50 border border-slate-100">
                        @if($ytVideoId)
                          <div class="aspect-video">
                            <iframe
                              src="https://www.youtube.com/embed/{{ $ytVideoId }}"
                              class="w-full h-full"
                              frameborder="0"
                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                              allowfullscreen
                              loading="lazy"
                              title="{{ $item->caption ?? 'Video' }}">
                            </iframe>
                          </div>
                        @else
                          <video controls class="w-full aspect-video object-cover">
                            <source src="{{ asset('storage/'.$item->url) }}">
                          </video>
                        @endif
                        @if($item->caption)
                          <p class="px-3 py-2 text-xs text-slate-500">{{ $item->caption }}</p>
                        @endif
                      </div>
                      @endforeach
                    </div>
                  @endif
                @endif
              </div>
            </section>

            {{-- Hours Section --}}
            <section id="hours" class="scroll-section scroll-mt-32">
              <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Business Hours
              </h2>
              @if($profile->businessHours->isEmpty())
                <div class="bg-white border border-slate-100 rounded-2xl py-8 text-center text-slate-400 text-sm">No business hours listed</div>
              @else
              <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                @php $dayNames = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; @endphp
                @foreach($profile->businessHours->sortBy('day_of_week_id') as $hour)
                @php
                  $dayLabel = $hour->dayOfWeek?->getTranslation('translations','en') ?: 'Day ' . $hour->day_of_week_id;
                  $isToday  = strtolower($dayLabel) === strtolower($todayName);
                @endphp
                <div class="flex items-center justify-between px-5 py-3.5 {{ !$loop->last ? 'border-b border-slate-100' : '' }} {{ $isToday ? 'bg-primary-50/50' : '' }}">
                  <span class="text-sm {{ $isToday ? 'font-bold text-primary-700' : 'text-slate-700' }}">
                    {{ $dayLabel }}
                    @if($isToday) <span class="ms-1 text-[10px] font-bold bg-primary-600 text-white rounded-full px-2 py-0.5">{{ __('website/provider_profile.sections.today') }}</span> @endif
                  </span>
                  @if($hour->is_closed)
                    <span class="text-sm text-red-500 font-bold">{{ __('website/provider_profile.sections.closed') }}</span>
                  @else
                    <span class="text-sm text-slate-600 font-semibold">
                       {{ substr($hour->start_time ?? '09:00', 0, 5) }} – {{ substr($hour->end_time ?? '18:00', 0, 5) }}
                    </span>
                  @endif
                </div>
                @endforeach
              </div>
              @endif
            </section>

            {{-- Reviews --}}
            <section id="reviews" class="scroll-section scroll-mt-32">
              <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                {{ __('website/provider_profile.reviews.heading') }}
              </h2>
              <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-xs">
                @if($profile->reviews->isEmpty())
                  <div class="text-center py-8 text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                    <p class="text-sm">{{ __('website/provider_profile.sections.no_reviews_yet') }}</p>
                  </div>
                @else
                  {{-- Rating summary --}}
                  @if($avgRating)
                  <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5 mb-5 flex items-center gap-6">
                    <div class="text-center shrink-0">
                      <p class="text-5xl font-black text-slate-900">{{ number_format($avgRating, 1) }}</p>
                      <div class="flex justify-center mt-1">
                        @for($i=1; $i<=5; $i++)
                          <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-accent-400' : 'text-slate-200' }} fill-current" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        @endfor
                      </div>
                      <p class="text-xs text-slate-400 mt-1.5 font-bold uppercase tracking-wide">{{ trans_choice('website/provider_profile.reviews.review_count', $reviewCount, ['count' => $reviewCount]) }}</p>
                    </div>
                    <div class="flex-1 space-y-1.5">
                      @for($s=5; $s>=1; $s--)
                      @php $cnt = $profile->reviews->filter(fn($r) => $r->rating === $s)->count(); @endphp
                      <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-500 w-3 text-end font-bold">{{ $s }}</span>
                        <svg class="w-3 h-3 text-accent-400 fill-current shrink-0" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                          <div class="h-full bg-accent-400 rounded-full" style="width: {{ $reviewCount > 0 ? round($cnt / $reviewCount * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-slate-400 w-4 text-end">{{ $cnt }}</span>
                      </div>
                      @endfor
                    </div>
                  </div>
                  @endif

                  {{-- Review list --}}
                  <div class="space-y-4">
                    @foreach($profile->reviews as $review)
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-slate-300 transition">
                      <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-sm shrink-0">
                          {{ strtoupper(substr($review->customer?->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                          <div class="flex items-center justify-between gap-2 flex-wrap">
                            <p class="font-bold text-slate-900 text-sm">{{ $review->customer?->name ?? __('website/provider_profile.reviews.anonymous') }}</p>
                            <span class="text-xs text-slate-400 font-medium">{{ $review->created_at->diffForHumans() }}</span>
                          </div>
                          <div class="flex mt-0.5">
                            @for($i=1; $i<=5; $i++)
                              <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-accent-400' : 'text-slate-200' }} fill-current" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            @endfor
                          </div>
                          @if($review->review)
                            <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $review->review }}</p>
                          @endif
                          {{-- Reply --}}
                          @if($review->reply)
                          <div class="mt-3 ms-2 border-s-2 border-primary-200 ps-3 bg-primary-50/50 rounded-e-lg p-2.5">
                            <p class="text-xs font-bold text-primary-700">{{ $profile->business_name }} {{ __('website/provider_profile.reviews.replied') }}</p>
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed">{{ $review->reply->reply }}</p>
                          </div>
                          @endif
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </section>
          </div>
        </div>
      </div>{{-- /left col --}}

      {{-- ─── STICKY SIDEBAR (DESKTOP) ─── --}}
      <div class="hidden lg:block">
        <div class="sticky top-24 space-y-4">

          {{-- Contact Card --}}
          <div class="bg-white rounded-2xl border border-slate-200 p-5 pb-4 shadow-xs">
            <p class="text-xs font-black text-slate-900 uppercase tracking-wider mb-4">{{ __('website/provider_profile.contact.contact_details') }}</p>

            {{-- Contact Reveal Trigger --}}
            <div class="space-y-4">
              <template x-if="!showContact">
                <button @click="viewContactDetails()" 
                        class="w-full inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-black transition shadow-sm select-none">
                    <svg x-show="loadingContact" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg x-show="!loadingContact" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span x-text="loadingContact ? '{{ __('website/provider_profile.contact.fetching') }}' : '{{ __('website/provider_profile.contact.show_info') }}'"></span>
                </button>
              </template>

              <div x-show="showContact" x-transition class="space-y-3.5">
                <template x-if="primaryPhone">
                  <a :href="'tel:' + primaryPhone" class="flex items-center gap-3.5 p-3 rounded-xl border border-slate-100 bg-slate-50 hover:bg-primary-50 hover:border-primary-100 transition group shadow-2xs">
                    <span class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center shrink-0 text-primary-600 group-hover:bg-primary-200 transition">
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <div>
                      <p class="text-[9px] uppercase font-black tracking-wider text-slate-400">{{ __('website/provider_profile.contact.direct_phone') }}</p>
                      <p class="text-sm font-black text-slate-800" x-text="primaryPhone"></p>
                    </div>
                  </a>
                </template>
                <template x-if="whatsappNumber">
                  <a :href="'https://wa.me/' + whatsappNumber.replace(/[^0-9]/g, '')" target="_blank" rel="noopener" class="flex items-center gap-3.5 p-3 rounded-xl border border-slate-100 bg-slate-50 hover:bg-green-50 hover:border-green-100 transition group shadow-2xs">
                    <span class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0 text-green-600 group-hover:bg-green-200 transition">
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </span>
                    <div>
                      <p class="text-[9px] uppercase font-black tracking-wider text-slate-400">{{ __('website/provider_profile.contact.whatsapp') }}</p>
                      <p class="text-sm font-black text-slate-800" x-text="whatsappNumber"></p>
                    </div>
                  </a>
                </template>

                {{-- Revealed Desktop Social & Website --}}
                <div x-show="website || facebookUrl || instagramUrl || youtubeUrl" 
                     class="pt-3 border-t border-slate-100/80 mt-3 space-y-2">
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ __('website/provider_profile.contact.socials_website') }}</p>
                  <div class="grid grid-cols-2 gap-2">
                    <template x-if="website">
                      <a :href="website" target="_blank" rel="noopener" 
                         class="flex items-center gap-2 p-2 rounded-xl border border-slate-100 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition group shadow-3xs">
                        <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        <span>Website</span>
                      </a>
                    </template>
                    <template x-if="facebookUrl">
                      <a :href="facebookUrl" target="_blank" rel="noopener" 
                         class="flex items-center gap-2 p-2 rounded-xl border border-blue-100 bg-blue-50/50 hover:bg-blue-50 text-blue-700 text-xs font-semibold transition group shadow-3xs">
                        <svg class="w-4 h-4 shrink-0 text-blue-600 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        <span>Facebook</span>
                      </a>
                    </template>
                    <template x-if="instagramUrl">
                      <a :href="instagramUrl" target="_blank" rel="noopener" 
                         class="flex items-center gap-2 p-2 rounded-xl border border-pink-100 bg-pink-50/50 hover:bg-pink-50 text-pink-700 text-xs font-semibold transition group shadow-3xs">
                        <svg class="w-4 h-4 shrink-0 text-pink-600 fill-current" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3.2"/><path d="M17.5 2.5h-11A5 5 0 001.5 7.5v11a5 5 0 005 5h11a5 5 0 005-5v-11a5 5 0 00-5-5zm3.5 16a3.5 3.5 0 01-3.5 3.5h-11A3.5 3.5 0 013 18.5v-11A3.5 3.5 0 016.5 4h11A3.5 3.5 0 0121 7.5v11z"/><circle cx="18.5" cy="5.5" r="1"/></svg>
                        <span>Instagram</span>
                      </a>
                    </template>
                    <template x-if="youtubeUrl">
                      <a :href="youtubeUrl" target="_blank" rel="noopener" 
                         class="flex items-center gap-2 p-2 rounded-xl border border-red-100 bg-red-50/50 hover:bg-red-50 text-red-700 text-xs font-semibold transition group shadow-3xs">
                        <svg class="w-4 h-4 shrink-0 text-red-600 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.518 3.5 12 3.5 12 3.5s-7.518 0-9.388.503a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.87.503 9.388.503 9.388.503s7.518 0 9.388-.503a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        <span>YouTube</span>
                      </a>
                    </template>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4 space-y-2.5">
              <button @click="openRequest()" class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-black text-sm shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ __('website/provider_profile.actions.submit_request') }}
              </button>
              <button @click="openMessage()" class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                {{ __('website/provider_profile.actions.send_message') }}
              </button>
              
            </div>
          </div>

          {{-- Today's Hours --}}
          <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <p class="text-xs font-black text-slate-900 uppercase tracking-wider mb-3">{{ __('website/provider_profile.sections.todays_hours') }}</p>
            @if($todayHour)
              @if($todayHour->is_closed)
                <p class="text-sm text-red-500 font-bold flex items-center gap-1.5">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                  {{ __('website/provider_profile.sections.closed_today') }}
                </p>
              @else
                <p class="text-sm text-green-600 font-bold flex items-center gap-1.5">
                  <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                  {{ __('website/provider_profile.sections.open_today') }}
                </p>
                <p class="text-xs text-slate-500 mt-1 font-semibold">{{ substr($todayHour->start_time ?? '09:00', 0, 5) }} – {{ substr($todayHour->end_time ?? '18:00', 0, 5) }}</p>
              @endif
            @else
              <p class="text-sm text-slate-400 font-medium">{{ __('website/provider_profile.sections.hours_not_specified') }}</p>
            @endif
            @if($profile->emergency_available)
              <p class="mt-2 text-xs text-red-600 font-semibold flex items-center gap-1">⚡ {{ __('website/provider_profile.sections.emergency_requests_247') }}</p>
            @endif
          </div>

          {{-- Service Areas --}}
          @if($profile->serviceAreas->count())
          <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <p class="text-xs font-black text-slate-900 uppercase tracking-wider mb-3">{{ __('website/provider_profile.sections.service_areas') }}</p>
            <div class="space-y-2.5">
              @foreach($profile->serviceAreas->take(4) as $area)
              <div class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                <svg class="w-3.5 h-3.5 text-primary-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>{{ $area->area?->name ?? $area->district?->name ?? $area->division?->name ?? '—' }}</span>
                @if($area->radius_km)
                  <span class="text-xs text-slate-400 ms-auto font-normal">{{ $area->radius_km }} km</span>
                @endif
              </div>
              @endforeach
            </div>
          </div>
          @endif

        </div>
      </div>

    </div>{{-- /grid --}}

    {{-- Similar Providers Section --}}
    @if(isset($similarProviders) && $similarProviders->isNotEmpty())
      <div class="mt-16 pt-12 border-t border-slate-100">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">{{ __('website/provider_profile.sections.similar_providers') }}</h2>
            <p class="text-xs sm:text-sm text-slate-400 font-semibold mt-1">{{ __('website/provider_profile.sections.similar_providers_desc') }}</p>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          @foreach($similarProviders as $sp)
            @include('website.partials.provider-card', ['profile' => $sp])
          @endforeach
        </div>
      </div>
    @endif
  </div>{{-- /container --}}


  {{-- ════════════════════════════════════════════════════════ MODALS ════════════════════════════════════════════════════════ --}}
  
  {{-- 1. SUBMIT REQUEST MODAL --}}
  <div x-show="showRequestModal" 
       class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
       style="display: none;"
       x-transition>
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100" @click.away="showRequestModal = false">
      <button @click="showRequestModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
      
      <div class="flex items-center gap-3 mb-5">
        <span class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </span>
        <div>
          <h3 class="text-base font-black text-slate-900">{{ __('website/provider_profile.modals.request.heading') }}</h3>
          <p class="text-xs text-slate-500 font-semibold">{{ __('website/provider_profile.modals.request.to', ['name' => $profile->business_name]) }}</p>
        </div>
      </div>

      <form action="{{ route('provider.profile.request', $profile) }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitRequest($el)">
        @csrf
        <div class="space-y-4 max-h-[70vh] overflow-y-auto pe-1">

          {{-- Provider Service --}}
          @if($profile->services->isNotEmpty())
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Service Required</label>
            <select id="req_provider_service_select" name="provider_service_id" x-model="reqPreselectedServiceId"
                    class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 bg-white">
              <option value="">— Select a service (optional) —</option>
              @foreach($profile->services as $ps)
                <option value="{{ $ps->id }}">{{ $ps->title }}</option>
              @endforeach
            </select>
          </div>
          @endif

          {{-- Title --}}
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('website/provider_profile.modals.request.title') }}</label>
            <input type="text" name="title" required placeholder="{{ __('website/provider_profile.modals.request.title_placeholder') }}"
                   class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
          </div>

          {{-- Description --}}
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('website/provider_profile.modals.request.desc') }}</label>
            <textarea name="description" rows="3" required placeholder="{{ __('website/provider_profile.modals.request.desc_placeholder') }}"
                      class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 resize-none"></textarea>
          </div>

          {{-- Date & Time --}}
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('website/provider_profile.modals.request.date') }}</label>
              <input type="date" name="preferred_date" min="{{ date('Y-m-d') }}"
                     class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('website/provider_profile.modals.request.time') }}</label>
              @php
                $openHours = $profile->businessHours->where('is_closed', false);
                if ($openHours->isNotEmpty()) {
                    $slotStart = $openHours->min(fn($h) => strtotime($h->start_time));
                    $slotEnd   = $openHours->max(fn($h) => strtotime($h->end_time));
                } else {
                    $slotStart = strtotime('06:00');
                    $slotEnd   = strtotime('20:00');
                }
              @endphp
              <select name="preferred_time" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 bg-white">
                <option value="">— Any time —</option>
                @php
                  $slot = $slotStart;
                  while ($slot <= $slotEnd) {
                      echo '<option value="' . date('H:i', $slot) . '">' . date('h:i A', $slot) . '</option>';
                      $slot += 1800;
                  }
                @endphp
              </select>
            </div>
          </div>

          {{-- Urgency --}}
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('website/provider_profile.modals.request.urgency') }}</label>
            <select name="urgency" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 bg-white">
              <option value="normal">{{ __('website/provider_profile.modals.request.urgency_normal') }}</option>
              <option value="urgent">{{ __('website/provider_profile.modals.request.urgency_urgent') }}</option>
              <option value="emergency">{{ __('website/provider_profile.modals.request.urgency_emergency') }}</option>
            </select>
          </div>

          {{-- Address --}}
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('website/provider_profile.modals.request.address') }}</label>

            {{-- Saved address cards --}}
            <template x-if="reqAddresses.length > 0">
              <div class="grid grid-cols-1 gap-2 mb-3">
                <template x-for="addr in reqAddresses" :key="addr.id">
                  <label class="relative flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150 select-none"
                         :class="reqSelectedAddressId == addr.id ? 'border-primary-500 bg-primary-50/30' : 'border-slate-200 bg-white hover:border-slate-300'">
                    <input type="radio" name="_req_addr_pick" :value="addr.id" x-model="reqSelectedAddressId" @change="reqPickAddress(addr.id)" class="sr-only">
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition"
                         :class="reqSelectedAddressId == addr.id ? 'bg-primary-600 border-primary-600' : 'border-slate-300'">
                      <svg x-show="reqSelectedAddressId == addr.id" class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 12 12"><circle cx="6" cy="6" r="4"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-xs font-bold text-slate-800 uppercase tracking-wide" x-text="addr.label"></p>
                      <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed truncate" x-text="addr.address"></p>
                    </div>
                  </label>
                </template>
                <label class="relative flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150 select-none"
                       :class="reqSelectedAddressId === 'other' ? 'border-primary-500 bg-primary-50/30' : 'border-slate-200 bg-white hover:border-slate-300'">
                  <input type="radio" name="_req_addr_pick" value="other" x-model="reqSelectedAddressId" @change="reqPickAddress('other')" class="sr-only">
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition"
                       :class="reqSelectedAddressId === 'other' ? 'bg-primary-600 border-primary-600' : 'border-slate-300'">
                    <svg x-show="reqSelectedAddressId === 'other'" class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 12 12"><circle cx="6" cy="6" r="4"/></svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-xs font-bold text-slate-800 uppercase tracking-wide">Other / Custom</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Enter a different address</p>
                  </div>
                </label>
              </div>
            </template>

            {{-- Manual address input --}}
            <div x-show="reqShowManualAddress || reqAddresses.length === 0" x-transition>
              <input type="text" name="address" x-model="reqAddress"
                     placeholder="{{ __('website/provider_profile.modals.request.address_placeholder') }}"
                     :required="reqShowManualAddress || reqAddresses.length === 0"
                     class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
            </div>

            {{-- Hidden address input when using saved address --}}
            <div x-show="!reqShowManualAddress && reqAddresses.length > 0" x-cloak>
              <input type="hidden" name="address" :value="reqAddress">
              <div class="flex items-center gap-2 px-3 py-2 bg-primary-50 border border-primary-100 rounded-xl text-xs text-primary-700 font-medium">
                <svg class="w-4 h-4 shrink-0 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span x-text="reqAddress"></span>
              </div>
            </div>
          </div>

          {{-- Attachments --}}
          <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('website/provider_profile.modals.request.attachments') }}</label>
            <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.mp4"
                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
          </div>

        </div>{{-- /scrollable area --}}

        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
          <button type="button" @click="showRequestModal = false" class="px-4 py-2 text-sm font-semibold text-slate-500 hover:bg-slate-50 rounded-xl transition">{{ __('website/provider_profile.modals.request.cancel') }}</button>
          <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-xl shadow-xs transition disabled:opacity-50">
            <svg x-show="submitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="submitting ? '{{ __('website/provider_profile.modals.request.submitting') }}' : '{{ __('website/provider_profile.modals.request.submit') }}'"></span>
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- 2. SEND MESSAGE MODAL --}}
  <div x-show="showMessageModal" 
       class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
       style="display: none;"
       x-transition>
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 flex flex-col max-h-[90vh]" @click.away="showMessageModal = false">
      <button @click="showMessageModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
      
      <div class="flex items-center gap-3 mb-4 shrink-0">
        <span class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        </span>
        <div>
          <h3 class="text-base font-black text-slate-900">{{ __('website/provider_profile.modals.message.heading', ['name' => $profile->business_name]) }}</h3>
          <p class="text-xs text-slate-500 font-semibold">{{ __('website/provider_profile.modals.message.history') }}</p>
        </div>
      </div>

      {{-- Messages Box --}}
      <div id="chat-messages-container" class="flex-1 overflow-y-auto min-h-[300px] max-h-[450px] border border-slate-100 rounded-xl bg-slate-50 p-4 space-y-3 mb-4 flex flex-col">
        
        {{-- Load older messages trigger --}}
        <template x-if="hasMoreChat">
          <div class="text-center py-2">
            <button type="button" @click="loadChatMessages(chatPage + 1, true)" class="text-xs font-semibold text-primary-600 hover:text-primary-700 bg-white border border-slate-200 px-3 py-1.5 rounded-full shadow-sm hover:shadow-md transition">
              <span x-show="!loadingChat">↑ {{ __('website/provider_profile.modals.message.load_older') }}</span>
              <span x-show="loadingChat" class="inline-flex items-center gap-1"><svg class="animate-spin h-3 w-3 text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __('website/provider_profile.modals.message.loading') }}</span>
            </button>
          </div>
        </template>

        {{-- If no messages yet --}}
        <template x-if="chatMessages.length === 0">
          <div class="flex-1 flex flex-col items-center justify-center text-center p-6 text-slate-400">
            <span class="text-4xl mb-2">💬</span>
            <p class="text-xs font-semibold">{{ __('website/provider_profile.modals.message.no_conversation') }}</p>
            <p class="text-[10px]">{{ __('website/provider_profile.modals.message.start_conversation') }}</p>
          </div>
        </template>

        {{-- Message Bubbles --}}
        <div class="space-y-3 flex-1 flex flex-col justify-end">
          <template x-for="msg in chatMessages" :key="msg.id">
            <div :class="msg.sender_id === currentUserId ? 'self-end text-right max-w-[85%]' : 'self-start text-left max-w-[85%]'">
              <div :class="msg.sender_id === currentUserId ? 'bg-primary-600 text-white rounded-2xl rounded-tr-none px-4 py-2.5 shadow-sm text-sm' : 'bg-white text-slate-800 border border-slate-100 rounded-2xl rounded-tl-none px-4 py-2.5 shadow-sm text-sm'">
                <p class="leading-relaxed whitespace-pre-line" x-text="msg.message"></p>
              </div>
              <span class="text-[9px] text-slate-400 font-medium mt-1 block" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
            </div>
          </template>
        </div>
      </div>

      {{-- Message Send Box --}}
      <form action="{{ route('provider.profile.message', $profile) }}" method="POST" @submit.prevent="submitMessage($el)" class="shrink-0">
        @csrf
        <div class="relative flex items-end gap-2 bg-white border border-slate-200 rounded-2xl p-1.5 focus-within:border-primary-400 focus-within:ring-2 focus-within:ring-primary-50 focus-within:ring-offset-0 transition duration-150">
          <textarea x-model="chatMessageText" rows="1" required placeholder="{{ __('website/provider_profile.modals.message.placeholder') }}" 
                    @keydown.enter.prevent="if (!event.shiftKey) { submitMessage($el.closest('form')); }"
                    class="flex-1 max-h-[100px] min-h-[40px] border-0 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 bg-transparent focus:ring-0 focus:outline-none resize-none"></textarea>
          <button type="submit" :disabled="submitting || !chatMessageText.trim()" 
                  class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary-600 hover:bg-primary-700 text-white shadow-sm transition disabled:opacity-40 disabled:cursor-not-allowed">
            <svg x-show="!submitting" class="w-5 h-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            <svg x-show="submitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </button>
        </div>
      </form>
    </div>
  </div>
  {{-- 3. PROFILE INCOMPLETE MODAL --}}
  <div x-show="showProfileIncompleteModal" 
       class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
       style="display: none;"
       x-transition>
    <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 text-center" @click.away="showProfileIncompleteModal = false">
      <button @click="showProfileIncompleteModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
      
      <div class="w-16 h-16 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      </div>
      
      <h3 class="text-xl font-black text-slate-900 mb-2">Almost there!</h3>
      <p class="text-sm text-slate-500 mb-6 leading-relaxed">
        You need to complete your profile before you can request a service or send messages.
      </p>
      
      <div class="space-y-3">
        <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-black text-sm shadow-sm transition">
          Complete Profile
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
        <button @click="showProfileIncompleteModal = false" class="text-xs text-slate-400 hover:text-slate-600 font-semibold mt-1">
          Cancel
        </button>
      </div>
    </div>
  </div>

  {{-- 4. LOGIN/REGISTER MODAL --}}
  <div x-show="showLoginModal" 
       class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
       style="display: none;"
       x-transition>
    <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 text-center" @click.away="showLoginModal = false">
      <button @click="showLoginModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>

      <span class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      </span>

      <h3 class="text-lg font-black text-slate-900">{{ __('website/provider_profile.modals.auth.heading') }}</h3>
      <p class="text-sm text-slate-500 mt-2 max-w-xs mx-auto">{{ __('website/provider_profile.modals.auth.desc') }}</p>

      <div class="mt-6 flex flex-col gap-2.5">
        <a href="{{ route('login') }}" class="w-full py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm shadow-xs transition">
          {{ __('website/provider_profile.modals.auth.login') }}
        </a>
        <a href="{{ route('register') }}" class="w-full py-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm transition">
          {{ __('website/provider_profile.modals.auth.register') }}
        </a>
        <button @click="showLoginModal = false" class="text-xs text-slate-400 hover:text-slate-600 font-semibold mt-1">
          {{ __('website/provider_profile.modals.auth.continue') }}
        </button>
      </div>
    </div>
  </div>
</div>{{-- /x-data --}}

@endsection
