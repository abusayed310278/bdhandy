@extends('layouts.dashboard')
@section('title', 'Analytics')

@section('content')
<div class="space-y-6 text-sm">

  <div>
    <h2 class="text-xl font-bold text-slate-900">Analytics</h2>
    <p class="text-slate-500 text-xs mt-0.5">Your performance overview</p>
  </div>

  {{-- KPI row --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
      <p class="text-xs font-medium text-slate-500">Total Requests</p>
      <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalRequests }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
      <p class="text-xs font-medium text-slate-500">Completed</p>
      <p class="text-3xl font-bold text-green-600 mt-1">{{ $completedRequests }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
      <p class="text-xs font-medium text-slate-500">Avg Rating</p>
      <p class="text-3xl font-bold text-slate-900 mt-1">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
      <p class="text-xs font-medium text-slate-500">Total Reviews</p>
      <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalReviews }}</p>
    </div>
  </div>

  {{-- Requests per month --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    <h3 class="font-semibold text-slate-900 mb-4">Requests — Last 6 Months</h3>
    @php $maxVal = max(array_values($months) + [1]); @endphp
    <div class="flex items-end gap-2 h-32">
      @foreach($months as $month => $count)
      @php $barH = $maxVal > 0 ? round($count / $maxVal * 100) : 0; @endphp
      <div class="flex-1 flex flex-col items-center gap-1">
        <span class="text-[10px] font-semibold text-slate-600">{{ $count > 0 ? $count : '' }}</span>
        <div class="w-full rounded-t-lg bg-primary-{{ $count > 0 ? '400' : '100' }} transition-all" style="height:{{ max(4, $barH) }}%"></div>
        <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M') }}</span>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Status breakdown --}}
  <div class="grid sm:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
      <h3 class="font-semibold text-slate-900 mb-4">Request Status Breakdown</h3>
      @php
        $statusColors = ['pending'=>'yellow','accepted'=>'primary','in_progress'=>'blue','completed'=>'green','cancelled'=>'slate','disputed'=>'red','expired'=>'slate'];
        $total = array_sum($statusBreakdown) ?: 1;
      @endphp
      @if(empty($statusBreakdown))
        <p class="text-slate-400 text-xs">No data yet.</p>
      @else
        <div class="space-y-2.5">
          @foreach($statusBreakdown as $status => $cnt)
          @php $color = $statusColors[$status] ?? 'slate'; $pct = round($cnt / $total * 100); @endphp
          <div>
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs text-slate-600 capitalize">{{ str_replace('_', ' ', $status) }}</span>
              <span class="text-xs font-semibold text-slate-700">{{ $cnt }} ({{ $pct }}%)</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
              <div class="bg-{{ $color }}-400 h-2 rounded-full" style="width:{{ $pct }}%"></div>
            </div>
          </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Rating distribution --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
      <h3 class="font-semibold text-slate-900 mb-4">Rating Distribution</h3>
      @if(empty($ratingDist))
        <p class="text-slate-400 text-xs">No reviews yet.</p>
      @else
        @php $rTotal = array_sum($ratingDist) ?: 1; @endphp
        <div class="space-y-2">
          @for($i = 5; $i >= 1; $i--)
          @php $cnt = $ratingDist[$i] ?? 0; $pct = round($cnt / $rTotal * 100); @endphp
          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-600 w-3">{{ $i }}</span>
            <svg class="w-3.5 h-3.5 text-yellow-400 shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <div class="flex-1 bg-slate-100 rounded-full h-2">
              <div class="bg-yellow-400 h-2 rounded-full" style="width:{{ $pct }}%"></div>
            </div>
            <span class="text-xs text-slate-400 w-5 text-right">{{ $cnt }}</span>
          </div>
          @endfor
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
