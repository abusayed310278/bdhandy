@extends('layouts.dashboard')
@section('title', __('admin/dashboard.title'))

@section('content')
<div class="space-y-6">

  {{-- KPI Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach([
      ['label' => __('admin/dashboard.total_providers'),      'value' => number_format($stats['total_providers']), 'icon' => 'tool',   'color' => 'text-primary-500',  'bg' => 'bg-primary-50'],
      ['label' => __('admin/dashboard.total_customers'),      'value' => number_format($stats['total_customers']), 'icon' => 'users',  'color' => 'text-green-500',    'bg' => 'bg-green-50'],
      ['label' => __('admin/dashboard.pending_verif'),        'value' => $stats['pending_verif'],                  'icon' => 'shield', 'color' => 'text-accent-500',   'bg' => 'bg-accent-50'],
      ['label' => __('admin/dashboard.active_subscriptions'), 'value' => number_format($stats['active_subs']),     'icon' => 'star',   'color' => 'text-purple-500',   'bg' => 'bg-purple-50'],
    ] as $kpi)
    <div class="bg-white rounded-xl border border-slate-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <p class="text-sm font-medium text-slate-500">{{ $kpi['label'] }}</p>
        <span class="w-9 h-9 rounded-lg {{ $kpi['bg'] }} flex items-center justify-center {{ $kpi['color'] }}">
          @if($kpi['icon'] === 'tool')
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          @elseif($kpi['icon'] === 'users')
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          @elseif($kpi['icon'] === 'shield')
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          @else
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          @endif
        </span>
      </div>
      <p class="text-3xl font-bold text-slate-900">{{ $kpi['value'] }}</p>
    </div>
    @endforeach
  </div>

  {{-- Pending Verification Queue --}}
  @if($recentProviders->isNotEmpty())
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/50">
      <div>
        <h3 class="text-sm font-semibold text-slate-900">{{ __('admin/dashboard.pending_verification') }}</h3>
        <p class="text-xs text-slate-500 mt-0.5">{{ __('admin/dashboard.providers_awaiting') }}</p>
      </div>
      <a href="{{ route('admin.providers.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
        {{ __('admin/dashboard.view_all') }} <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>
    <div class="divide-y divide-slate-100">
      @foreach($recentProviders as $provider)
      <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/50 transition">
        <div class="flex items-center gap-3">
          <span class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-sm shrink-0">
            {{ substr($provider->business_name, 0, 1) }}
          </span>
          <div>
            <p class="text-sm font-semibold text-slate-900">{{ $provider->business_name }}</p>
            <p class="text-xs text-slate-500">{{ $provider->user->email }} · {{ $provider->updated_at->diffForHumans() }}</p>
          </div>
        </div>
        <a href="{{ route('admin.providers.show', $provider) }}"
           class="text-xs font-semibold text-primary-600 hover:text-primary-700 px-3 py-1.5 rounded-lg hover:bg-primary-50 transition">
          {{ __('admin/dashboard.review') }}
        </a>
      </div>
      @endforeach
    </div>
  </div>
  @else
  <div class="bg-white rounded-xl border border-slate-200 p-10 text-center">
    <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-3">
      <svg class="w-6 h-6 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <p class="text-sm font-medium text-slate-900">{{ __('admin/dashboard.all_clear') }}</p>
    <p class="text-xs text-slate-500 mt-1">{{ __('admin/dashboard.no_pending') }}</p>
  </div>
  @endif

</div>
@endsection
