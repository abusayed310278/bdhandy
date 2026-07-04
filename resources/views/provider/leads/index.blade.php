@extends('layouts.dashboard')
@section('title', 'Leads')

@section('content')
<div class="space-y-5 text-sm">

  <div>
    <h2 class="text-xl font-bold text-slate-900">Leads</h2>
    <p class="text-slate-500 text-xs mt-0.5">Browse open customer requirements and submit proposals</p>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  @if($leads->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
      <p class="text-slate-500 font-medium">No open leads right now</p>
      <p class="text-slate-400 text-xs mt-1">Check back soon — new customer requirements will appear here</p>
    </div>
  @else
    <div class="space-y-3">
      @foreach($leads as $lead)
      @php
        $urgColors = ['emergency'=>'red','urgent'=>'yellow','normal'=>'slate'];
        $uc = $urgColors[$lead->urgency] ?? 'slate';
        $proposed = in_array($lead->id, $myProposalRequirementIds);
      @endphp
      <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
          <div class="min-w-0 flex-1">
            <div class="flex items-start gap-2 flex-wrap">
              <a href="{{ route('provider.leads.show', $lead) }}" class="font-semibold text-slate-900 hover:text-primary-600 transition text-base leading-snug">{{ $lead->title }}</a>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-{{ $uc }}-50 text-{{ $uc }}-700">{{ $lead->urgency }}</span>
              @if($proposed)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-green-50 text-green-700">Proposed</span>
              @endif
            </div>
            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $lead->description }}</p>
            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-[11px] text-slate-500">
              @if($lead->service)
                <span>{{ ($lead->service->getTranslation('translations','en')['name'] ?? null) ?: $lead->service->slug }}</span>
              @endif
              @if($lead->category)
                <span>{{ $lead->category->getTranslation('translations','en') ?: $lead->category->slug }}</span>
              @endif
              @if($lead->preferred_date)
                <span>Needed by: {{ $lead->preferred_date->format('d M Y') }}</span>
              @endif
              @if($lead->expiry_at)
                <span class="text-red-400">Expires {{ $lead->expiry_at->diffForHumans() }}</span>
              @endif
            </div>
          </div>
          <div class="shrink-0 text-right">
            @if($lead->budget_type === 'fixed' && $lead->budget_fixed)
              <p class="font-bold text-slate-900">{{ $lead->currency?->symbol }}{{ number_format($lead->budget_fixed, 0) }}</p>
            @elseif($lead->budget_type === 'range')
              <p class="font-bold text-slate-900">{{ $lead->currency?->symbol }}{{ number_format($lead->budget_min, 0) }} – {{ $lead->currency?->symbol }}{{ number_format($lead->budget_max, 0) }}</p>
            @else
              <p class="text-slate-400 italic text-xs">Flexible budget</p>
            @endif
            <p class="text-[11px] text-slate-400 mt-1">{{ $lead->created_at->diffForHumans() }}</p>
            <a href="{{ route('provider.leads.show', $lead) }}" class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100 transition">
              {{ $proposed ? 'View Proposal' : 'Propose' }} <span>→</span>
            </a>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    @if($leads->hasPages())
      <div class="py-2">{{ $leads->links() }}</div>
    @endif
  @endif
</div>
@endsection
