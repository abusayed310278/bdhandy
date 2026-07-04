@extends('layouts.dashboard')
@section('title', 'Ticket — ' . $ticket->ticket_number)

@section('content')
<div class="max-w-3xl space-y-5 text-sm">

  <div class="flex items-start justify-between gap-3">
    <div>
      <a href="{{ route('provider.tickets.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1">
        <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        Support Tickets
      </a>
      <h2 class="text-xl font-bold text-slate-900">{{ $ticket->subject }}</h2>
      <p class="text-xs text-slate-400 mt-0.5">{{ $ticket->ticket_number }}</p>
    </div>
    @php
      $sc = ['open'=>'primary','pending'=>'yellow','resolved'=>'green','closed'=>'slate'][$ticket->status] ?? 'slate';
      $pc = ['low'=>'slate','medium'=>'blue','high'=>'yellow','urgent'=>'red'][$ticket->priority] ?? 'slate';
    @endphp
    <div class="flex items-center gap-2 shrink-0">
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-{{ $pc }}-50 text-{{ $pc }}-700">{{ $ticket->priority }}</span>
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-{{ $sc }}-50 text-{{ $sc }}-700">{{ $ticket->status }}</span>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  {{-- Original description --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 font-bold text-xs flex items-center justify-center">
          {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div>
          <p class="font-semibold text-slate-900 text-xs">{{ auth()->user()->name }}</p>
          <p class="text-[10px] text-slate-400">{{ $ticket->created_at->diffForHumans() }}</p>
        </div>
      </div>
      <span class="text-[10px] text-slate-400 capitalize">{{ $ticket->department }}</span>
    </div>
    <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $ticket->description }}</p>
  </div>

  {{-- Thread messages --}}
  @foreach($ticket->messages as $msg)
  @php $isMe = $msg->sender_id === auth()->id(); @endphp
  <div class="bg-white rounded-2xl border {{ $isMe ? 'border-slate-200' : 'border-primary-100 bg-primary-50/30' }} p-5">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full {{ $isMe ? 'bg-slate-100 text-slate-600' : 'bg-primary-500 text-white' }} font-bold text-xs flex items-center justify-center">
          {{ strtoupper(substr($msg->sender?->name ?? 'S', 0, 2)) }}
        </div>
        <div>
          <p class="font-semibold text-slate-900 text-xs">{{ $isMe ? 'You' : ($msg->sender?->name ?? 'Support') }}</p>
          <p class="text-[10px] text-slate-400">{{ $msg->created_at->diffForHumans() }}</p>
        </div>
      </div>
      @if(!$isMe)
        <span class="text-[10px] px-2 py-0.5 rounded-full bg-primary-100 text-primary-700 font-medium">Support</span>
      @endif
    </div>
    <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $msg->message }}</p>
  </div>
  @endforeach

  {{-- Reply box --}}
  @if(!in_array($ticket->status, ['resolved', 'closed']))
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    <h3 class="font-bold text-slate-800 mb-3">Reply</h3>
    <form action="{{ route('provider.tickets.reply', $ticket) }}" method="POST" class="space-y-3">
      @csrf
      <textarea name="message" rows="4" required
        placeholder="Type your reply here…"
        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition resize-none"></textarea>
      <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">Send Reply</button>
      </div>
    </form>
  </div>
  @else
    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 text-center">
      <p class="text-xs text-slate-500">This ticket is {{ $ticket->status }}. <a href="{{ route('provider.tickets.create') }}" class="text-primary-600 hover:underline">Open a new ticket</a> if you need further help.</p>
    </div>
  @endif
</div>
@endsection
