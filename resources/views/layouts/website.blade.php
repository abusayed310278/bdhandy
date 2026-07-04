@php
  $locale = app()->getLocale();
  $dir    = in_array($locale, ['ar','he','fa','ur']) ? 'rtl' : 'ltr';
  $langs  = [
    'en' => ['label' => 'English',    'short' => 'EN',  'dir' => 'ltr'],
    'bn' => ['label' => 'বাংলা',      'short' => 'বাং', 'dir' => 'ltr'],
    'ar' => ['label' => 'العربية',    'short' => 'ع',   'dir' => 'rtl'],
    'uz' => ['label' => "O\u{02BC}zbekcha", 'short' => 'UZ',  'dir' => 'ltr'],
    'ru' => ['label' => 'Русский',    'short' => 'RU',  'dir' => 'ltr'],
  ];
  $currentLang = $langs[$locale] ?? $langs['en'];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="h-full bg-white text-slate-700 scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0F94EA">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="application-name" content="{{ config('app.name') }}">
  <meta name="format-detection" content="telephone=no">
  <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
  <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

  {{-- SEO: title, meta description, canonical, robots, OG, Twitter Card --}}
  @php
    echo \Artesaos\SEOTools\Facades\SEOMeta::generate();
    echo \Artesaos\SEOTools\Facades\OpenGraph::generate();
    echo \Artesaos\SEOTools\Facades\TwitterCard::generate();
  @endphp

  {{-- JSON-LD structured data (WebSite, Organization, LocalBusiness, BreadcrumbList…) --}}
  @php echo \Artesaos\SEOTools\Facades\JsonLdMulti::generate(); @endphp

  {{-- Hreflang alternates for multilingual support --}}
  @include('partials.seo._hreflang')

  {{-- Sitemap discovery --}}
  <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('/sitemap.xml') }}">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { 50:'#F0F8FF',100:'#E0F1FE',200:'#BAE0FD',300:'#7CC8FB',400:'#38ADF7',500:'#0F94EA',600:'#0277C7',700:'#0561A1' },
            accent:  { 50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C' }
          },
          boxShadow: { soft:'0 4px 20px -8px rgba(15,148,234,0.15)', warm:'0 4px 20px -8px rgba(249,115,22,0.15)' }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600;700&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    html[lang="bn"] body { font-family:'Hind Siliguri',system-ui,sans-serif; line-height:1.75; }
    html[lang="ar"] body { font-family:'Cairo',system-ui,sans-serif; }
    html[lang="en"] body { font-family:'Inter',system-ui,sans-serif; }
    [dir="rtl"] .rtl-flip { transform:scaleX(-1); }
    .no-scrollbar::-webkit-scrollbar { display:none; }
    .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
    @media (prefers-reduced-motion: reduce) { * { transition:none!important; animation:none!important; } }

    /* PNotify Modern & Premium Custom Styling */
    .pnotify-container {
      background: rgba(222, 250, 224, 0.98) !important;
      backdrop-filter: blur(8px) !important;
      border: 1px solid rgba(226, 232, 240, 0.8) !important;
      border-radius: 14px !important;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
      padding: 14px 18px !important;
      font-family: 'Inter', system-ui, sans-serif !important;
      color: #1e293b !important;
      min-width: 320px !important;
    }
    
    .pnotify-container.pnotify-type-success {
      border-left: 4px solid #10b981 !important;
      background: rgba(240, 253, 250, 0.98) !important;
    }
    .pnotify-container.pnotify-type-error {
      border-left: 4px solid #ef4444 !important;
      background: rgba(254, 242, 242, 0.98) !important;
    }
    .pnotify-container.pnotify-type-info {
      border-left: 4px solid #3b82f6 !important;
      background: rgba(239, 246, 255, 0.98) !important;
    }
    .pnotify-container.pnotify-type-notice {
      border-left: 4px solid #f59e0b !important;
      background: rgba(254, 243, 199, 0.98) !important;
    }

    .pnotify-title {
      font-size: 0.875rem !important;
      font-weight: 700 !important;
      color: #0f172a !important;
      margin-bottom: 4px !important;
      line-height: 1.25rem !important;
    }

    .pnotify-text {
      font-size: 0.8125rem !important;
      font-weight: 500 !important;
      color: #475569 !important;
      line-height: 1.125rem !important;
    }

    .pnotify-closer {
      color: #94a3b8 !important;
      transition: color 0.15s ease !important;
      outline: none !important;
    }
    .pnotify-closer:hover {
      color: #475569 !important;
    }
    
    .pnotify-icon {
      font-size: 1.125rem !important;
      margin-right: 12px !important;
      align-self: flex-start !important;
      margin-top: 2px !important;
    }
    .pnotify-type-success .pnotify-icon {
      color: #10b981 !important;
    }
    .pnotify-type-error .pnotify-icon {
      color: #ef4444 !important;
    }
  </style>
  <!-- PNotify -->
  <link href="https://cdn.jsdelivr.net/npm/@pnotify/core@5.2.0/dist/PNotify.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@pnotify/core@5.2.0/dist/BrightTheme.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@pnotify/core@5.2.0/dist/PNotify.js"></script>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  @stack('head')
