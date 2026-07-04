@extends('layouts.dashboard')
@section('title', 'Messages')

@section('content')
<div class="space-y-5 text-sm">

  <div>
    <h2 class="text-xl font-bold text-slate-900">Messages</h2>
    <p class="text-slate-500 text-xs mt-0.5">Conversations with your customers</p>
  </div>

  @if($conversations->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      <p class="text-slate-500 font-medium">No conversations yet</p>
      <p class="text-slate-400 text-xs mt-1">Conversations start when a customer contacts you about a request</p>
    </div>
  @else
    <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
      @foreach($conversations as $conv)
      @php
        $lastMsg   = $conv->messages->first();
        $customer  = $conv->customer;
        $unread    = $conv->messages->where('sender_id', '!=', auth()->id())->where('is_read', false)->count();
      @endphp
      <a href="{{ route('provider.conversations.show', $conv) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50/80 transition">
        <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center shrink-0">
          {{ strtoupper(substr($customer?->name ?? '?', 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between gap-2">
            <p class="font-semibold text-slate-900 truncate">{{ $customer?->name ?? 'Customer' }}</p>
            <p class="text-[11px] text-slate-400 shrink-0">{{ $lastMsg ? $lastMsg->created_at->diffForHumans() : $conv->created_at->diffForHumans() }}</p>
          </div>
          @if($lastMsg)
            <p class="text-xs text-slate-400 truncate mt-0.5 {{ $unread > 0 ? 'font-semibold text-slate-700' : '' }}">
              {{ $lastMsg->sender_id === auth()->id() ? 'You: ' : '' }}{{ Str::limit($lastMsg->message, 60) }}
            </p>
          @endif
        </div>
        @if($unread > 0)
          <span class="w-5 h-5 rounded-full bg-primary-500 text-white text-[10px] font-bold flex items-center justify-center shrink-0">{{ $unread }}</span>
        @endif
      </a>
      @endforeach
    </div>
  @endif
</div>
@endsection
