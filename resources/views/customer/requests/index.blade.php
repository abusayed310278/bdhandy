@extends('layouts.dashboard')
@section('title', 'My Requests')

@section('content')
<div class="space-y-5 text-sm">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h2 class="text-xl font-bold text-slate-900">My Requests</h2>
      <p class="text-slate-500 text-xs mt-0.5">Track all your service requests</p>
    </div>
  </div>

  {{-- Tabs --}}
  <nav class="flex gap-1 border-b border-slate-200 overflow-x-auto no-scrollbar">
    @foreach(['active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
      <a href="{{ route('customer.requests.index', ['tab' => $key]) }}"
         class="px-4 py-2.5 text-sm font-medium border-b-2 whitespace-nowrap transition {{ $tab === $key ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
        {{ $label }}
      </a>
    @endforeach
  </nav>

  @if($requests->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>
      <p class="text-slate-500 font-medium">No {{ $tab }} requests</p>
      <p class="text-slate-400 text-xs mt-1">Requests from providers will appear here</p>
    </div>
  @else
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Request</th>
              <th class="px-5 py-3.5">Provider</th>
              <th class="px-5 py-3.5">Date</th>
              <th class="px-5 py-3.5 text-center">Status</th>
              <th class="px-5 py-3.5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($requests as $req)
            <tr class="hover:bg-slate-50/50 transition">
              <td class="px-5 py-4">
                <p class="font-semibold text-slate-900">{{ $req->title }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $req->request_number }}</p>
                @if($req->service)
                  <span class="mt-1 inline-block text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded">{{ $req->service->getTranslation('translations','en')['name'] ?? $req->service->slug }}</span>
                @endif
              </td>
              <td class="px-5 py-4">
                @if($req->provider?->providerProfile)
                  <p class="font-medium text-slate-800">{{ $req->provider->providerProfile->business_name }}</p>
                  <p class="text-xs text-slate-400">{{ $req->provider->name }}</p>
                @else
                  <span class="text-slate-400">—</span>
                @endif
              </td>
              <td class="px-5 py-4 text-slate-500 text-xs">
                {{ $req->preferred_date?->format('d M Y') ?? '—' }}
              </td>
              <td class="px-5 py-4 text-center">
                @php
                  $colors = ['pending'=>'yellow','accepted'=>'primary','in_progress'=>'primary','completed'=>'green','cancelled'=>'slate','disputed'=>'red','expired'=>'slate'];
                  $c = $colors[$req->request_status] ?? 'slate';
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $c }}-50 text-{{ $c }}-700">
                  {{ str_replace('_', ' ', $req->request_status) }}
                </span>
              </td>
              <td class="px-5 py-4 text-right">
                <a href="{{ route('customer.requests.show', $req) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100 transition">
                  View <span class="rtl-flip">→</span>
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($requests->hasPages())
        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100">{{ $requests->appends(['tab'=>$tab])->links() }}</div>
      @endif
    </div>
  @endif
</div>
@endsection
