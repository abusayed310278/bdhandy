@extends('layouts.dashboard')
@section('title', $member->full_name . ' — Analytics')
@section('content')
<div class="space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">{{ $member->full_name }}</h2>
      <p class="text-slate-500 text-xs mt-0.5">Last 30 days performance</p>
    </div>
    <a href="{{ route('business.analytics.team') }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold">← Team</a>
  </div>

  <div class="grid sm:grid-cols-5 gap-4">
    @foreach([
      ['Jobs', $kpis['total_jobs']],
      ['Completed', $kpis['completed']],
      ['Avg Rating', '★ ' . $kpis['avg_rating']],
      ['Avg Travel (min)', $kpis['avg_travel']],
      ['Avg Work (min)', $kpis['avg_work']],
    ] as $kpi)
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">{{ $kpi[0] }}</p>
      <p class="text-xl font-black text-slate-900 mt-1">{{ $kpi[1] }}</p>
    </div>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100"><h3 class="font-semibold text-slate-900">Recent Jobs</h3></div>
    <table class="w-full text-sm">
      <tbody class="divide-y divide-slate-50">
        @forelse($jobs as $job)
        <tr>
          <td class="px-5 py-3">
            <p class="font-medium text-slate-900">{{ $job->request?->request_number ?? '—' }}</p>
            <p class="text-[11px] text-slate-400">{{ $job->assigned_at->format('d M Y') }}</p>
          </td>
          <td class="px-4 py-3">
            @php $sc=['assigned'=>'slate','accepted'=>'blue','in_progress'=>'orange','completed'=>'green','rejected'=>'red'][$job->status]??'slate'; @endphp
            <span class="px-2 py-0.5 rounded-full bg-{{ $sc }}-100 text-{{ $sc }}-700 text-[11px] font-semibold capitalize">{{ str_replace('_',' ',$job->status) }}</span>
          </td>
          <td class="px-4 py-3 text-end text-xs text-slate-500">
            @if($job->customer_rating) ★ {{ $job->customer_rating }}/5 @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="3" class="px-5 py-8 text-center text-slate-400 italic">No jobs in period.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
