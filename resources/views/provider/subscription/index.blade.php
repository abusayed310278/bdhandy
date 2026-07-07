@extends('layouts.dashboard')
@section('title', 'Subscription')

@section('content')
<div class="space-y-8">

  @if(session('success'))
    <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
  @endif
  @if(session('info'))
    <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">{{ session('info') }}</div>
  @endif
  @if(session('error'))
    <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
  @endif

  {{-- Wallet balance strip --}}
  <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 px-5 py-3">
    <div>
      <p class="text-xs text-slate-500">Wallet balance</p>
      <p class="text-lg font-bold text-slate-900">৳{{ number_format($walletBalance, 2) }}</p>
    </div>
    <a href="{{ route('provider.wallet.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 underline">Add Balance →</a>
  </div>

  {{-- Past Due Banner --}}
  @if(!$subscription && $pastDue)
  <div class="bg-red-50 border border-red-200 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <p class="text-xs font-semibold uppercase tracking-wider text-red-600 mb-1">Payment Failed</p>
      <h2 class="text-xl font-bold text-slate-900">{{ $pastDue->plan->name }} — access paused</h2>
      <p class="text-sm text-slate-600 mt-1">Your wallet balance wasn't enough to renew this plan. Add balance and renew to restore access to your dashboard.</p>
    </div>
    <form method="POST" action="{{ route('provider.subscription.checkout') }}">
      @csrf
      <input type="hidden" name="plan_id" value="{{ $pastDue->plan_id }}">
      <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition shadow-soft whitespace-nowrap">
        Renew Now — ৳{{ number_format($pastDue->plan->price, 0) }}
      </button>
    </form>
  </div>
  @endif

  {{-- Current Plan Banner --}}
  @if($subscription)
  <div class="bg-gradient-to-r from-primary-50 to-accent-50 rounded-2xl border border-primary-100 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <p class="text-xs font-semibold uppercase tracking-wider text-primary-600 mb-1">Current Plan</p>
      <h2 class="text-2xl font-bold text-slate-900">{{ $subscription->plan->name }}</h2>
      @if($subscription->end_date)
        <p class="text-sm text-slate-600 mt-1">
          Valid until <strong>{{ $subscription->end_date->format('M d, Y') }}</strong>
          ({{ $subscription->end_date->diffForHumans() }})
        </p>
        @if($subscription->auto_renew && $subscription->next_billing_at)
          <p class="text-xs text-slate-500 mt-0.5">
            Next auto-charge <strong>{{ $subscription->next_billing_at->format('M d, Y') }}</strong> — ৳{{ number_format($subscription->plan->price, 0) }} from your wallet balance
          </p>
        @endif
      @else
        <p class="text-sm text-slate-600 mt-1">Free plan — valid forever</p>
      @endif
    </div>
    <div class="flex items-center gap-3">
      @if($subscription->subscription_status === 'grace')
        <span class="px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 text-sm font-semibold ring-1 ring-yellow-200">Grace Period</span>
      @else
        <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-sm font-semibold ring-1 ring-green-200">Active</span>
      @endif
    </div>
  </div>

  {{-- Usage Meters --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    @php
      $plan = $subscription->plan;
      $user = auth()->user();
      $profile = $user->providerProfile;
    @endphp
    @foreach([
      ['label' => 'Service Areas', 'used' => $profile?->serviceAreas->count() ?? 0, 'limit' => $plan->service_area_limit, 'icon' => 'map-pin'],
      ['label' => 'Gallery Photos', 'used' => $profile?->gallery->count() ?? 0, 'limit' => $plan->gallery_limit, 'icon' => 'image'],
      ['label' => 'Monthly Leads',  'used' => 0, 'limit' => $plan->lead_limit, 'icon' => 'briefcase'],
    ] as $meter)
    <div class="bg-white rounded-xl border border-slate-200 p-4">
      <div class="flex items-center justify-between mb-3">
        <p class="text-sm font-medium text-slate-700">{{ $meter['label'] }}</p>
        <p class="text-xs text-slate-500">
          {{ $meter['used'] }} / {{ $meter['limit'] < 0 ? '∞' : $meter['limit'] }}
        </p>
      </div>
      @php
        $pct = $meter['limit'] > 0 ? min(100, round($meter['used'] / $meter['limit'] * 100)) : 0;
        $color = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-primary-500');
      @endphp
      <div class="w-full bg-slate-100 rounded-full h-1.5">
        <div class="{{ $color }} h-1.5 rounded-full transition-all" style="width: {{ $meter['limit'] < 0 ? 0 : $pct }}%"></div>
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div class="bg-accent-50 border border-accent-200 rounded-2xl p-6 text-center">
    <p class="text-lg font-semibold text-slate-900 mb-1">No active subscription</p>
    <p class="text-sm text-slate-600">Choose a plan below to unlock access to your dashboard and leads.</p>
  </div>
  @endif

  {{-- Plan Grid --}}
  <div>
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Available Plans</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 overflow-x-auto">
      @foreach($plans as $plan)
      @php $isCurrent = $subscription && $subscription->plan_id === $plan->id; @endphp
      <div class="bg-white rounded-2xl border p-5 flex flex-col gap-4
                  {{ $plan->is_featured ? 'border-accent-300 ring-2 ring-accent-200' : 'border-slate-200' }}">

        @if($plan->is_featured)
          <span class="self-start inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-accent-100 text-accent-700">★ Best Value</span>
        @endif

        <div>
          <h4 class="text-base font-bold text-slate-900">{{ $plan->name }}</h4>
          <div class="mt-1 flex items-baseline gap-1">
            @if($plan->price > 0)
              <span class="text-2xl font-bold text-slate-900">{{ $plan->currency->symbol ?? '৳' }}{{ number_format($plan->price, 0) }}</span>
              <span class="text-sm text-slate-500">/ {{ $plan->duration_months }}mo</span>
            @else
              <span class="text-2xl font-bold text-slate-900">Free</span>
            @endif
          </div>
          @if($plan->discount_percent > 0)
            <p class="text-xs text-green-600 font-medium mt-0.5">{{ $plan->discount_percent }}% off</p>
          @endif
        </div>

        <ul class="text-sm text-slate-600 space-y-1.5 flex-1">
          <li class="flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ $plan->lead_limit < 0 ? 'Unlimited' : $plan->lead_limit }} leads/mo
          </li>
          <li class="flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ $plan->service_area_limit < 0 ? 'Unlimited' : $plan->service_area_limit }} service areas
          </li>
          <li class="flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ $plan->gallery_limit < 0 ? 'Unlimited' : $plan->gallery_limit }} gallery photos
          </li>
          @if($plan->is_featured)
          <li class="flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Featured placement
          </li>
          @endif
          @if($plan->is_verified_badge_included)
          <li class="flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Verified badge
          </li>
          @endif
        </ul>

        @if($isCurrent)
          <span class="w-full text-center py-2 rounded-lg bg-primary-50 text-primary-700 text-sm font-semibold">Current Plan</span>
        @else
          <form method="POST" action="{{ route('provider.subscription.checkout') }}">
            @csrf
            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
            <button type="submit" class="w-full py-2 rounded-lg text-sm font-semibold transition
                           {{ $plan->is_featured ? 'bg-accent-500 text-white hover:bg-accent-600' : 'bg-primary-500 text-white hover:bg-primary-600' }}">
              {{ $plan->price > 0 ? 'Upgrade — ' . ($plan->currency->symbol ?? '৳') . number_format($plan->price, 0) : 'Select Free Plan' }}
            </button>
          </form>
        @endif

      </div>
      @endforeach
    </div>
  </div>

  {{-- Invoice History --}}
  @if($invoices->isNotEmpty())
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
      <h3 class="text-sm font-semibold text-slate-900">Subscription History</h3>
    </div>
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-50 border-b border-slate-100">
          <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">Plan</th>
          <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">Started</th>
          <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">Ends</th>
          <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($invoices as $sub)
        <tr class="hover:bg-slate-50/50 transition">
          <td class="px-5 py-3 font-medium text-slate-900">{{ $sub->plan->name }}</td>
          <td class="px-5 py-3 text-slate-500">{{ $sub->start_date->format('M d, Y') }}</td>
          <td class="px-5 py-3 text-slate-500">{{ $sub->end_date ? $sub->end_date->format('M d, Y') : 'Forever' }}</td>
          <td class="px-5 py-3">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
              {{ $sub->subscription_status === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">
              {{ ucfirst($sub->subscription_status) }}
            </span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

</div>
@endsection
