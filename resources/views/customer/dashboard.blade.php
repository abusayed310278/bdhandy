@extends('layouts.dashboard')
@section('title', __('customer/dashboard.title'))

@section('content')
<div class="space-y-6 text-sm">

  {{-- Welcome --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-semibold text-slate-900">{{ __('customer/dashboard.welcome_back', ['name' => explode(' ', $user->name)[0]]) }}</h2>
      <p class="text-sm text-slate-500 mt-0.5">{{ __('customer/dashboard.account_subtitle') }}</p>
    </div>
    <a href="{{ route('categories') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition self-start sm:self-auto">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      {{ __('customer/dashboard.find_provider') }}
    </a>
  </div>

  {{-- KPI Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('customer.requests.index') }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-200 hover:shadow-soft transition">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-primary-50 text-primary-500 flex items-center justify-center">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
        </span>
        <span class="text-2xl font-bold text-slate-900">{{ $stats['active_requests'] }}</span>
      </div>
      <p class="text-xs font-semibold text-slate-600 group-hover:text-primary-700 transition">{{ __('customer/dashboard.active_requests') }}</p>
    </a>

    <a href="{{ route('customer.requirements.index') }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-200 hover:shadow-soft transition">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-accent-50 text-accent-500 flex items-center justify-center">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        </span>
        <span class="text-2xl font-bold text-slate-900">{{ $stats['open_requirements'] }}</span>
      </div>
      <p class="text-xs font-semibold text-slate-600 group-hover:text-primary-700 transition">{{ __('customer/dashboard.open_requirements') }}</p>
    </a>

    <a href="{{ route('customer.conversations.index') }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-200 hover:shadow-soft transition">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-primary-50 text-primary-500 flex items-center justify-center">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </span>
        <span class="text-2xl font-bold {{ $unreadMessages > 0 ? 'text-primary-600' : 'text-slate-900' }}">{{ $unreadMessages }}</span>
      </div>
      <p class="text-xs font-semibold text-slate-600 group-hover:text-primary-700 transition">{{ __('customer/dashboard.unread_messages') }}</p>
    </a>

    <a href="{{ route('customer.saved.index') }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-200 hover:shadow-soft transition">
      <div class="flex items-center justify-between mb-3">
        <span class="w-9 h-9 rounded-xl bg-red-50 text-red-400 flex items-center justify-center">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
        </span>
        <span class="text-2xl font-bold text-slate-900">{{ $stats['saved_providers'] }}</span>
      </div>
      <p class="text-xs font-semibold text-slate-600 group-hover:text-primary-700 transition">{{ __('customer/dashboard.saved_providers') }}</p>
    </a>
  </div>

  {{-- Two-column layout --}}
  <div class="grid lg:grid-cols-2 gap-5">

    {{-- Recent Requests --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">{{ __('customer/dashboard.recent_requests') }}</h3>
        <a href="{{ route('customer.requests.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">{{ __('customer/dashboard.view_all') }}</a>
      </div>
      @if($recentRequests->isEmpty())
        <div class="p-8 text-center">
          <p class="text-xs text-slate-400">{{ __('customer/dashboard.no_requests') }}</p>
          <p class="text-[11px] text-slate-300 mt-0.5">{{ __('customer/dashboard.no_requests_hint') }}</p>
        </div>
      @else
        <div class="divide-y divide-slate-100">
          @foreach($recentRequests as $req)
          @php
            $colors = ['pending'=>'yellow','accepted'=>'primary','in_progress'=>'primary','completed'=>'green','cancelled'=>'slate','disputed'=>'red','expired'=>'slate'];
            $rc = $colors[$req->request_status] ?? 'slate';
          @endphp
          <a href="{{ route('customer.requests.show', $req) }}" class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50 transition">
            <div class="min-w-0 flex-1">
              <p class="font-medium text-slate-900 truncate">{{ $req->title }}</p>
              <p class="text-[11px] text-slate-400 mt-0.5">{{ $req->provider?->providerProfile?->business_name ?? '—' }}</p>
            </div>
            <span class="ms-3 shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $rc }}-50 text-{{ $rc }}-700">
              {{ str_replace('_', ' ', $req->request_status) }}
            </span>
          </a>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Open Requirements --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">{{ __('customer/dashboard.open_requirements_head') }}</h3>
        <a href="{{ route('customer.requirements.create') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">{{ __('customer/dashboard.post_new') }}</a>
      </div>
      @if($recentRequirements->isEmpty())
        <div class="p-8 text-center">
          <p class="text-xs text-slate-400">{{ __('customer/dashboard.no_requirements') }}</p>
          <a href="{{ route('customer.requirements.create') }}" class="mt-2 inline-block text-xs text-primary-600 hover:underline">{{ __('customer/dashboard.post_requirement_link') }}</a>
        </div>
      @else
        <div class="divide-y divide-slate-100">
          @foreach($recentRequirements as $req)
          <a href="{{ route('customer.requirements.show', $req) }}" class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50 transition">
            <div class="min-w-0 flex-1">
              <p class="font-medium text-slate-900 truncate">{{ $req->title }}</p>
              <p class="text-[11px] text-slate-400 mt-0.5">{{ trans_choice('customer/dashboard.proposals_count', $req->proposals->count(), ['count' => $req->proposals->count()]) }} · {{ $req->created_at->diffForHumans() }}</p>
            </div>
            <span class="ms-3 shrink-0 text-[11px] text-primary-600 font-semibold">
              {{ $req->proposals->count() > 0 ? trans_choice('customer/dashboard.proposals_count', $req->proposals->count(), ['count' => $req->proposals->count()]) : __('customer/dashboard.no_bids') }}
            </span>
          </a>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- Quick Action Buttons --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <a href="{{ route('customer.requirements.create') }}" class="flex flex-col items-center gap-2 bg-white rounded-2xl border border-slate-200 p-4 hover:border-primary-200 hover:bg-primary-50/40 transition text-center">
      <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/></svg>
      </div>
      <span class="text-[11px] font-semibold text-slate-700">{{ __('customer/dashboard.post_requirement') }}</span>
    </a>
    <a href="{{ route('categories') }}" class="flex flex-col items-center gap-2 bg-white rounded-2xl border border-slate-200 p-4 hover:border-primary-200 hover:bg-primary-50/40 transition text-center">
      <div class="w-10 h-10 rounded-xl bg-accent-100 text-accent-600 flex items-center justify-center">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </div>
      <span class="text-[11px] font-semibold text-slate-700">{{ __('customer/dashboard.browse_services') }}</span>
    </a>
    <a href="{{ route('customer.addresses.create') }}" class="flex flex-col items-center gap-2 bg-white rounded-2xl border border-slate-200 p-4 hover:border-primary-200 hover:bg-primary-50/40 transition text-center">
      <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      </div>
      <span class="text-[11px] font-semibold text-slate-700">{{ __('customer/dashboard.add_address') }}</span>
    </a>
    <a href="{{ route('customer.tickets.create') }}" class="flex flex-col items-center gap-2 bg-white rounded-2xl border border-slate-200 p-4 hover:border-primary-200 hover:bg-primary-50/40 transition text-center">
      <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </div>
      <span class="text-[11px] font-semibold text-slate-700">{{ __('customer/dashboard.get_support') }}</span>
    </a>
  </div>

</div>
@endsection
