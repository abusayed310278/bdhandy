@extends('layouts.dashboard')
@section('title', 'Requests')

@section('content')
<div class="space-y-5 text-sm">

  <div>
    <h2 class="text-xl font-bold text-slate-900">Service Requests</h2>
    <p class="text-slate-500 text-xs mt-0.5">Manage requests from your customers</p>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  {{-- Tabs --}}
  <div class="flex gap-1 bg-slate-100 rounded-xl p-1 w-fit">
    @foreach(['pending' => 'Pending', 'active' => 'Active', 'completed' => 'History'] as $t => $label)
    <a href="{{ route('provider.requests.index', ['tab' => $t]) }}"
      class="px-4 py-2 rounded-lg text-xs font-semibold transition
        {{ $tab === $t ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
      {{ $label }}
      @if(isset($counts[$t]) && $counts[$t] > 0)
        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold
          {{ $tab === $t ? 'bg-primary-100 text-primary-700' : 'bg-slate-200 text-slate-600' }}">{{ $counts[$t] }}</span>
      @endif
    </a>
    @endforeach
  </div>

  @if($requests->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
      <p class="text-slate-500 font-medium">No requests in this category</p>
    </div>
  @else
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Request</th>
              <th class="px-5 py-3.5">Customer</th>
              <th class="px-5 py-3.5 text-center">Urgency</th>
              <th class="px-5 py-3.5 text-center">Status</th>
              <th class="px-5 py-3.5 text-center">Invoice</th>
              <th class="px-5 py-3.5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($requests as $req)
            @php
              $statusColors = ['pending'=>'yellow','accepted'=>'primary','in_progress'=>'blue','completed'=>'green','cancelled'=>'slate','disputed'=>'red','expired'=>'slate'];
              $urgColors    = ['emergency'=>'red','urgent'=>'yellow','normal'=>'slate'];
              $sc = $statusColors[$req->request_status] ?? 'slate';
              $uc = $urgColors[$req->urgency] ?? 'slate';
            @endphp
            <tr class="hover:bg-slate-50/50 transition">
              <td class="px-5 py-4">
                <p class="font-semibold text-slate-900">{{ Str::limit($req->title, 45) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $req->request_number }}</p>
                @if($req->preferred_date)
                  <p class="text-[11px] text-slate-400 mt-0.5">{{ $req->preferred_date->format('d M Y') }}</p>
                @endif
              </td>
              <td class="px-5 py-4 text-slate-600">{{ $req->customer?->name ?? '—' }}</td>
              <td class="px-5 py-4 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-{{ $uc }}-50 text-{{ $uc }}-700">{{ $req->urgency }}</span>
              </td>
              <td class="px-5 py-4 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-{{ $sc }}-50 text-{{ $sc }}-700">{{ str_replace('_',' ',$req->request_status) }}</span>
              </td>
              <td class="px-5 py-4 text-center">
                @if($req->request_status === 'completed')
                  @if($req->invoice)
                    @php $invSc = ['draft'=>'slate','pending'=>'yellow','due'=>'orange','partial'=>'blue','paid'=>'green'][$req->invoice->payment_status] ?? 'slate'; @endphp
                    <a href="{{ route('provider.invoices.show', $req->invoice) }}"
                       class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-{{ $invSc }}-50 text-{{ $invSc }}-700 hover:bg-{{ $invSc }}-100 transition">
                      <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                      {{ ucfirst($req->invoice->payment_status) }}
                    </a>
                  @else
                    <a href="{{ route('provider.invoices.create', $req) }}"
                       class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 hover:bg-primary-50 hover:text-primary-600 transition">
                      <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                      Create
                    </a>
                  @endif
                @else
                  <span class="text-slate-300 text-xs">—</span>
                @endif
              </td>
              <td class="px-5 py-4 text-right">
                <a href="{{ route('provider.requests.show', $req) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100 transition">
                  View <span class="rtl-flip">→</span>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($requests->hasPages())
        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100">{{ $requests->links() }}</div>
      @endif
    </div>
  @endif
</div>
@endsection
