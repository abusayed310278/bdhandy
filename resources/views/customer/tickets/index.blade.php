@extends('layouts.dashboard')
@section('title', 'Support Tickets')

@section('content')
<div class="space-y-5 text-sm">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Support Tickets</h2>
      <p class="text-slate-500 text-xs mt-0.5">Get help from our support team</p>
    </div>
    <a href="{{ route('customer.tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Ticket
    </a>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  @if($tickets->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      <p class="text-slate-500 font-medium">No tickets yet</p>
      <p class="text-slate-400 text-xs mt-1">Open a ticket and our team will get back to you</p>
      <a href="{{ route('customer.tickets.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">Open Ticket</a>
    </div>
  @else
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Ticket</th>
              <th class="px-5 py-3.5">Department</th>
              <th class="px-5 py-3.5 text-center">Priority</th>
              <th class="px-5 py-3.5 text-center">Status</th>
              <th class="px-5 py-3.5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($tickets as $ticket)
            @php
              $pColors = ['low'=>'slate','medium'=>'blue','high'=>'yellow','urgent'=>'red'];
              $sColors = ['open'=>'primary','pending'=>'yellow','resolved'=>'green','closed'=>'slate'];
              $pc = $pColors[$ticket->priority] ?? 'slate';
              $sc = $sColors[$ticket->status] ?? 'slate';
            @endphp
            <tr class="hover:bg-slate-50/50 transition">
              <td class="px-5 py-4">
                <p class="font-semibold text-slate-900">{{ $ticket->subject }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $ticket->ticket_number }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $ticket->created_at->diffForHumans() }}</p>
              </td>
              <td class="px-5 py-4 text-slate-500 capitalize">{{ $ticket->department }}</td>
              <td class="px-5 py-4 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $pc }}-50 text-{{ $pc }}-700">{{ $ticket->priority }}</span>
              </td>
              <td class="px-5 py-4 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $sc }}-50 text-{{ $sc }}-700">{{ $ticket->status }}</span>
              </td>
              <td class="px-5 py-4 text-right">
                <a href="{{ route('customer.tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100 transition">
                  View <span class="rtl-flip">→</span>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($tickets->hasPages())
        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100">{{ $tickets->links() }}</div>
      @endif
    </div>
  @endif
</div>
@endsection
