@extends('layouts.dashboard')
@section('title', 'My Earnings')
@section('content')
<div class="space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Earnings</h2>
      <p class="text-slate-500 text-xs mt-0.5">Since {{ $start->format('d M Y') }}</p>
    </div>
    <div class="flex items-center gap-2">
      @foreach(['today','week','month','year'] as $p)
      <a href="{{ route('tech.earnings.period', $p) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ $period === $p ? 'bg-primary-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">{{ ucfirst($p) }}</a>
      @endforeach
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center">
    <p class="text-xs text-slate-500 uppercase tracking-widest">Total Earnings</p>
    <p class="text-4xl font-black text-slate-900 mt-2">{{ number_format($total, 2) }}</p>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Date</th>
          <th class="px-4 py-3 text-start font-semibold">Job</th>
          <th class="px-4 py-3 text-end font-semibold">Commission</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($jobs as $job)
        <tr>
          <td class="px-5 py-3">{{ $job->completed_at?->format('d M Y') ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $job->request?->request_number ?? '—' }}</td>
          <td class="px-4 py-3 text-end font-bold text-green-600">{{ number_format($job->commission_earned ?? 0, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="3" class="px-5 py-10 text-center text-slate-400 italic">No completed jobs in this period.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($jobs->hasPages())<div>{{ $jobs->links() }}</div>@endif
</div>
@endsection
