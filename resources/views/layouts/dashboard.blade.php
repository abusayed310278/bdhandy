<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar','he','fa','ur']) ? 'rtl' : 'ltr' }}" class="h-full bg-slate-50 text-slate-700">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', __('layout/dashboard.title')) — {{ config('app.name') }}</title>
  <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
  <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
@php
  $locale = app()->getLocale();
  $langs = [
    'en' => ['label' => 'English',    'short' => 'EN',  'dir' => 'ltr'],
    'bn' => ['label' => 'বাংলা',      'short' => 'বাং', 'dir' => 'ltr'],
    'ar' => ['label' => 'العربية',    'short' => 'ع',   'dir' => 'rtl'],
    'uz' => ['label' => "O\u{02BC}zbekcha", 'short' => 'UZ',  'dir' => 'ltr'],
    'ru' => ['label' => 'Русский',    'short' => 'RU',  'dir' => 'ltr'],
  ];
  $currentLang = $langs[$locale] ?? $langs['en'];
@endphp

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
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* PNotify Modern & Premium Custom Styling */
    .pnotify-container {
      background: rgba(255, 255, 255, 0.98) !important;
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
  <link rel="stylesheet" href="{{ asset('vendor/mckenziearts/laravel-notify/dist/notify.css') }}">
  <!-- PNotify -->
  <link href="https://cdn.jsdelivr.net/npm/@pnotify/core@5.2.0/dist/PNotify.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@pnotify/core@5.2.0/dist/BrightTheme.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@pnotify/core@5.2.0/dist/PNotify.js"></script>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  @stack('head')
</head>
<body class="min-h-full antialiased font-sans" x-data="{ sidebarOpen: false }">

<!-- ════════════════════════════════════════
     SIDEBAR
═════════════════════════════════════════ -->
<aside
  class="fixed inset-y-0 start-0 z-40 w-60 bg-white border-e border-slate-200 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0"
  :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
  <!-- Brand -->
  <div class="h-16 px-4 flex items-center justify-between border-b border-slate-200 shrink-0">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5" dir="ltr">
      <span class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center text-white shadow-soft shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      </span>
      <div class="leading-tight">
        <span class="block text-sm font-bold text-slate-900">{{ config('app.name') }}</span>
        <span class="block text-[10px] uppercase tracking-wide font-semibold
          @role('admin|super_admin|moderator|support') text-accent-600 @endrole
          @role('freelancer|business') text-primary-600 @endrole
          @role('customer') text-green-600 @endrole
        ">
          @role('super_admin') {{ __('layout/dashboard.roles.super_admin') }}
          @elserole('admin') {{ __('layout/dashboard.roles.admin') }}
          @elserole('moderator') {{ __('layout/dashboard.roles.moderator') }}
          @elserole('support') {{ __('layout/dashboard.roles.support') }}
          @elserole('business') {{ __('layout/dashboard.roles.business') }}
          @elserole('freelancer') {{ __('layout/dashboard.roles.freelancer') }}
          @elserole('customer') {{ __('layout/dashboard.roles.customer') }}
          @endrole
        </span>
      </div>
    </a>
    <button @click="sidebarOpen = false" class="lg:hidden p-1.5 text-slate-400 hover:text-slate-600 rounded-md hover:bg-slate-100 transition">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5 no-scrollbar">

    <!-- ── ADMIN ─────────────────────────────── -->
    @role('admin|super_admin|moderator|support')
    <x-nav-link-dashboard :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="dashboard">
      {{ __('layout/dashboard.nav.overview') }}
    </x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.providers') }}</p>

    <x-nav-link-dashboard :href="route('admin.providers.index')" :active="request()->routeIs('admin.providers.*')" icon="shield">
      {{ __('layout/dashboard.nav.verification_queue') }}
      @php $pendingCount = \App\Models\ProviderProfile::where('verification_status','in_review')->count(); @endphp
      @if($pendingCount > 0)
        <span class="ms-auto inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-[10px] font-bold bg-accent-100 text-accent-700">{{ $pendingCount }}</span>
      @endif
    </x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.administration') }}</p>

    <x-nav-link-dashboard :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')" icon="shield">
      {{ __('layout/dashboard.nav.roles_permissions') }}
    </x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.support_desk') }}</p>
    <x-nav-link-dashboard :href="route('admin.tickets.index')" :active="request()->routeIs('admin.tickets.*')" icon="help">
      {{ __('layout/dashboard.nav.support_tickets') }}
      @php $openTicketsCount = \App\Models\SupportTicket::whereIn('status', ['open', 'pending'])->count(); @endphp
      @if($openTicketsCount > 0)
        <span class="ms-auto inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700">{{ $openTicketsCount }}</span>
      @endif
    </x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.geography') }}</p>
    <x-nav-link-dashboard :href="route('admin.countries.index')"  :active="request()->routeIs('admin.countries.*')"  icon="globe">{{ __('layout/dashboard.nav.countries') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.divisions.index')"  :active="request()->routeIs('admin.divisions.*')"  icon="map">{{ __('layout/dashboard.nav.divisions') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.districts.index')"  :active="request()->routeIs('admin.districts.*')"  icon="navigation">{{ __('layout/dashboard.nav.districts') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.areas.index')"      :active="request()->routeIs('admin.areas.*')"      icon="map-pin">{{ __('layout/dashboard.nav.areas') }}</x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.catalog') }}</p>
    <x-nav-link-dashboard :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" icon="layers">{{ __('layout/dashboard.nav.categories') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.services.index')"   :active="request()->routeIs('admin.services.*')"   icon="tool">{{ __('layout/dashboard.nav.services') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.document_types.index')" :active="request()->routeIs('admin.document_types.*')" icon="file-text">{{ __('layout/dashboard.nav.document_types') }}</x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.finance_growth') }}</p>
    <x-nav-link-dashboard :href="route('admin.subscription_plans.index')" :active="request()->routeIs('admin.subscription_plans.*')" icon="briefcase">{{ __('layout/dashboard.nav.plans') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')" icon="list">{{ __('layout/dashboard.nav.coupons') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.currencies.index')" :active="request()->routeIs('admin.currencies.*')" icon="currency">{{ __('layout/dashboard.nav.currencies') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.referrals.index')" :active="request()->routeIs('admin.referrals.*')" icon="users">Referral Payouts</x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.content') }}</p>
    <x-nav-link-dashboard :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')" icon="image">{{ __('layout/dashboard.nav.banners') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.faqs.index')"   :active="request()->routeIs('admin.faqs.*')"    icon="help">{{ __('layout/dashboard.nav.faqs') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.languages.index')" :active="request()->routeIs('admin.languages.*')" icon="globe">{{ __('layout/dashboard.nav.languages') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')" icon="settings">{{ __('layout/dashboard.nav.settings') }}</x-nav-link-dashboard>
    @endrole

    <!-- ── PROVIDER (freelancer / business) ──── -->
    @role('freelancer|business')
    <x-nav-link-dashboard :href="route('provider.dashboard')" :active="request()->routeIs('provider.dashboard')" icon="dashboard">
      {{ __('layout/dashboard.nav.dashboard') }}
    </x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.provider_hub') }}</p>
    <x-nav-link-dashboard :href="route('provider.profile.edit')" :active="request()->routeIs('provider.profile.*')" icon="user">{{ __('layout/dashboard.nav.provider_profile') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.services.index')" :active="request()->routeIs('provider.services.*')" icon="tool">{{ __('layout/dashboard.nav.my_services') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.areas.index')" :active="request()->routeIs('provider.areas.*')" icon="map-pin">{{ __('layout/dashboard.nav.service_areas') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.hours.index')" :active="request()->routeIs('provider.hours.*') || request()->routeIs('provider.holidays.*')" icon="calendar">{{ __('layout/dashboard.nav.hours_holidays') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.gallery.index')" :active="request()->routeIs('provider.gallery.*')" icon="image">{{ __('layout/dashboard.nav.gallery') }}</x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.business_hub') }}</p>
    @php
      $openLeadsCount = \App\Models\CustomerRequirement::where('status','open')->count();
      $pendingRequestsCount = \App\Models\ServiceRequest::where('provider_id', auth()->id())->where('request_status', 'pending')->count();
      $unreadMessagesCount = \App\Models\Message::where('sender_id', '!=', auth()->id())
          ->where('is_read', false)
          ->whereHas('conversation', fn($q) => $q->where('provider_id', auth()->id()))
          ->count();
    @endphp
    <x-nav-link-dashboard :href="route('provider.leads.index')" :active="request()->routeIs('provider.leads.*')" icon="inbox" :badge="$openLeadsCount > 0 ? $openLeadsCount : null">{{ __('layout/dashboard.nav.leads') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.requests.index')" :active="request()->routeIs('provider.requests.*')" icon="clipboard" :badge="$pendingRequestsCount > 0 ? $pendingRequestsCount : null">{{ __('layout/dashboard.nav.requests') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.conversations.index')" :active="request()->routeIs('provider.conversations.*')" icon="message-circle" :badge="$unreadMessagesCount > 0 ? $unreadMessagesCount : null">{{ __('layout/dashboard.nav.messages') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.reviews.index')" :active="request()->routeIs('provider.reviews.*')" icon="star">{{ __('layout/dashboard.nav.reviews') }}</x-nav-link-dashboard>

    {{-- ── BUSINESS-ONLY: Team Management ───────────────────────── --}}
    @role('business')
    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Team Management</p>
    <x-nav-link-dashboard :href="route('business.team.index')" :active="request()->routeIs('business.team.*')" icon="users">Team Members</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('business.dispatch.index')" :active="request()->routeIs('business.dispatch.*')" icon="inbox">Job Dispatch</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('business.schedule.index')" :active="request()->routeIs('business.schedule.*')" icon="calendar">Schedule</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('business.attendance.index')" :active="request()->routeIs('business.attendance.*')" icon="clipboard">Attendance</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('business.location.live')" :active="request()->routeIs('business.location.*')" icon="map-pin">Live Location</x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Assets</p>
    <x-nav-link-dashboard :href="route('business.equipment.index')" :active="request()->routeIs('business.equipment.*')" icon="tool">Equipment</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('business.inventory.index')" :active="request()->routeIs('business.inventory.*')" icon="list">Inventory</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('business.vehicles.index')" :active="request()->routeIs('business.vehicles.*')" icon="briefcase">Vehicles</x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Operations</p>
    <x-nav-link-dashboard :href="route('business.payroll.index')" :active="request()->routeIs('business.payroll.*')" icon="bar-chart">Payroll</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('business.analytics.team')" :active="request()->routeIs('business.analytics.*')" icon="bar-chart">Team Analytics</x-nav-link-dashboard>
    @endrole

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.account') }}</p>
    <x-nav-link-dashboard :href="route('provider.subscription.index')" :active="request()->routeIs('provider.subscription.*')" icon="briefcase">{{ __('layout/dashboard.nav.subscription') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.wallet.index')" :active="request()->routeIs('provider.wallet.*')" icon="currency">Wallet</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.affiliate.index')" :active="request()->routeIs('provider.affiliate.*')" icon="users">{{ __('layout/dashboard.nav.affiliate') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.analytics.index')" :active="request()->routeIs('provider.analytics.*')" icon="bar-chart">{{ __('layout/dashboard.nav.analytics') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('provider.tickets.index')" :active="request()->routeIs('provider.tickets.*')" icon="help">{{ __('layout/dashboard.nav.support_tickets') }}</x-nav-link-dashboard>

    {{-- Subscription mini-card --}}
    @php $sub = auth()->user()->activeSubscription(); @endphp
    @if($sub)
    @php
      $daysLeft = $sub->end_date ? (int) now()->diffInDays($sub->end_date, false) : null;
      $totalDays = $sub->plan ? $sub->plan->duration_months * 30 : 30;
      $elapsed   = $sub->start_date ? (int) $sub->start_date->diffInDays(now()) : 0;
      $elapsedPct = max(0, min(100, $totalDays > 0 ? round($elapsed / $totalDays * 100) : 0));
      $isWarning = $daysLeft !== null && $daysLeft <= 7;

      if ($daysLeft !== null && $daysLeft >= 0) {
          if ($daysLeft >= 30) {
              $months = (int) floor($daysLeft / 30);
              $remDays = $daysLeft % 30;
              $daysLabel = $remDays > 0 ? "{$months}mo {$remDays}d" : "{$months}mo";
          } else {
              $daysLabel = "{$daysLeft}d";
          }
      } else {
          $daysLabel = __('layout/dashboard.expired');
      }
    @endphp
    <div class="mt-5 mx-0 rounded-xl p-3 space-y-2 {{ $isWarning ? 'bg-accent-50 border border-accent-200' : 'bg-primary-50' }}">
      <div class="flex items-center justify-between">
        <p class="text-xs font-semibold {{ $isWarning ? 'text-accent-700' : 'text-primary-700' }}">{{ $sub->plan->name }}</p>
        @if($daysLeft !== null)
          <p class="text-[10px] font-bold {{ $isWarning ? 'text-accent-600' : 'text-primary-500' }}">
            {{ $daysLeft >= 0 ? __('layout/dashboard.days_left', ['days' => $daysLabel]) : $daysLabel }}
          </p>
        @endif
      </div>
      @if($sub->end_date)
        <div class="w-full {{ $isWarning ? 'bg-accent-200' : 'bg-primary-200' }} rounded-full h-1.5">
          <div class="{{ $isWarning ? 'bg-accent-500' : 'bg-primary-500' }} h-1.5 rounded-full transition-all" style="width:{{ $elapsedPct }}%"></div>
        </div>
      @endif
      <a href="{{ route('provider.subscription.index') }}" class="block text-center text-[11px] font-bold text-white {{ $isWarning ? 'bg-accent-500 hover:bg-accent-600' : 'bg-accent-500 hover:bg-accent-600' }} rounded-lg py-1.5 transition">
        {{ $isWarning ? __('layout/dashboard.renew_now') : __('layout/dashboard.upgrade_plan') }}
      </a>
    </div>
    @endif
    @endrole

    <!-- ── CUSTOMER ──────────────────────────── -->
    @role('customer')
    <x-nav-link-dashboard :href="route('customer.dashboard')" :active="request()->routeIs('customer.dashboard')" icon="dashboard">
      {{ __('layout/dashboard.nav.dashboard') }}
    </x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.my_activity') }}</p>
    @php
      $customerPendingRequestsCount = \App\Models\ServiceRequest::where('customer_id', auth()->id())->where('request_status', 'pending')->count();
      $customerUnreadMessagesCount = \App\Models\Message::where('sender_id', '!=', auth()->id())
          ->where('is_read', false)
          ->whereHas('conversation', fn($q) => $q->where('customer_id', auth()->id()))
          ->count();
    @endphp
    <x-nav-link-dashboard :href="route('customer.requests.index')" :active="request()->routeIs('customer.requests.*')" icon="clipboard" :badge="$customerPendingRequestsCount > 0 ? $customerPendingRequestsCount : null">{{ __('layout/dashboard.nav.my_requests') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('customer.requirements.index')" :active="request()->routeIs('customer.requirements.*')" icon="list">{{ __('layout/dashboard.nav.my_requirements') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('customer.conversations.index')" :active="request()->routeIs('customer.conversations.*')" icon="message-circle" :badge="$customerUnreadMessagesCount > 0 ? $customerUnreadMessagesCount : null">{{ __('layout/dashboard.nav.messages') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('customer.saved.index')" :active="request()->routeIs('customer.saved.*')" icon="heart">{{ __('layout/dashboard.nav.saved_providers') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('customer.reviews.index')" :active="request()->routeIs('customer.reviews.*')" icon="star">{{ __('layout/dashboard.nav.my_reviews') }}</x-nav-link-dashboard>

    <p class="px-3 mt-5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('layout/dashboard.nav.account') }}</p>
    <x-nav-link-dashboard :href="route('customer.addresses.index')" :active="request()->routeIs('customer.addresses.*')" icon="map-pin">{{ __('layout/dashboard.nav.my_addresses') }}</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('customer.tickets.index')" :active="request()->routeIs('customer.tickets.*')" icon="help">{{ __('layout/dashboard.nav.support') }}</x-nav-link-dashboard>
    @endrole

    <!-- ── TEAM MEMBER (technician portal) ──── -->
    @role('team_member')
    @php $tm = auth()->user()->teamMember; @endphp
    <p class="px-3 mt-2 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">My Work</p>
    <x-nav-link-dashboard :href="route('tech.schedule.today')" :active="request()->routeIs('tech.schedule.*')" icon="calendar">Today's Schedule</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('tech.jobs.index')" :active="request()->routeIs('tech.jobs.*')" icon="clipboard">My All Jobs</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('tech.attendance.history')" :active="request()->routeIs('tech.attendance.*')" icon="clipboard">Attendance</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('tech.equipment.index')" :active="request()->routeIs('tech.equipment.*')" icon="tool">My Equipment</x-nav-link-dashboard>
    <x-nav-link-dashboard :href="route('tech.earnings.index')" :active="request()->routeIs('tech.earnings.*')" icon="bar-chart">Earnings</x-nav-link-dashboard>

    @if($tm)
    <div class="mt-5 mx-0 rounded-xl p-3 space-y-1 bg-primary-50">
      <p class="text-[10px] font-bold uppercase tracking-widest text-primary-600">Employee Code</p>
      <p class="text-sm font-bold text-primary-900">{{ $tm->employee_code }}</p>
      <p class="text-[11px] text-primary-700/70">{{ $tm->business?->business_name ?? 'My Team' }}</p>
    </div>
    @endif
    @endrole

  </nav>

  <!-- Sidebar footer: user card -->
  <div class="p-3 border-t border-slate-200 shrink-0">
    <div class="bg-slate-50 rounded-xl p-3 flex items-center gap-3">
      @if(auth()->user()->photo)
        <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-8 h-8 rounded-full object-cover shrink-0">
      @else
        <span class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </span>
      @endif
      <div class="min-w-0 flex-1">
        <p class="text-xs font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
        <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="shrink-0">
        @csrf
        <button type="submit" title="{{ __('layout/dashboard.sign_out') }}" class="text-slate-400 hover:text-red-500 transition p-1 rounded-md hover:bg-red-50">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </button>
      </form>
    </div>
  </div>
</aside>

<!-- Mobile overlay -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden" style="display:none"></div>

<!-- ════════════════════════════════════════
     MAIN CONTENT
═════════════════════════════════════════ -->
<div class="lg:ps-60 min-h-screen flex flex-col">

  <!-- Topbar -->
  <header class="sticky top-0 z-20 h-16 bg-white border-b border-slate-200 flex items-center px-4 lg:px-6 gap-3">
    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ms-2 text-slate-600 hover:bg-slate-100 rounded-lg transition">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    <div class="flex-1 min-w-0">
      <h1 class="text-base font-semibold text-slate-900 truncate">@yield('title', __('layout/dashboard.title'))</h1>
    </div>

    <div class="flex items-center gap-2">
      <!-- View Website -->
      <a href="{{ url('/') }}" target="_blank" title="{{ __('layout/dashboard.view_website') }}" 
         class="p-2 text-slate-400 hover:text-primary-600 rounded-lg hover:bg-slate-50 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
      </a>

      <!-- Notifications -->
      @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
      <div class="relative" x-data="{
          open: false,
          notifications: [],
          unreadCount: {{ $unreadCount }},
          loading: false,
          perPage: 8,
          hasMore: false,
          fetchNotifications() {
              this.loading = true;
              fetch('{{ route('notifications.index') }}?per_page=' + this.perPage, {
                  headers: {
                      'Accept': 'application/json',
                      'X-Requested-With': 'XMLHttpRequest'
                  }
              })
                  .then(res => res.json())
                  .then(data => {
                      this.notifications = data.data;
                      this.unreadCount = data.unread_count;
                      this.hasMore = data.has_more;
                      this.loading = false;
                  })
                  .catch(err => {
                      this.loading = false;
                      console.error(err);
                  });
          },
          markAllRead() {
              fetch('{{ route('notifications.read-all') }}', {
                  method: 'POST',
                  headers: {
                      'Accept': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  }
              })
              .then(res => res.json())
              .then(data => {
                  if (data.success) {
                      this.unreadCount = 0;
                      this.notifications.forEach(n => n.read_at = new Date());
                  }
              });
          },
          markRead(notif) {
              if (notif.read_at) return;
              fetch('/notifications/' + notif.id + '/read', {
                  method: 'POST',
                  headers: {
                      'Accept': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  }
              })
              .then(res => res.json())
              .then(data => {
                  if (data.success) {
                      notif.read_at = new Date();
                      this.unreadCount = data.unread_count;
                  }
              });
          }
      }">
        <button @click="open = !open; if(open) { perPage = 8; fetchNotifications(); }" class="relative p-2 text-slate-400 hover:text-primary-600 rounded-lg hover:bg-slate-50 transition">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span x-show="unreadCount > 0" class="absolute top-1.5 end-1.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white ring-1 ring-rose-300 animate-pulse"></span>
        </button>
        <div x-show="open" @click.away="open = false" x-transition
             class="absolute end-0 mt-2 w-80 bg-white rounded-2xl shadow-lg border border-slate-200 z-50 overflow-hidden" style="display:none">
          <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('layout/dashboard.notifications') }}</span>
            <span @click="markAllRead()" class="text-[10px] font-bold text-primary-600 uppercase cursor-pointer hover:text-primary-700 transition select-none">
              {{ __('layout/dashboard.mark_all_read') }}
            </span>
          </div>

          <!-- Loading state -->
          <div x-show="loading && notifications.length === 0" class="p-8 text-center">
            <svg class="w-6 h-6 animate-spin text-primary-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p class="text-xs text-slate-400">Loading...</p>
          </div>

          <!-- Notifications List -->
          <div x-show="notifications.length > 0">
            <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
              <template x-for="notif in notifications" :key="notif.id">
                <a :href="notif.data.url || '#'" 
                   @click="markRead(notif)"
                   class="block px-4 py-3 hover:bg-slate-50 transition border-s-4"
                   :class="notif.read_at ? 'border-transparent' : 'border-primary-500 bg-primary-50/10'">
                  <div class="flex justify-between items-start gap-2">
                    <p class="text-xs font-bold text-slate-900" x-text="notif.data.title || 'Notification'"></p>
                    <span x-show="!notif.read_at" class="w-1.5 h-1.5 bg-primary-500 rounded-full shrink-0 mt-1"></span>
                  </div>
                  <p class="text-[11px] text-slate-500 mt-0.5" x-text="notif.data.body || ''"></p>
                  <span class="text-[9px] text-slate-400 mt-1 block" x-text="new Date(notif.created_at).toLocaleDateString() + ' ' + new Date(notif.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                </a>
              </template>
            </div>
            
            <!-- View All Link -->
            <div class="p-2 border-t border-slate-100 bg-slate-50/50 text-center">
              <a href="{{ route('notifications.index') }}" class="block text-xs font-bold text-primary-600 hover:text-primary-700 w-full py-1 select-none">
                View All
              </a>
            </div>
          </div>

          <!-- Empty state -->
          <div x-show="!loading && notifications.length === 0" class="p-8 text-center">
            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
              <svg class="w-6 h-6 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </div>
            <p class="text-xs text-slate-500">{{ __('layout/dashboard.no_new_notifications') }}</p>
          </div>
        </div>
      </div>

      <!-- Language Switcher -->
      <div class="relative" x-data="{ open: false }" @click.outside="open = false">
        <button @click="open = !open"
                class="flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition text-slate-700">
          <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          <span class="text-xs font-bold uppercase tracking-wide">{{ $currentLang['short'] }}</span>
          <svg class="w-3 h-3 text-slate-400 transition-transform" :class="open && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
        <div x-show="open" x-transition
             class="absolute end-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-slate-200 z-50 py-1 overflow-hidden" style="display:none">
          @foreach($langs as $code => $lang)
          <form method="POST" action="{{ route('lang.switch', $code) }}" class="m-0 p-0 block">
            @csrf
            <button type="submit" dir="{{ $lang['dir'] }}"
                    class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold hover:bg-primary-50 hover:text-primary-600 transition {{ $locale === $code ? 'text-primary-600 bg-primary-50/50' : 'text-slate-700' }}">
              <span>{{ $lang['label'] }}</span>
              @if($locale === $code)
                <svg class="w-3.5 h-3.5 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              @else
                <span class="text-[9px] text-slate-400 uppercase tracking-wider">{{ $lang['short'] }}</span>
              @endif
            </button>
          </form>
          @endforeach
        </div>
      </div>

      <!-- Avatar Dropdown -->
      <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center p-0.5 rounded-full border-2 border-transparent hover:border-primary-200 transition">
          @if(auth()->user()->photo)
            <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-8 h-8 rounded-full object-cover">
          @else
            <span class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-xs font-black uppercase">
              {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
          @endif
        </button>
        <div x-show="open" @click.away="open = false" x-transition
             class="absolute end-0 mt-2 w-56 bg-white rounded-2xl shadow-lg border border-slate-200 z-50 overflow-hidden" style="display:none">
          <div class="px-4 py-3 bg-slate-50/50 border-b border-slate-100">
            <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
            <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
          </div>
          <div class="py-1">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-primary-50 hover:text-primary-700 transition">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              {{ __('layout/dashboard.my_profile') }}
            </a>
          </div>
          <div class="py-1 border-t border-slate-100 bg-slate-50/50">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                {{ __('layout/dashboard.sign_out') }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Page Content -->
  <main class="flex-1 p-4 sm:p-6 lg:p-8">
    @yield('content')
  </main>

</div>

<!-- User Guide Widget -->
@include('components.user-guide')

<x-notify::notify />
<script src="{{ asset('vendor/mckenziearts/laravel-notify/dist/notify.js') }}"></script>
@stack('scripts')
</body>
</html>
