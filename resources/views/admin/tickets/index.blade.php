@extends('layouts.dashboard')
@section('title', 'Support Tickets')

@section('content')
<div class="space-y-6 text-sm">

  {{-- KPI Counters --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
      <div class="flex items-center justify-between mb-2">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Tickets</p>
        <span class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
        </span>
      </div>
      <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['total']) }}</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
      <div class="flex items-center justify-between mb-2">
        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Open Tickets</p>
        <span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        </span>
      </div>
      <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['open']) }}</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
      <div class="flex items-center justify-between mb-2">
        <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Pending Response</p>
        <span class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
      </div>
      <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['pending']) }}</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
      <div class="flex items-center justify-between mb-2">
        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Resolved / Closed</p>
        <span class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
      </div>
      <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['resolved']) }}</p>
    </div>
  </div>

  {{-- Filters Card --}}
  <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
    <form action="{{ route('admin.tickets.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
      
      {{-- Search --}}
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Search</label>
        <div class="relative">
          <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-slate-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </span>
          <input type="text" name="search" value="{{ request('search') }}"
            placeholder="No., subject, user..."
            class="block w-full rounded-lg border border-slate-300 bg-white ps-9 pe-3 py-2 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>
      </div>

      {{-- Status --}}
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
        <select name="status" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          <option value="">All Statuses</option>
          @foreach(['open' => 'Open', 'pending' => 'Pending', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $k => $v)
            <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      {{-- Department --}}
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Department</label>
        <select name="department" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          <option value="">All Departments</option>
          @foreach(['technical' => 'Technical Support', 'billing' => 'Billing & Accounts', 'verification' => 'Verification Desk', 'general' => 'General Inquiries'] as $k => $v)
            <option value="{{ $k }}" @selected(request('department') === $k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      {{-- Priority --}}
      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Priority</label>
        <select name="priority" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          <option value="">All Priorities</option>
          @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $k => $v)
            <option value="{{ $k }}" @selected(request('priority') === $k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      {{-- Buttons --}}
      <div class="flex items-center gap-2">
        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-semibold bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition active:scale-95">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
          Filter
        </button>
        @if(request()->anyFilled(['search', 'status', 'department', 'priority']))
          <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center justify-center p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition" title="Clear Filters">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </a>
        @endif
      </div>
    </form>
  </div>

  {{-- Tickets List Table --}}
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-start border-collapse">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-100 text-start">
            <th class="px-5 py-3 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">Ticket Details</th>
            <th class="px-5 py-3 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">Submitter</th>
            <th class="px-5 py-3 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">Dept. & Priority</th>
            <th class="px-5 py-3 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned Agent</th>
            <th class="px-5 py-3 text-start text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
            <th class="px-5 py-3 text-end text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($tickets as $t)
            @php
              $sc = ['open'=>'emerald','pending'=>'amber','resolved'=>'blue','closed'=>'slate'][$t->status] ?? 'slate';
              $pc = ['low'=>'slate','medium'=>'blue','high'=>'amber','urgent'=>'rose'][$t->priority] ?? 'slate';
              
              $isProvider = $t->user && $t->user->isProvider();
            @endphp
            <tr class="hover:bg-slate-50/50 transition">
              {{-- Details --}}
              <td class="px-5 py-4">
                <div class="max-w-xs sm:max-w-sm">
                  <a href="{{ route('admin.tickets.show', $t) }}" class="font-bold text-slate-900 hover:text-primary-600 transition block text-sm mb-0.5 truncate">
                    {{ $t->subject }}
                  </a>
                  <div class="flex items-center gap-1.5 text-xs text-slate-400">
                    <span class="font-semibold text-slate-600">{{ $t->ticket_number }}</span>
                    <span>·</span>
                    <span>Last updated {{ $t->updated_at->diffForHumans() }}</span>
                  </div>
                </div>
              </td>
              
              {{-- Submitter --}}
              <td class="px-5 py-4">
                <div class="flex flex-col">
                  <span class="font-semibold text-slate-800">{{ $t->user?->name ?? 'Guest' }}</span>
                  <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="text-xs text-slate-400">{{ $t->user?->email }}</span>
                    @if($isProvider)
                      <span class="text-[9px] font-bold uppercase tracking-wider bg-purple-50 text-purple-600 border border-purple-200 px-1.5 py-0.2 rounded">Provider</span>
                    @else
                      <span class="text-[9px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200 px-1.5 py-0.2 rounded">Customer</span>
                    @endif
                  </div>
                </div>
              </td>

              {{-- Dept & Priority --}}
              <td class="px-5 py-4">
                <div class="flex flex-col gap-1 items-start">
                  <span class="text-xs text-slate-600 capitalize bg-slate-100 rounded-md px-2 py-0.5 font-medium">{{ $t->department }}</span>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-{{ $pc }}-50 text-{{ $pc }}-600 border border-{{ $pc }}-200">
                    {{ $t->priority }}
                  </span>
                </div>
              </td>

              {{-- Assigned Agent --}}
              <td class="px-5 py-4 text-xs">
                @if($t->assignedTo)
                  <span class="inline-flex items-center gap-1.5 font-semibold text-slate-700 bg-slate-100 px-2 py-1 rounded-lg">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                    {{ $t->assignedTo->name }}
                  </span>
                @else
                  <span class="inline-flex items-center font-medium text-slate-400 bg-slate-50 px-2 py-1 rounded-lg border border-slate-200 border-dashed">
                    Unassigned
                  </span>
                @endif
              </td>

              {{-- Status --}}
              <td class="px-5 py-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider bg-{{ $sc }}-50 text-{{ $sc }}-600 border border-{{ $sc }}-200">
                  {{ $t->status }}
                </span>
              </td>

              {{-- Actions --}}
              <td class="px-5 py-4 text-end">
                <a href="{{ route('admin.tickets.show', $t) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-primary-50 hover:text-primary-700 rounded-lg text-slate-700 transition">
                  Manage
                  <svg class="w-3.5 h-3.5 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-slate-100">
                  <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <p class="text-sm font-semibold text-slate-700">No Tickets Found</p>
                <p class="text-xs text-slate-400 mt-1">Try tweaking your search term or filter rules.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
      <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $tickets->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
