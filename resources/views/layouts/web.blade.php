<!DOCTYPE html>
<html lang="en" dir="ltr" class="h-full bg-white text-slate-700 scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0F94EA">
  <meta name="description" content="{{ config('app.name') }} — {{ __('layout/web.description') }}">
  <title>{{ config('app.name') }} — {{ __('layout/web.title') }}</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50:'#F0F8FF',100:'#E0F1FE',200:'#BAE0FD',300:'#7CC8FB',
              400:'#38ADF7',500:'#0F94EA',600:'#0277C7',700:'#0561A1'
            },
            accent: {
              50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',
              400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C'
            }
          },
          fontFamily: {
            sans: ['Inter','system-ui','sans-serif'],
            bn:   ['"Hind Siliguri"','"Noto Sans Bengali"','sans-serif'],
            ar:   ['Cairo','Tajawal','"Noto Naskh Arabic"','sans-serif']
          },
          boxShadow: {
            soft: '0 4px 20px -8px rgba(15, 148, 234, 0.15)',
            warm: '0 4px 20px -8px rgba(249, 115, 22, 0.15)'
          }
        }
      }
    }
  </script>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600;700&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    html[lang="bn"] body { font-family: 'Hind Siliguri', system-ui, sans-serif; line-height: 1.75; }
    html[lang="ar"] body { font-family: 'Cairo', system-ui, sans-serif; }
    html[lang="en"] body { font-family: 'Inter', system-ui, sans-serif; }
    [dir="rtl"] .rtl-flip { transform: scaleX(-1); }

    /* Hide scrollbars on horizontal scrolling areas while keeping function */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    @media (prefers-reduced-motion: reduce) {
      * { transition: none !important; animation: none !important; }
    }
  </style>
</head>
<body class="min-h-full antialiased font-sans">

<!-- ===================== ANNOUNCEMENT BAR ===================== -->
<div x-data="{ open: true }" x-show="open" class="bg-primary-600 text-white text-xs sm:text-sm">
  <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between gap-3">
    <p class="flex items-center gap-2 truncate">
      <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent-400 animate-pulse"></span>
      <span class="truncate">{!! __('layout/web.announcement', ['span' => '<strong class="font-semibold">', 'end' => '</strong>']) !!}</span>
    </p>
    <button @click="open = false" class="shrink-0 text-white/80 hover:text-white" aria-label="Dismiss">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
</div>

<!-- ===================== HEADER ===================== -->
<header
  x-data="{ mobileOpen: false, langOpen: false, catOpen: false }"
  class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200"
>
  <div class="max-w-7xl mx-auto px-4 lg:px-6 h-16 lg:h-[72px] flex items-center gap-3 lg:gap-6">

    <!-- Mobile menu trigger -->
    <button @click="mobileOpen = true" class="lg:hidden -ms-2 p-2 rounded-md text-slate-700 hover:bg-slate-100" aria-label="Open menu">
      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    <!-- Logo (always LTR) -->
    <a href="#" class="flex items-center gap-2 shrink-0" dir="ltr">
      <span class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center text-white shadow-soft">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      </span>
      <span class="font-bold text-lg text-slate-900 tracking-tight">{{ config('app.name') }}</span>
    </a>

    <!-- Desktop nav -->
    <nav class="hidden lg:flex items-center gap-1 ms-2">
      <div class="relative" @click.outside="catOpen = false">
        <button @click="catOpen = !catOpen" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100">
          {{ __('web.nav.categories') }}
          <svg class="w-4 h-4 transition" :class="catOpen && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div x-show="catOpen" x-transition class="absolute top-full start-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-slate-200 p-2 z-30" style="display:none">
          <a href="#" class="flex items-center gap-3 p-2 rounded-md hover:bg-slate-50">
            <span class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center text-sm">⚡</span>
            <span class="text-sm text-slate-700">{{ __('Electrician') }}</span>
          </a>
          <a href="#" class="flex items-center gap-3 p-2 rounded-md hover:bg-slate-50">
            <span class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center text-sm">❄️</span>
            <span class="text-sm text-slate-700">{{ __('AC Repair') }}</span>
          </a>
          <a href="#" class="flex items-center gap-3 p-2 rounded-md hover:bg-slate-50">
            <span class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center text-sm">🔧</span>
            <span class="text-sm text-slate-700">{{ __('Plumbing') }}</span>
          </a>
          <a href="#" class="flex items-center gap-3 p-2 rounded-md hover:bg-slate-50">
            <span class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center text-sm">🧹</span>
            <span class="text-sm text-slate-700">{{ __('Cleaning') }}</span>
          </a>
          <div class="border-t border-slate-100 my-1"></div>
          <a href="#" class="block p-2 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded-md">{{ __('web.nav.see_all') }} →</a>
        </div>
      </div>
      <a href="#how-it-works" class="px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100">{{ __('web.nav.how_it_works') }}</a>
      <a href="#become-provider" class="px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100">{{ __('web.nav.become_provider') }}</a>
    </nav>

    <div class="flex-1"></div>

    <!-- Language switcher -->
    <div class="relative" @click.outside="langOpen = false">
      <button @click="langOpen = !langOpen" class="flex items-center gap-1.5 px-2.5 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100" aria-label="{{ __('web.footer.language') }}">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        <span class="hidden sm:inline">EN</span>
        <svg class="w-3.5 h-3.5 hidden sm:block" :class="langOpen && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div x-show="langOpen" x-transition class="absolute top-full end-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-slate-200 p-1 z-30" style="display:none">
        <a href="#" class="flex items-center justify-between px-3 py-2 rounded-md hover:bg-slate-50 text-sm">English <span class="text-primary-500">✓</span></a>
        <a href="#" class="block px-3 py-2 rounded-md hover:bg-slate-50 text-sm">বাংলা</a>
        <a href="#" class="block px-3 py-2 rounded-md hover:bg-slate-50 text-sm" dir="rtl">العربية</a>
      </div>
    </div>

    <!-- Auth -->
    <a href="#" class="hidden sm:inline-flex text-sm font-medium text-slate-700 hover:text-primary-600 px-2">{{ __('web.nav.login') }}</a>
    <a href="#" class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition shadow-soft">
      {{ __('web.nav.signup') }}
    </a>
  </div>

  <!-- Mobile drawer -->
  <div x-show="mobileOpen" class="lg:hidden fixed inset-0 z-50" style="display:none">
    <div @click="mobileOpen = false" class="absolute inset-0 bg-slate-900/50"></div>
    <aside class="absolute inset-y-0 start-0 w-[85%] max-w-xs bg-white shadow-xl flex flex-col" x-transition:enter="transition" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
      <div class="h-16 flex items-center justify-between px-4 border-b border-slate-200">
        <span class="font-bold text-slate-900">{{ __('web.nav.menu') }}</span>
        <button @click="mobileOpen = false" class="p-2 -me-2 text-slate-600" aria-label="Close menu">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <nav class="flex-1 overflow-y-auto p-3 space-y-1">
        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 text-slate-700"><span class="text-lg">📋</span> {{ __('web.nav.categories') }}</a>
        <a href="#how-it-works" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 text-slate-700"><span class="text-lg">💡</span> {{ __('web.nav.how_it_works') }}</a>
        <a href="#become-provider" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 text-slate-700"><span class="text-lg">💼</span> {{ __('web.nav.become_provider') }}</a>
        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 text-slate-700"><span class="text-lg">📞</span> {{ __('layout/web.support') }}</a>
        <div class="border-t border-slate-100 my-2"></div>
        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 text-slate-700">{{ __('web.nav.login') }}</a>
      </nav>
      <div class="p-4 border-t border-slate-200">
        <a href="#" class="block w-full text-center px-4 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600">{{ __('layout/web.sign_up_free') }}</a>
      </div>
    </aside>
  </div>