</head>
<body class="min-h-full antialiased font-sans">

{{-- Announcement Banner --}}
@if(isset($banner) && $banner)
<div x-data="{ open: true }" x-show="open" class="bg-primary-600 text-white text-xs sm:text-sm">
  <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between gap-3">
    <p class="flex items-center gap-2 truncate">
      <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent-400 animate-pulse"></span>
      <span class="truncate">{!! $banner->title !!}</span>
    </p>
    <button @click="open = false" class="shrink-0 text-white/80 hover:text-white" aria-label="Dismiss">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
</div>
@endif

{{-- Header + mobile drawer share one Alpine scope so the drawer can live outside the header
     (backdrop-blur on header creates a containing block that would clip fixed children) --}}
<div x-data="{ mobileOpen: false, langOpen: false, catOpen: false, scrolled: false, showTopBtn: false }" @keydown.escape="mobileOpen=false; langOpen=false; catOpen=false" @scroll.window="scrolled = window.scrollY > 10; showTopBtn = window.scrollY > 300">

<header class="sticky top-0 z-40 bg-white border-b border-slate-200 transition-shadow duration-300" :class="scrolled ? 'shadow-md' : 'shadow-none'">
  <div class="max-w-7xl mx-auto px-4 lg:px-6 h-16 lg:h-[72px] flex items-center gap-3 lg:gap-5">

    {{-- Mobile menu trigger --}}
    <button @click="mobileOpen = true" class="lg:hidden -ms-2 p-2 rounded-md text-slate-700 hover:bg-slate-100" aria-label="Open menu">
      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0" dir="ltr">
      <span class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center text-white shadow-soft">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      </span>
      <span class="font-bold text-lg text-slate-900 tracking-tight">{{ config('app.name') }}</span>
    </a>

    {{-- Desktop nav --}}
    <nav class="hidden lg:flex items-center gap-0.5 ms-1">

      {{-- Categories dropdown --}}
      <div class="relative" @click.outside="catOpen = false">
        <button @click="catOpen = !catOpen" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100">
          {{ __('web.nav.categories') }}
          <svg class="w-4 h-4 transition-transform" :class="catOpen && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div x-show="catOpen" x-transition class="absolute top-full start-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-slate-200 p-2 z-30" style="display:none">
          @foreach($navCategories ?? [] as $cat)
            @php $name = $cat->getTranslation('translations', $locale) ?: $cat->slug; @endphp
            <a href="{{ route('providers', ['category' => $cat->id]) }}" class="flex items-center gap-3 p-2 rounded-md hover:bg-slate-50">
              @if($cat->icon)
                <span class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center overflow-hidden shrink-0">
                  <img src="{{ asset('storage/'.$cat->icon) }}" class="w-5 h-5 object-contain">
                </span>
              @else
                <span class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center text-sm shrink-0">🔧</span>
              @endif
              <span class="text-sm text-slate-700">{{ $name }}</span>
            </a>
          @endforeach
          <div class="border-t border-slate-100 my-1"></div>
          <a href="{{ route('categories') }}" class="block p-2 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded-md">{{ __('web.nav.see_all') }} →</a>
        </div>
      </div>

      <a href="{{ route('providers') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('providers') ? 'text-primary-600 bg-primary-50' : 'text-slate-700 hover:bg-slate-100' }}">{{ __('web.nav.find_providers') }}</a>
      <a href="{{ route('pricing') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('pricing') ? 'text-primary-600 bg-primary-50' : 'text-slate-700 hover:bg-slate-100' }}">{{ __('web.nav.pricing') }}</a>
    
      @if(!auth()->check() || auth()->user()->isCustomer())
      <a href="{{ route('post-a-need') }}" class="px-3 py-2 rounded-md text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 font-semibold">📋 {{ __('web.nav.post_a_need') }}</a>
      @endif
    </nav>

    <div class="flex-1"></div>

    {{-- Language switcher --}}
    <div class="relative" @click.outside="langOpen = false">
      <button @click="langOpen = !langOpen" class="flex items-center gap-1.5 px-2.5 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100" aria-label="{{ __('web.footer.language') }}">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        <span class="hidden sm:inline text-xs font-semibold">{{ $currentLang['short'] }}</span>
        <svg class="w-3 h-3 hidden sm:block transition-transform" :class="langOpen && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div x-show="langOpen" x-transition class="absolute top-full end-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 p-1 z-30" style="display:none">
        @foreach($langs as $code => $lang)
        <form method="POST" action="{{ route('lang.switch', $code) }}" class="m-0 p-0 block">
          @csrf
          <button type="submit" dir="{{ $lang['dir'] }}" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-slate-50 text-sm {{ $locale === $code ? 'text-primary-600 font-medium' : 'text-slate-700' }}">
            {{ $lang['label'] }}
            @if($locale === $code)
              <svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            @endif
          </button>
        </form>
        @endforeach
      </div>
    </div>

    {{-- Become a Provider (orange CTA) --}}
    @guest
    <a href="{{ route('register') }}" class="hidden lg:inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-accent-500 text-white text-sm font-medium hover:bg-accent-600 transition shadow-warm">
      {{ __('web.nav.become_provider') }}
    </a>
    @endguest

    {{-- Auth --}}
    @auth
      <div class="hidden sm:flex items-center gap-1">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          {{ __('web.nav.dashboard') }}
        </a>
        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
          @csrf
          <button type="submit" class="inline-flex items-center justify-center p-2 rounded-md text-slate-500 hover:text-red-600 hover:bg-red-50 transition" title="{{ __('web.nav.logout') ?? 'Logout' }}">
            <i class="fas fa-sign-out-alt text-lg"></i>
          </button>
        </form>
      </div>
    @else
      <a href="{{ route('login') }}" class="hidden sm:inline-flex text-sm font-medium text-slate-700 hover:text-primary-600 px-2">{{ __('web.nav.login') }}</a>
      <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition shadow-soft">{{ __('web.nav.signup') }}</a>
    @endauth
  </div>
