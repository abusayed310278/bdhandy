@extends('layouts.dashboard')
@section('title', 'Payroll — ' . $month)

@section('content')
<div class="space-y-6 text-sm">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Payroll Centre</h2>
      <p class="text-slate-500 text-xs mt-0.5">{{ $start->format('F Y') }}</p>
    </div>
    <div class="flex items-center gap-2">
      <form method="GET" class="flex items-center gap-2">
        <input type="month" name="month" value="{{ $month }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-500 outline-none">
        <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">Calculate</button>
      </form>
      <a href="{{ route('business.payroll.export', ['month' => $month]) }}" class="px-4 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition">Export CSV</a>
    </div>
  </div>

  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif

  @php
    $grandTotal = $rows->sum('total');
    $totalHours = $rows->sum('hours');
    $totalJobs  = $rows->sum('jobs');
  @endphp
  <div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Total Payroll</p>
      <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($grandTotal, 2) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Total Hours</p>
      <p class="text-2xl font-black text-blue-600 mt-1">{{ number_format($totalHours, 1) }}h</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Jobs Completed</p>
      <p class="text-2xl font-black text-green-600 mt-1">{{ $totalJobs }}</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Member</th>
          <th class="px-4 py-3 text-start font-semibold">Type</th>
          <th class="px-4 py-3 text-end font-semibold">Hours</th>
          <th class="px-4 py-3 text-end font-semibold">Jobs</th>
          <th class="px-4 py-3 text-end font-semibold">Salary</th>
          <th class="px-4 py-3 text-end font-semibold">Commission</th>
          <th class="px-4 py-3 text-end font-semibold">Total Pay</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($rows as $row)
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3">
            <p class="font-semibold text-slate-900">{{ $row['name'] }}</p>
            <p class="text-[11px] text-slate-400">{{ $row['code'] }}</p>
          </td>
          <td class="px-4 py-3 capitalize text-slate-600">{{ $row['type'] }}</td>
          <td class="px-4 py-3 text-end text-slate-600">{{ number_format($row['hours'], 1) }}h</td>
          <td class="px-4 py-3 text-end text-slate-600">{{ $row['jobs'] }}</td>
          <td class="px-4 py-3 text-end text-slate-600">{{ $row['salary'] ? number_format($row['salary'], 2) : '—' }}</td>
          <td class="px-4 py-3 text-end text-slate-600">{{ $row['commission'] ? number_format($row['commission'], 2) : '—' }}</td>
          <td class="px-4 py-3 text-end font-bold text-slate-900">{{ $row['currency'] }} {{ number_format($row['total'], 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-12 text-center text-slate-400 italic">No active members.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($rows->isNotEmpty())
  <form action="{{ route('business.payroll.process') }}" method="POST" class="flex justify-end">
    @csrf
    <input type="hidden" name="month" value="{{ $month }}">
    <button class="px-6 py-2.5 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">Mark Period as Processed</button>
  </form>
  @endif
</div>
@endsection
