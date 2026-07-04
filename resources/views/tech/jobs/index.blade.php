@extends('layouts.dashboard')
@section('title', 'My All Jobs')

@push('head')
<style>
  .calendar-day { min-height: 80px; }
  .job-dot { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endpush

@section('content')
@php
  $allDates = $assignments->keys()->filter()->sort()->values();
  $firstDate = $allDates->first() ? \Carbon\Carbon::parse($allDates->first()) : now();
  $currentMonth = request('month', $firstDate->format('Y-m'));
  $monthStart   = \Carbon\Carbon::parse($currentMonth . '-01');
  $monthEnd     = $monthStart->copy()->endOfMonth();
  $prevMonth    = $monthStart->copy()->subMonth()->format('Y-m');
  $nextMonth    = $monthStart->copy()->addMonth()->format('Y-m');

  // Status colours
  $colours = [
    'assigned'    => ['bg' => 'bg-slate-100',   'text' => 'text-slate-700'],
    'accepted'    => ['bg' => 'bg-blue-100',    'text' => 'text-blue-700'],
    'en_route'    => ['bg' => 'bg-yellow-100',  'text' => 'text-yellow-700'],
    'arrived'     => ['bg' => 'bg-cyan-100',    'text' => 'text-cyan-700'],
    'in_progress' => ['bg' => 'bg-orange-100',  'text' => 'text-orange-700'],
    'completed'   => ['bg' => 'bg-green-100',   'text' => 'text-green-700'],
    'rejected'    => ['bg' => 'bg-red-100',     'text' => 'text-red-700'],
    'reassigned'  => ['bg' => 'bg-purple-100',  'text' => 'text-purple-700'],
  ];
@endphp

<div class="space-y-6 text-sm">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">My All Jobs</h2>
      <p class="text-slate-500 text-xs mt-0.5">Calendar view of all your assigned jobs</p>
    </div>
    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
      @php $total = $assignments->flatten()->count(); $done = $assignments->flatten()->where('status','completed')->count(); @endphp
      <span class="px-3 py-1.5 rounded-xl bg-white border border-slate-200">{{ $total }} total</span>
      <span class="px-3 py-1.5 rounded-xl bg-green-50 border border-green-200 text-green-700">{{ $done }} done</span>
    </div>
  </div>

  {{-- Month Navigation --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <a href="{{ request()->fullUrlWithQuery(['month' => $prevMonth]) }}"
         class="p-2 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
      </a>
      <h3 class="text-base font-bold text-slate-900">{{ $monthStart->format('F Y') }}</h3>
      <a href="{{ request()->fullUrlWithQuery(['month' => $nextMonth]) }}"
         class="p-2 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
      </a>
    </div>

    {{-- Weekday Headers --}}
    <div class="grid grid-cols-7 border-b border-slate-100">
      @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
      <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $d }}</div>
      @endforeach
    </div>

    {{-- Calendar Grid --}}
    @php
      $startPad = $monthStart->dayOfWeek; // 0=Sun
      $daysInMonth = $monthStart->daysInMonth;
      $today = today()->format('Y-m-d');
    @endphp
    <div class="grid grid-cols-7">
      {{-- Leading empty cells --}}
      @for($i = 0; $i < $startPad; $i++)
      <div class="calendar-day border-b border-e border-slate-100 bg-slate-50/50"></div>
      @endfor

      {{-- Day cells --}}
      @for($day = 1; $day <= $daysInMonth; $day++)
      @php
        $date    = $monthStart->copy()->day($day)->format('Y-m-d');
        $dayJobs = $assignments->get($date, collect());
        $isToday = $date === $today;
        $col     = ($startPad + $day - 1) % 7;
        $isLast  = $col === 6;
      @endphp
      <div class="calendar-day border-b {{ $isLast ? '' : 'border-e' }} border-slate-100 p-1.5 space-y-1 {{ $isToday ? 'bg-primary-50/60' : '' }}">
        <span class="flex items-center justify-center w-6 h-6 text-xs font-bold rounded-full
          {{ $isToday ? 'bg-primary-500 text-white' : 'text-slate-500' }}">{{ $day }}</span>

        @foreach($dayJobs->take(3) as $job)
        @php $c = $colours[$job->status] ?? $colours['assigned']; @endphp
        <a href="{{ route('tech.jobs.show', $job) }}"
           class="job-dot block w-full text-[10px] font-semibold px-1.5 py-0.5 rounded-md {{ $c['bg'] }} {{ $c['text'] }} hover:opacity-80 transition">
          {{ $job->request?->request_number ?? '#' . $job->id }}
        </a>
        @endforeach

        @if($dayJobs->count() > 3)
        <span class="block text-[10px] text-slate-400 font-semibold px-1">+{{ $dayJobs->count() - 3 }} more</span>
        @endif
      </div>
      @endfor

      {{-- Trailing empty cells to complete row --}}
      @php $trailing = (7 - ($startPad + $daysInMonth) % 7) % 7; @endphp
      @for($i = 0; $i < $trailing; $i++)
      <div class="calendar-day border-b border-e border-slate-100 bg-slate-50/50 last:border-e-0"></div>
      @endfor
    </div>
  </div>

  {{-- This Month Jobs List --}}
  @php
    $thisMonthDates = $assignments->filter(fn($_, $k) => $k && str_starts_with($k, $currentMonth))->sortKeys();
  @endphp

  @if($thisMonthDates->isNotEmpty())
  <div class="space-y-3">
    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">Jobs in {{ $monthStart->format('F Y') }}</h4>
    @foreach($thisMonthDates as $date => $jobs)
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($date)->format('l, d F') }}</span>
        <span class="ms-auto text-[10px] text-slate-400 font-semibold">{{ $jobs->count() }} {{ Str::plural('job', $jobs->count()) }}</span>
      </div>
      <div class="divide-y divide-slate-100">
        @foreach($jobs as $job)
        @php $req = $job->request; $c = $colours[$job->status] ?? $colours['assigned']; @endphp
        <a href="{{ route('tech.jobs.show', $job) }}"
           class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition group">
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-slate-900 group-hover:text-primary-600 transition">{{ $req?->request_number ?? '—' }}</p>
            @if($req?->title)
            <p class="text-xs text-slate-500 truncate mt-0.5">{{ $req->title }}</p>
            @endif
            @if($req?->address)
            <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ $req->address }}</p>
            @endif
          </div>
          <div class="shrink-0 text-end space-y-1">
            @if($job->scheduled_start_time)
            <p class="text-[11px] text-slate-500 font-medium">{{ $job->scheduled_start_time->format('H:i') }}</p>
            @endif
            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $c['bg'] }} {{ $c['text'] }}">
              {{ str_replace('_', ' ', $job->status) }}
            </span>
          </div>
          <svg class="w-4 h-4 text-slate-300 group-hover:text-primary-400 shrink-0 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        @endforeach
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div class="bg-white rounded-2xl border border-dashed border-slate-200 py-12 text-center text-slate-400 italic">
    No jobs assigned in {{ $monthStart->format('F Y') }}.
  </div>
  @endif

</div>
@endsection
