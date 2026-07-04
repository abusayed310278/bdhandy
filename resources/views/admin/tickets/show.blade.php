@extends('layouts.dashboard')
@section('title', 'Ticket — ' . $ticket->ticket_number)

@section('content')
<div class="space-y-6 text-sm">

  {{-- Top Navigation Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-primary-600 font-semibold mb-1 transition">
        <svg class="w-3.5 h-3.5 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to Ticket Queue
      </a>
      <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
        {{ $ticket->subject }}
      </h2>
      <p class="text-xs text-slate-400 mt-0.5">Ticket ID: <span class="font-semibold text-slate-600">{{ $ticket->ticket_number }}</span></p>
    </div>

    @php
      $sc = ['open'=>'emerald','pending'=>'amber','resolved'=>'blue','closed'=>'slate'][$ticket->status] ?? 'slate';
      $pc = ['low'=>'slate','medium'=>'blue','high'=>'amber','urgent'=>'rose'][$ticket->priority] ?? 'slate';
    @endphp
    <div class="flex items-center gap-2">
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $pc }}-50 text-{{ $pc }}-600 border border-{{ $pc }}-200">
        {{ $ticket->priority }} Priority
      </span>
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $sc }}-50 text-{{ $sc }}-600 border border-{{ $sc }}-200">
        Status: {{ $ticket->status }}
      </span>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-emerald-700 text-xs font-semibold shadow-xs">
      {{ session('success') }}
    </div>
  @endif

  {{-- Two Column Grid --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    
    {{-- Left column: Conversation & Reply --}}
    <div class="lg:col-span-2 space-y-6">
      
      {{-- Original Ticket Description --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center border border-slate-200">
              {{ strtoupper(substr($ticket->user?->name ?? 'G', 0, 2)) }}
            </div>
            <div>
              <p class="font-bold text-slate-900 text-sm">{{ $ticket->user?->name ?? 'Guest Submitter' }}</p>
              <p class="text-[10px] text-slate-400">Submitted {{ $ticket->created_at->format('M d, Y \a\t h:i A') }} ({{ $ticket->created_at->diffForHumans() }})</p>
            </div>
          </div>
          <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg capitalize">{{ $ticket->department }}</span>
        </div>
        <p class="text-slate-700 leading-relaxed whitespace-pre-line text-[13px]">{{ $ticket->description }}</p>
      </div>

      {{-- Thread Messages --}}
      @if($ticket->messages->isNotEmpty())
        <div class="space-y-4">
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Conversation Log</p>

          @foreach($ticket->messages as $msg)
            @php
              $isUser = $msg->sender_id === $ticket->user_id;
              $senderName = $msg->sender?->name ?? 'System';
              
              // Resolve roles/tags for sender
              $senderRole = 'Customer';
              if ($msg->sender) {
                  if ($msg->sender->hasRole(['admin', 'super_admin', 'support', 'moderator'])) {
                      $senderRole = 'Staff';
                  } elseif ($msg->sender->hasRole(['freelancer', 'business'])) {
                      $senderRole = 'Provider';
                  }
              }
            @endphp
            <div class="bg-white rounded-2xl border shadow-sm p-5 transition {{ !$isUser ? 'border-primary-100 bg-primary-50/15' : 'border-slate-200' }}">
              <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-100/50">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center border {{ !$isUser ? 'bg-primary-500 text-white border-primary-600 shadow-soft' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                    {{ strtoupper(substr($senderName, 0, 2)) }}
                  </div>
                  <div>
                    <p class="font-bold text-slate-900 text-xs">{{ $senderName }}</p>
                    <p class="text-[10px] text-slate-400">{{ $msg->created_at->diffForHumans() }} · {{ $msg->created_at->format('M d, h:i A') }}</p>
                  </div>
                </div>
                
                <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ !$isUser ? 'bg-primary-100 text-primary-700 border border-primary-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                  {{ $senderRole }}
                </span>
              </div>
              <p class="text-slate-700 leading-relaxed whitespace-pre-line text-xs">{{ $msg->message }}</p>
            </div>
          @endforeach
        </div>
      @endif

      {{-- Reply Panel --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
          <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
          <h3 class="font-bold text-slate-800 text-sm">Post Response</h3>
        </div>
        
        <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" class="space-y-4">
          @csrf
          <textarea name="message" rows="5" required
            placeholder="Write your professional response to the user..."
            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-xs focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition resize-none"></textarea>
          
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div class="flex items-center gap-2">
              <label class="text-xs font-semibold text-slate-500 shrink-0">Set status to:</label>
              <select name="status" class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs focus:border-primary-500 focus:outline-none transition">
                <option value="pending">Pending Response (User's turn)</option>
                <option value="open">Open (Active investigation)</option>
                <option value="resolved">Resolved (Completed/Solved)</option>
                <option value="closed">Closed (Permanently Closed)</option>
              </select>
            </div>

            <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition active:scale-95 shadow-soft">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
              Publish Response
            </button>
          </div>
        </form>
      </div>

    </div>

    {{-- Right column: Settings, Assignment, Details --}}
    <div class="space-y-6">
      
      {{-- Assigned Agent Panel --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Assigned Agent</h3>
        
        <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST" class="space-y-3">
          @csrf
          <div>
            <select name="assigned_to" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
              <option value="">-- Unassigned --</option>
              @foreach($agents as $agent)
                <option value="{{ $agent->id }}" @selected($ticket->assigned_to === $agent->id)>
                  {{ $agent->name }} ({{ $agent->roles->first()?->name ?? 'Staff' }})
                </option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-4 py-2 text-xs font-bold bg-slate-100 hover:bg-primary-50 hover:text-primary-700 text-slate-700 rounded-lg transition active:scale-95">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Update Assignment
          </button>
        </form>
      </div>

      {{-- Standalone Status Panel --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Quick Status Update</h3>
        
        <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST" class="space-y-3">
          @csrf
          <div>
            <select name="status" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
              @foreach(['open' => 'Open', 'pending' => 'Pending Response', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $k => $v)
                <option value="{{ $k }}" @selected($ticket->status === $k)>{{ $v }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-4 py-2 text-xs font-bold bg-slate-100 hover:bg-primary-50 hover:text-primary-700 text-slate-700 rounded-lg transition active:scale-95">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Save Ticket Status
          </button>
        </form>
      </div>

      {{-- Submitter Profile Card --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3.5">
        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Submitter Details</h3>
        
        @if($ticket->user)
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-400">Name</span>
              <span class="font-bold text-slate-900">{{ $ticket->user->name }}</span>
            </div>
            
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-400">Email</span>
              <a href="mailto:{{ $ticket->user->email }}" class="text-primary-600 hover:underline font-medium text-xs">{{ $ticket->user->email }}</a>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold text-slate-400">Account Type</span>
              @if($ticket->user->isProvider())
                <span class="text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200 rounded px-1.5 py-0.2">Provider</span>
              @else
                <span class="text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded px-1.5 py-0.2">Customer</span>
              @endif
            </div>

            @if($ticket->user->phone)
              <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400">Phone</span>
                <span class="font-medium text-slate-900">{{ $ticket->user->phone }}</span>
              </div>
            @endif
          </div>
        @else
          <p class="text-xs text-slate-400 italic">User account information unavailable.</p>
        @endif
      </div>

      {{-- Ticket Information Summary --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Ticket Summary</h3>
        
        <div class="space-y-2 text-xs">
          <div class="flex justify-between">
            <span class="text-slate-400 font-semibold">Priority:</span>
            <span class="font-bold capitalize text-slate-900">{{ $ticket->priority }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400 font-semibold">Department:</span>
            <span class="font-bold capitalize text-slate-900">{{ $ticket->department }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400 font-semibold">Created On:</span>
            <span class="font-medium text-slate-900">{{ $ticket->created_at->format('M d, Y h:i A') }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400 font-semibold">Last Update:</span>
            <span class="font-medium text-slate-900">{{ $ticket->updated_at->format('M d, Y h:i A') }}</span>
          </div>
        </div>
      </div>

    </div>

  </div>

</div>
@endsection