</header>

{{-- Mobile drawer — backdrop and panel are separate elements so each can have its own x-show + x-transition --}}
{{-- They live outside <header> to avoid backdrop-blur creating a fixed-position containing block --}}

  {{-- Backdrop --}}
  <div
    x-show="mobileOpen"
    x-transition:enter="transition-opacity duration-200 ease-out"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-150 ease-in"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="mobileOpen = false"
    class="lg:hidden fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-[2px]"
    style="display:none"
  ></div>

  {{-- Drawer Panel --}}
  <aside
    x-show="mobileOpen"
    x-transition:enter="transition-transform duration-300 ease-out"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition-transform duration-200 ease-in"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="lg:hidden fixed inset-y-0 start-0 z-50 w-[82%] max-w-[320px] bg-white flex flex-col overflow-hidden"
    style="display:none; box-shadow: 4px 0 32px -4px rgba(15,148,234,0.12), 2px 0 16px -2px rgba(0,0,0,0.10);"
  >

      {{-- ── Top bar ── --}}
      <div class="flex items-center justify-between px-4 h-14 border-b border-slate-100 shrink-0">
        <a href="{{ route('home') }}" @click="mobileOpen = false" class="flex items-center gap-2" dir="ltr">
          <span class="w-7 h-7 rounded-lg bg-primary-500 flex items-center justify-center text-white shadow-soft">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          </span>
          <span class="font-bold text-[15px] text-slate-900 tracking-tight">{{ config('app.name') }}</span>
        </a>
        <button @click="mobileOpen = false" aria-label="Close menu"
          class="w-8 h-8 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors">
          <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      {{-- ── User card (auth only) ── --}}
      @auth
      <div class="mx-3 mt-3 mb-1 rounded-xl border border-slate-100 bg-gradient-to-br from-primary-50 to-white p-3.5 flex items-center gap-3 shrink-0">
        <div class="w-10 h-10 rounded-full bg-primary-500 text-white flex items-center justify-center font-bold text-base shrink-0 shadow-soft">
          {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold text-slate-900 truncate leading-tight">{{ auth()->user()->name }}</p>
          <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
        </div>
        <a href="{{ route('dashboard') }}" @click="mobileOpen = false"
          class="shrink-0 text-primary-500 hover:text-primary-600 transition-colors" title="Dashboard">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      </div>
      @endauth

      {{-- ── Scrollable nav area ── --}}
      <nav class="flex-1 overflow-y-auto px-3 pb-3 pt-2 space-y-0.5">

        {{-- Post a Need — customer CTA (prominent) --}}
        @if(!auth()->check() || auth()->user()->isCustomer())
          <a href="{{ route('post-a-need') }}"
            class="flex items-center gap-3 px-3.5 py-3 rounded-xl bg-primary-500 text-white text-sm font-semibold mb-2 shadow-soft hover:bg-primary-600 active:scale-[0.98] transition-all">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('web.nav.post_a_need') }}
            <svg class="w-3.5 h-3.5 ms-auto opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        @endif

        {{-- Section label --}}
        <p class="px-1 pt-1 pb-0.5 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">
          {{ __('web.nav.menu') }}
        </p>

        @php
          $navItems = [
            ['route' => 'providers',    'label' => __('web.nav.find_providers'), 'icon' => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>'],
            ['route' => 'categories',   'label' => __('web.nav.categories'),     'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'],
            ['route' => 'pricing',      'label' => __('web.nav.pricing'),         'icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
          ];
        @endphp

        @foreach($navItems as $item)
          @php $active = request()->routeIs($item['route']); @endphp
          <a href="{{ route($item['route']) }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
              {{ $active
                ? 'bg-primary-50 text-primary-700 font-semibold'
                : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
              {{ $active ? 'bg-primary-100' : 'bg-slate-100' }}">
              <svg class="w-4 h-4 {{ $active ? 'text-primary-600' : 'text-slate-500' }}"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                {!! $item['icon'] !!}
              </svg>
            </span>
            <span>{{ $item['label'] }}</span>
            @if($active)
              <svg class="w-3.5 h-3.5 ms-auto text-primary-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            @endif
          </a>
        @endforeach

        {{-- Divider --}}
        <div class="pt-2 pb-1">
          <div class="border-t border-slate-100"></div>
        </div>

        {{-- Dashboard (auth) --}}
        @auth
          @php $active = request()->routeIs('dashboard') || request()->routeIs('*.dashboard'); @endphp
          <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}"
              class="flex-1 flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                {{ $active ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
              <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $active ? 'bg-primary-100' : 'bg-slate-100' }}">
                <svg class="w-4 h-4 {{ $active ? 'text-primary-600' : 'text-slate-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
              </span>
              {{ __('web.nav.dashboard') }}
            </a>
            <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 shrink-0">
              @csrf
              <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 hover:text-red-600 hover:bg-red-50 transition" title="{{ __('web.nav.logout') ?? 'Logout' }}">
                <i class="fas fa-sign-out-alt text-lg"></i>
              </button>
            </form>
          </div>
        @endauth

        {{-- Become a Provider --}}
        @guest
        <a href="{{ route('register') }}"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all text-accent-600 hover:bg-accent-50">
          <span class="w-8 h-8 rounded-lg bg-accent-50 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-accent-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          {{ __('web.nav.become_provider') }}
        </a>
        @endguest

        {{-- Language Switcher --}}
        <div class="pt-2 pb-1">
          <div class="border-t border-slate-100"></div>
        </div>
        <div class="px-1 pt-1">
          <p class="px-2 mb-2 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">{{ __('web.footer.language') }}</p>
          <div class="flex flex-wrap gap-1.5">
            @foreach($langs as $code => $lang)
            <form method="POST" action="{{ route('lang.switch', $code) }}" class="m-0 p-0">
              @csrf
              <button type="submit"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition-all
                  {{ $locale === $code
                    ? 'bg-primary-500 text-white border-primary-500 shadow-soft'
                    : 'bg-white text-slate-600 border-slate-200 hover:border-primary-300 hover:text-primary-600' }}"
                dir="{{ $lang['dir'] }}">
                {{ $lang['short'] }}
                <span class="text-[10px] {{ $locale === $code ? 'text-primary-100' : 'text-slate-400' }}">{{ $lang['label'] }}</span>
              </button>
            </form>
            @endforeach
          </div>
        </div>

      </nav>

      {{-- ── Guest bottom CTAs ── --}}
      @guest
      <div class="shrink-0 px-3 pb-4 pt-3 border-t border-slate-100 space-y-2">
        <a href="{{ route('register') }}"
          class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition-all shadow-soft active:scale-[0.98]">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
          {{ __('web.nav.signup') }}
        </a>
        <a href="{{ route('login') }}"
          class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-100 transition-all active:scale-[0.98]">
          {{ __('web.nav.login') }}
        </a>
      </div>
      @endguest

    </aside>

{{-- Page Content --}}
@yield('content')

{{-- Footer --}}
<footer class="bg-slate-50 border-t border-slate-200 mt-12">
  <div class="max-w-7xl mx-auto px-4 lg:px-6 py-12 lg:py-16">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">

      {{-- Brand + Social --}}
      <div class="col-span-2 lg:col-span-2">
        <a href="{{ route('home') }}" class="flex items-center gap-2" dir="ltr">
          <span class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center text-white">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          </span>
          <span class="font-bold text-lg text-slate-900">{{ config('app.name') }}</span>
        </a>
        <p class="mt-3 text-sm text-slate-500 max-w-xs">{{ __('web.footer.tagline') }}</p>
        <div class="mt-5 flex gap-3">
          <a href="#" class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-500 hover:border-primary-200 flex items-center justify-center transition" aria-label="Facebook">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
          </a>
          <a href="#" class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-500 hover:border-primary-200 flex items-center justify-center transition" aria-label="Instagram">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
          <a href="#" class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-500 hover:border-primary-200 flex items-center justify-center transition" aria-label="LinkedIn">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.761 0 5-2.239 5-5v-14c0-2.761-2.239-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
          </a>
          <a href="#" class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-accent-500 hover:border-accent-200 flex items-center justify-center transition" aria-label="YouTube">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          </a>
        </div>
      </div>

      {{-- Company --}}
      <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-900 mb-4">{{ __('web.footer.company') }}</h4>
        <ul class="space-y-2.5 text-sm text-slate-600">
          <li><a href="{{ route('about') }}" class="hover:text-primary-600">{{ __('web.footer.about') }}</a></li>
          <li><a href="{{ route('how-it-works') }}" class="hover:text-primary-600">{{ __('web.nav.how_it_works') }}</a></li>
          <!-- <li><a href="{{ route('affiliate-info') }}" class="hover:text-primary-600">{{ __('web.footer.affiliate') }}</a></li> -->
          <li><a href="{{ route('contact') }}" class="hover:text-primary-600">{{ __('web.footer.contact') }}</a></li>
        </ul>
      </div>

      {{-- For Customers --}}
      <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-900 mb-4">{{ __('web.footer.for_customers') }}</h4>
        <ul class="space-y-2.5 text-sm text-slate-600">
          <!-- <li><a href="{{ route('categories') }}" class="hover:text-primary-600">{{ __('web.footer.browse') }}</a></li> -->
          @if(!auth()->check() || auth()->user()->isCustomer())
          <li><a href="{{ route('post-a-need') }}" class="hover:text-primary-600">{{ __('web.footer.post_req') }}</a></li>
          @endif
          <li><a href="{{ route('safety') }}" class="hover:text-primary-600">{{ __('web.footer.safety') }}</a></li>
          <li><a href="{{ route('help') }}" class="hover:text-primary-600">{{ __('web.footer.help') }}</a></li>
        </ul>
      </div>

      {{-- For Providers --}}
      <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-900 mb-4">{{ __('web.footer.for_providers') }}</h4>
        <ul class="space-y-2.5 text-sm text-slate-600">
          @guest
          <li><a href="{{ route('register') }}" class="hover:text-primary-600">{{ __('web.footer.become') }}</a></li>
          @endguest
          <li><a href="{{ route('pricing') }}" class="hover:text-primary-600">{{ __('web.nav.pricing') }}</a></li>
          <li><a href="{{ route('affiliate-info') }}" class="hover:text-primary-600">{{ __('web.footer.affiliate') }}</a></li>
          <!-- <li><a href="{{ route('resources') }}" class="hover:text-primary-600">{{ __('web.footer.resources') }}</a></li> -->
        </ul>
      </div>
    </div>

    {{-- Bottom bar --}}
    <div class="mt-12 pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="text-xs text-slate-500">{{ __('web.footer.copyright', ['year' => date('Y'), 'app' => config('app.name')]) }}</p>
      <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
        <a href="{{ route('privacy') }}" class="hover:text-primary-600">{{ __('web.footer.privacy') }}</a>
        <a href="{{ route('terms') }}" class="hover:text-primary-600">{{ __('web.footer.terms') }}</a>
        <a href="{{ route('cookies') }}" class="hover:text-primary-600">{{ __('web.footer.cookies') }}</a>
        <span class="hidden sm:inline text-slate-300">·</span>
        {{-- Footer language select --}}
        <div x-data class="flex items-center gap-1">
          <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          <select id="footer-lang-select" class="bg-transparent text-xs text-slate-600 border border-slate-200 rounded-md px-2 py-1 focus:ring-2 focus:ring-primary-100 focus:border-primary-500 focus:outline-none">
            @foreach($langs as $code => $lang)
              <option value="{{ $code }}" {{ $locale === $code ? 'selected' : '' }}>{{ $lang['label'] }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>
</footer>

<script>
document.getElementById('footer-lang-select')?.addEventListener('change', function() {
  const code = this.value;
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '/lang/' + code;
  const csrf = document.createElement('input');
  csrf.type = 'hidden';
  csrf.name = '_token';
  csrf.value = document.querySelector('meta[name="csrf-token"]').content;
  form.appendChild(csrf);
  document.body.appendChild(form);
  form.submit();
});
</script>

@stack('scripts')
{{-- Scroll to Top Button --}}
<button 
  x-show="showTopBtn" 
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0 translate-y-4"
  x-transition:enter-end="opacity-100 translate-y-0"
  x-transition:leave="transition ease-in duration-300"
  x-transition:leave-start="opacity-100 translate-y-0"
  x-transition:leave-end="opacity-0 translate-y-4"
  @click="window.scrollTo({top: 0, behavior: 'smooth'})"
  class="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-primary-500 hover:bg-primary-600 text-white flex items-center justify-center shadow-lg transition-transform hover:scale-105 focus:outline-none"
  aria-label="Back to top"
  style="display: none;"
>
  <i class="fas fa-arrow-up text-lg"></i>
</button>
</div>{{-- end x-data wrapper --}}
</body>
</html>
