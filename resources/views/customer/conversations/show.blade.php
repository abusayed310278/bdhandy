@extends('layouts.dashboard')
@section('title', 'Conversation')

@section('content')
<div class="max-w-3xl space-y-4 text-sm">

  {{-- Header --}}
  <div class="flex items-center gap-3">
    <a href="{{ route('customer.conversations.index') }}" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition">
      <svg class="w-4 h-4 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
    </a>
    @php
      $provider = $conversation->provider?->providerProfile;
    @endphp
    @if($provider?->logo)
      <img src="{{ asset('storage/'.$provider->logo) }}" class="w-9 h-9 rounded-full object-cover">
    @else
      <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center shrink-0">
        {{ strtoupper(substr($provider?->business_name ?? $conversation->provider?->name ?? '?', 0, 2)) }}
      </div>
    @endif
    <div>
      <p class="font-semibold text-slate-900">{{ $provider?->business_name ?? $conversation->provider?->name ?? 'Provider' }}</p>
    </div>
    @if($provider)
    <div class="ms-auto flex items-center gap-3">
      <a href="{{ route('provider.profile.public', $provider->slug) }}" target="_blank"
         class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-bold transition">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View Profile
      </a>
    </div>
    @endif
  </div>

  {{-- Messages --}}
  <div class="bg-white rounded-2xl border border-slate-200 flex flex-col" style="height:calc(100vh - 260px); min-height:400px">

    <div id="msg_list" class="flex-1 overflow-y-auto p-5 space-y-4 scroll-smooth">
      @forelse($messages as $msg)
        @php $isMe = $msg->sender_id === auth()->id(); @endphp
        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}" data-msg-id="{{ $msg->id }}">
          @if(!$isMe)
            <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 font-bold text-[11px] flex items-center justify-center shrink-0 me-2 mt-1">
              {{ strtoupper(substr($msg->sender?->name ?? '?', 0, 2)) }}
            </div>
          @endif
          <div class="max-w-[75%]">
            <div class="px-4 py-2.5 rounded-2xl text-xs leading-relaxed
              {{ $isMe ? 'bg-primary-500 text-white rounded-br-sm' : 'bg-slate-100 text-slate-800 rounded-bl-sm' }}">
              {{ $msg->message }}
            </div>
            <p class="text-[10px] text-slate-400 mt-1 {{ $isMe ? 'text-right' : 'text-left' }}">
              {{ $msg->created_at->format('h:i A') }}
            </p>
          </div>
        </div>
      @empty
        <div class="flex items-center justify-center h-full">
          <p class="text-slate-400 text-xs">No messages yet. Start the conversation!</p>
        </div>
      @endforelse
    </div>

    {{-- Input --}}
    <div class="border-t border-slate-100 p-4" x-data="chat({{ $conversation->id }}, {{ auth()->id() }})">
      <div class="flex items-end gap-3">
        <form id="msg_form" action="{{ route('customer.conversations.message', $conversation) }}" method="POST" class="flex-1 flex items-end gap-3" @submit.prevent="sendMessage">
          @csrf
          <textarea name="message" id="msg_input" rows="1" x-model="newMessage"
            @keydown.enter.prevent="if (!$event.shiftKey) { sendMessage(); }"
            @input="$el.style.height='auto'; $el.style.height=$el.scrollHeight+'px'"
            placeholder="Type a message…"
            class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition resize-none overflow-hidden"
            style="max-height:120px"></textarea>
          <button type="submit" :disabled="!newMessage.trim()"
            class="p-2.5 rounded-xl bg-primary-500 text-white hover:bg-primary-600 transition disabled:opacity-40 shrink-0">
            <svg class="w-4 h-4 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
<script>
  function chat(conversationId, myId) {
    return {
      newMessage: '',

      init() {
        this.scrollToBottom();
        this.subscribeReverb();
      },

      scrollToBottom() {
        const list = document.getElementById('msg_list');
        if (list) list.scrollTop = list.scrollHeight;
      },

      async sendMessage() {
        const text = this.newMessage.trim();
        if (!text) return;

        this.appendMessage({ sender_id: myId, message: text, created_at: new Date().toISOString(), sender_name: '' });
        this.newMessage = '';
        const ta = document.getElementById('msg_input');
        if (ta) { ta.style.height = 'auto'; }

        const form = document.getElementById('msg_form');
        const fd = new FormData(form);
        fd.set('message', text);

        try {
          await fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
            }
          });
        } catch (err) {
          console.error('Failed to send message:', err);
        }
      },

      appendMessage(msg) {
        const list = document.getElementById('msg_list');
        const isMe = msg.sender_id == myId;
        const time = new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        const initials = (msg.sender_name || '?').substring(0, 2).toUpperCase();

        const wrapper = document.createElement('div');
        wrapper.className = 'flex ' + (isMe ? 'justify-end' : 'justify-start');

        wrapper.innerHTML = (!isMe ? `
          <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 font-bold text-[11px] flex items-center justify-center shrink-0 me-2 mt-1">${initials}</div>
        ` : '') + `
          <div class="max-w-[75%]">
            <div class="px-4 py-2.5 rounded-2xl text-xs leading-relaxed ${isMe ? 'bg-primary-500 text-white rounded-br-sm' : 'bg-slate-100 text-slate-800 rounded-bl-sm'}">
              ${msg.message.replace(/</g,'&lt;').replace(/>/g,'&gt;')}
            </div>
            <p class="text-[10px] text-slate-400 mt-1 ${isMe ? 'text-right' : 'text-left'}">${time}</p>
          </div>
        `;

        list.appendChild(wrapper);
        this.scrollToBottom();
      },

      subscribeReverb() {
        const pusher = new Pusher('{{ config('broadcasting.connections.reverb.key') }}', {
          wsHost: '{{ config('broadcasting.connections.reverb.options.host') }}',
          wsPort: {{ config('broadcasting.connections.reverb.options.port') }},
          wssPort: {{ config('broadcasting.connections.reverb.options.port') }},
          forceTLS: {{ config('broadcasting.connections.reverb.options.scheme') === 'https' ? 'true' : 'false' }},
          enabledTransports: ['ws', 'wss'],
          cluster: 'mt1',
          authEndpoint: '/broadcasting/auth',
          auth: { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' } }
        });

        const channel = pusher.subscribe('private-conversation.' + conversationId);
        channel.bind('App\\Events\\MessageSent', (data) => {
          if (data.sender_id != myId) {
            this.appendMessage(data);
          }
        });
      }
    }
  }

  // Scroll to bottom on load
  document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('msg_list');
    if (list) list.scrollTop = list.scrollHeight;
  });
</script>
@endsection
