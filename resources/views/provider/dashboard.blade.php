@extends('layouts.dashboard')
@section('title', __('provider/dashboard.title'))

@section('content')
<div class="space-y-6">

  {{-- Welcome --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-semibold text-slate-900">{{ __('provider/dashboard.welcome_back', ['name' => explode(' ', $user->name)[0]]) }}</h2>
      <p class="text-sm text-slate-500 mt-0.5">
        {{ $profile?->business_name }} ·
        @if($plan)
          <span class="text-primary-600 font-medium">{{ $plan->name }}</span>
        @else
          {{ __('provider/dashboard.no_subscription') }}
        @endif
      </p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('provider.wallet.index') }}" class="flex items-center gap-3 pl-4 pr-2 py-2 rounded-xl bg-white border border-slate-200 hover:border-primary-200 hover:shadow-md transition">
        <div>
          <p class="text-[11px] text-slate-400 leading-none">Wallet Balance</p>
          <p class="text-sm font-bold text-slate-900 mt-0.5">৳{{ number_format($user->wallet_balance, 2) }}</p>
        </div>
        <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-primary-50 text-primary-600 text-xs font-semibold">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add
        </span>
      </a>
      <a href="{{ route('provider.requests.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
        {{ __('provider/dashboard.view_requests') }}
      </a>
    </div>
  </div>

  {{-- Pending verification banner --}}
  @if($profile && $profile->verification_status === 'pending')
  <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-slate-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
      <p class="text-sm font-semibold text-slate-800">{{ __('provider/dashboard.verify_pending_title') }}</p>
      <p class="text-xs text-slate-600 mt-0.5">{{ __('provider/dashboard.verify_pending_body') }}</p>
    </div>
  </div>
  @endif

  @if($profile && $profile->verification_status === 'in_review')
  <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
      <p class="text-sm font-semibold text-yellow-800">{{ __('provider/dashboard.verify_review_title') }}</p>
      <p class="text-xs text-yellow-700 mt-0.5">{{ __('provider/dashboard.verify_review_body') }}</p>
    </div>
  </div>
  @endif

  @if($profile && $profile->verification_status === 'rejected')
  <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    <div>
      <p class="text-sm font-semibold text-red-800">{{ __('provider/dashboard.verify_rejected_title') }}</p>
      <p class="text-xs text-red-700 mt-0.5">{{ __('provider/dashboard.verify_rejected_body') }}</p>
    </div>
  </div>
  @endif

  {{-- Subscription expiry warning --}}
  @if($subscription && $subscription->end_date && $subscription->end_date->diffInDays(now()) <= 7 && $subscription->end_date->isFuture())
  <div class="bg-accent-50 border border-accent-200 rounded-xl p-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-accent-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <div class="flex-1">
      <p class="text-sm font-semibold text-accent-800">{{ __('provider/dashboard.sub_expiry_title') }}</p>
      <p class="text-xs text-accent-700 mt-0.5">{{ __('provider/dashboard.sub_expiry_body', ['plan' => $plan->name, 'date' => $subscription->end_date->diffForHumans()]) }}</p>
    </div>
    <a href="{{ route('provider.subscription.index') }}" class="shrink-0 text-xs font-semibold text-white bg-accent-500 px-3 py-1.5 rounded-lg hover:bg-accent-600 transition">{{ __('provider/dashboard.renew') }}</a>
  </div>
  @endif

  {{-- KPI Row --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('provider.leads.index') }}" class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md hover:border-primary-200 transition">
      <p class="text-sm font-medium text-slate-500">{{ __('provider/dashboard.open_leads') }}</p>
      <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['open_leads'] }}</p>
      <p class="text-xs text-slate-400 mt-1">{{ __('provider/dashboard.active_customer_posts') }}</p>
    </a>
    <a href="{{ route('provider.requests.index', ['tab' => 'pending']) }}" class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md hover:border-primary-200 transition">
      <p class="text-sm font-medium text-slate-500">{{ __('provider/dashboard.pending_requests') }}</p>
      <p class="text-3xl font-bold {{ $stats['pending_requests'] > 0 ? 'text-accent-600' : 'text-slate-900' }} mt-1">{{ $stats['pending_requests'] }}</p>
      <p class="text-xs text-slate-400 mt-1">{{ __('provider/dashboard.awaiting_action') }}</p>
    </a>
    <a href="{{ route('provider.reviews.index') }}" class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md hover:border-primary-200 transition">
      <p class="text-sm font-medium text-slate-500">{{ __('provider/dashboard.avg_rating') }}</p>
      <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['avg_rating'] ?? '—' }}</p>
      <p class="text-xs text-slate-400 mt-1">{{ trans_choice('provider/dashboard.reviews_count', $stats['total_reviews'], ['count' => $stats['total_reviews']]) }}</p>
    </a>
    <a href="{{ route('provider.conversations.index') }}" class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md hover:border-primary-200 transition">
      <p class="text-sm font-medium text-slate-500">{{ __('provider/dashboard.unread_messages') }}</p>
      <p class="text-3xl font-bold {{ $stats['unread_messages'] > 0 ? 'text-primary-600' : 'text-slate-900' }} mt-1">{{ $stats['unread_messages'] }}</p>
      <p class="text-xs text-slate-400 mt-1">{{ __('provider/dashboard.across_conversations') }}</p>
    </a>
  </div>

  {{-- Recent Requests --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h3 class="text-sm font-semibold text-slate-900">{{ __('provider/dashboard.recent_requests') }}</h3>
      <a href="{{ route('provider.requests.index') }}" class="text-xs text-primary-600 hover:underline">{{ __('provider/dashboard.view_all') }}</a>
    </div>
    @if($recentRequests->isEmpty())
      <div class="p-8 text-center">
        <p class="text-slate-400 text-sm">{{ __('provider/dashboard.no_requests') }}</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3">{{ __('provider/dashboard.col_request') }}</th>
              <th class="px-5 py-3">{{ __('provider/dashboard.col_customer') }}</th>
              <th class="px-5 py-3 text-center">{{ __('provider/dashboard.col_status') }}</th>
              <th class="px-5 py-3 text-right">{{ __('provider/dashboard.col_date') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($recentRequests as $req)
            @php
              $statusColors = [
                'pending'     => 'yellow',
                'accepted'    => 'primary',
                'in_progress' => 'blue',
                'completed'   => 'green',
                'cancelled'   => 'slate',
                'disputed'    => 'red',
                'expired'     => 'slate',
              ];
              $sc = $statusColors[$req->request_status] ?? 'slate';
            @endphp
            <tr class="hover:bg-slate-50/50 transition">
              <td class="px-5 py-3.5">
                <a href="{{ route('provider.requests.show', $req) }}" class="font-medium text-slate-900 hover:text-primary-600 transition">{{ Str::limit($req->title, 40) }}</a>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $req->request_number }}</p>
              </td>
              <td class="px-5 py-3.5 text-slate-600">{{ $req->customer?->name ?? '—' }}</td>
              <td class="px-5 py-3.5 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $sc }}-50 text-{{ $sc }}-700">{{ str_replace('_', ' ', $req->request_status) }}</span>
              </td>
              <td class="px-5 py-3.5 text-right text-slate-400 text-xs">{{ $req->created_at->format('d M') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- Quick Links --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach([
      ['title' => __('provider/dashboard.link_services_title'),     'desc' => __('provider/dashboard.link_services_desc'),     'route' => route('provider.services.index'),      'icon' => 'tool',        'color' => 'primary'],
      ['title' => __('provider/dashboard.link_areas_title'),        'desc' => __('provider/dashboard.link_areas_desc'),        'route' => route('provider.areas.index'),          'icon' => 'map-pin',     'color' => 'primary'],
      ['title' => __('provider/dashboard.link_hours_title'),        'desc' => __('provider/dashboard.link_hours_desc'),        'route' => route('provider.hours.index'),          'icon' => 'calendar',    'color' => 'primary'],
      ['title' => __('provider/dashboard.link_leads_title'),        'desc' => __('provider/dashboard.link_leads_desc'),        'route' => route('provider.leads.index'),          'icon' => 'inbox',       'color' => 'accent'],
      ['title' => __('provider/dashboard.link_requests_title'),     'desc' => __('provider/dashboard.link_requests_desc'),     'route' => route('provider.requests.index'),       'icon' => 'clipboard',   'color' => 'accent'],
      ['title' => __('provider/dashboard.link_subscription_title'), 'desc' => __('provider/dashboard.link_subscription_desc'), 'route' => route('provider.subscription.index'),   'icon' => 'credit-card', 'color' => 'primary'],
    ] as $link)
    <a href="{{ $link['route'] }}"
       class="group bg-white rounded-xl border border-slate-200 p-5 flex items-start gap-4 hover:shadow-md hover:border-primary-200 transition">
      <span class="w-10 h-10 rounded-xl bg-{{ $link['color'] === 'accent' ? 'accent' : 'primary' }}-50 text-{{ $link['color'] === 'accent' ? 'accent' : 'primary' }}-500 flex items-center justify-center shrink-0 group-hover:bg-{{ $link['color'] === 'accent' ? 'accent' : 'primary' }}-100 transition">
        @if($link['icon'] === 'tool')
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        @elseif($link['icon'] === 'map-pin')
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        @elseif($link['icon'] === 'calendar')
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        @elseif($link['icon'] === 'inbox')
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
        @elseif($link['icon'] === 'clipboard')
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
        @else
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        @endif
      </span>
      <div class="min-w-0">
        <p class="text-sm font-semibold text-slate-900 group-hover:text-primary-700 transition">{{ $link['title'] }}</p>
        <p class="text-xs text-slate-500 mt-0.5">{{ $link['desc'] }}</p>
      </div>
    </a>
    @endforeach
  </div>

</div>
@endsection