</header>



    @yield('content')

<!-- ===================== FOOTER ===================== -->
<footer class="bg-slate-50 border-t border-slate-200">
  <div class="max-w-7xl mx-auto px-4 lg:px-6 py-12 lg:py-16">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">

      <div class="col-span-2 lg:col-span-2">
        <a href="#" class="flex items-center gap-2" dir="ltr">
          <span class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center text-white">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          </span>
          <span class="font-bold text-lg text-slate-900">{{ config('app.name') }}</span>
        </a>
        <p class="mt-3 text-sm text-slate-500 max-w-xs">
          {{ __('web.footer.tagline') }}
        </p>
        <div class="mt-5 flex gap-3">
          <a href="#" class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-500 hover:border-primary-200 flex items-center justify-center transition" aria-label="Facebook">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
          </a>
          <a href="#" class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-500 hover:border-primary-200 flex items-center justify-center transition" aria-label="Instagram">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
          <a href="#" class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-500 hover:border-primary-200 flex items-center justify-center transition" aria-label="LinkedIn">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.761 0 5-2.239 5-5v-14c0-2.761-2.239-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
          </a>
        </div>
      </div>

      <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-900 mb-4">{{ __('web.footer.company') }}</h4>
        <ul class="space-y-2.5 text-sm text-slate-600">
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.about') }}</a></li>
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.careers') }}</a></li>
          <li><a href="#" class="hover:text-primary-600">{{ __('Blog') }}</a></li>
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.press') }}</a></li>
        </ul>
      </div>

      <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-900 mb-4">{{ __('web.footer.for_customers') }}</h4>
        <ul class="space-y-2.5 text-sm text-slate-600">
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.browse') }}</a></li>
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.post_req') }}</a></li>
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.safety') }}</a></li>
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.help') }}</a></li>
        </ul>
      </div>

      <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-900 mb-4">{{ __('web.footer.for_providers') }}</h4>
        <ul class="space-y-2.5 text-sm text-slate-600">
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.become') }}</a></li>
          <li><a href="#" class="hover:text-primary-600">{{ __('web.nav.pricing') }}</a></li>
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.affiliate') }}</a></li>
          <li><a href="#" class="hover:text-primary-600">{{ __('web.footer.resources') }}</a></li>
        </ul>
      </div>
    </div>

    <div class="mt-12 pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="text-xs text-slate-500">{{ __('web.footer.copyright', ['year' => date('Y'), 'app' => config('app.name')]) }}</p>
      <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
        <a href="#" class="hover:text-primary-600">{{ __('web.footer.privacy') }}</a>
        <a href="#" class="hover:text-primary-600">{{ __('web.footer.terms') }}</a>
        <a href="#" class="hover:text-primary-600">{{ __('web.footer.cookies') }}</a>
        <span class="hidden sm:inline">·</span>
        <select class="bg-transparent text-xs text-slate-600 border border-slate-200 rounded-md px-2 py-1 focus:ring-2 focus:ring-primary-100 focus:border-primary-500 focus:outline-none">
          <option>English</option>
          <option>বাংলা</option>
          <option>العربية</option>
        </select>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
