@extends('layouts.dashboard')
@section('title', 'Messages')

@section('content')
<div class="space-y-5 text-sm">
  <div>
    <h2 class="text-xl font-bold text-slate-900">Messages</h2>
    <p class="text-slate-500 text-xs mt-0.5">Conversations with your service providers</p>
  </div>

  @if($conversations->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      <p class="text-slate-500 font-medium">No conversations yet</p>
      <p class="text-slate-400 text-xs mt-1">When a provider accepts your request, a conversation will appear here</p>
    </div>
  @else
    <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
      @foreach($conversations as $conv)
        @php
          $lastMsg = $conv->messages->first();
          $provider = $conv->provider?->providerProfile;
        @endphp
        <a href="{{ route('customer.conversations.show', $conv) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition">
          @if($provider?->logo)
            <img src="{{ asset('storage/'.$provider->logo) }}" class="w-11 h-11 rounded-full object-cover shrink-0">
          @else
            <div class="w-11 h-11 rounded-full bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center shrink-0">
              {{ strtoupper(substr($provider?->business_name ?? $conv->provider?->name ?? '?', 0, 2)) }}
            </div>
          @endif
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
              <p class="font-semibold text-slate-900 truncate">{{ $provider?->business_name ?? $conv->provider?->name ?? 'Provider' }}</p>
              @if($lastMsg)
                <span class="text-[11px] text-slate-400 shrink-0">{{ $lastMsg->created_at->diffForHumans(null, true) }}</span>
              @endif
            </div>
            @if($lastMsg)
              <p class="text-xs text-slate-400 truncate mt-0.5">{{ $lastMsg->message }}</p>
            @endif
          </div>
          <svg class="w-4 h-4 text-slate-300 shrink-0 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection
