@extends('layouts.dashboard')
@section('title', 'Team Analytics')
@section('content')
<div class="space-y-6 text-sm">
  <div>
    <h2 class="text-xl font-bold text-slate-900">Team Performance</h2>
    <p class="text-slate-500 text-xs mt-0.5">{{ now()->format('F Y') }}</p>
  </div>

  <div class="grid sm:grid-cols-4 gap-4">
    @foreach([
      ['Total Jobs', $totals['jobs'], 'blue'],
      ['Completed', $totals['completed'], 'green'],
      ['Avg Completion Rate', number_format($totals['avg_rate'], 1) . '%', 'primary'],
      ['Avg Rating', $totals['avg_rating'] . ' / 5', 'amber'],
    ] as $kpi)
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">{{ $kpi[0] }}</p>
      <p class="text-2xl font-black text-{{ $kpi[2] }}-600 mt-1">{{ $kpi[1] }}</p>
    </div>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Member</th>
          <th class="px-4 py-3 text-end font-semibold">Total Jobs</th>
          <th class="px-4 py-3 text-end font-semibold">Completed</th>
          <th class="px-4 py-3 text-end font-semibold">Rate</th>
          <th class="px-4 py-3 text-end font-semibold">Avg Rating</th>
          <th class="px-4 py-3 text-end font-semibold"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($stats as $s)
        <tr>
          <td class="px-5 py-3">
            <p class="font-semibold text-slate-900">{{ $s['member']->full_name }}</p>
            <p class="text-[11px] text-slate-400">{{ $s['member']->employee_code }}</p>
          </td>
          <td class="px-4 py-3 text-end">{{ $s['total_jobs'] }}</td>
          <td class="px-4 py-3 text-end">{{ $s['completed'] }}</td>
          <td class="px-4 py-3 text-end font-bold {{ $s['rate'] >= 80 ? 'text-green-600' : ($s['rate'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $s['rate'] }}%</td>
          <td class="px-4 py-3 text-end">★ {{ $s['avg_rating'] }}</td>
          <td class="px-4 py-3 text-end">
            <a href="{{ route('business.analytics.member', $s['member']) }}" class="text-xs text-primary-600 hover:underline font-semibold">View →</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
