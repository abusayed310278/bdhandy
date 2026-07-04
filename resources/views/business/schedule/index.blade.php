@extends('layouts.dashboard')
@section('title', 'Schedule Calendar')

@section('content')
<div class="space-y-6 text-sm">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Schedule & Routes</h2>
      <p class="text-slate-500 text-xs mt-0.5">Manage and view monthly field routes and technician scheduling</p>
    </div>
    
    {{-- Fast jump date picker to build daily route --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-3 flex items-center gap-3">
      <span class="text-xs text-slate-500 font-medium">Daily Route Builder:</span>
      <form method="GET" action="" id="sched-form" class="flex items-center gap-2">
        <input type="date" id="sched-date" value="{{ $today }}" class="rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs focus:border-primary-500 outline-none">
        <button class="px-3.5 py-1.5 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition shadow-soft">Go</button>
      </form>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
  @endif

  {{-- Calendar Controls & Filters --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
    
    {{-- Month Navigation --}}
    <div class="flex items-center gap-3">
      <a href="{{ route('business.schedule.index', ['month' => $prevMonth, 'team_member_id' => request('team_member_id'), 'status' => request('status')]) }}" 
         class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-sm"
         title="Previous Month">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
      </a>
      <h3 class="text-lg font-black text-slate-900 min-w-[140px] text-center capitalize">
        {{ $month->format('F Y') }}
      </h3>
      <a href="{{ route('business.schedule.index', ['month' => $nextMonth, 'team_member_id' => request('team_member_id'), 'status' => request('status')]) }}" 
         class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-sm"
         title="Next Month">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('business.schedule.index') }}" class="flex flex-wrap items-center gap-3">
      <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
      
      <div>
        <select name="team_member_id" onchange="this.form.submit()" 
                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-primary-500">
          <option value="">All Team Members</option>
          @foreach($members as $m)
            <option value="{{ $m->id }}" @selected(request('team_member_id') == $m->id)>{{ $m->full_name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <select name="status" onchange="this.form.submit()" 
                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-primary-500">
          <option value="">All Statuses</option>
          @foreach([
            'assigned' => 'Assigned',
            'accepted' => 'Accepted',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
            'reassigned' => 'Reassigned'
          ] as $val => $lbl)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>

      @if(request()->filled('team_member_id') || request()->filled('status'))
        <a href="{{ route('business.schedule.index', ['month' => $month->format('Y-m')]) }}" 
           class="text-xs text-red-600 hover:text-red-700 font-semibold underline">
          Clear Filters
        </a>
      @endif
    </form>
  </div>

  {{-- Calendar Grid --}}
  <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
    
    {{-- Days of Week Header --}}
    <div class="grid grid-cols-7 bg-slate-50 border-b border-slate-100">
      @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $w)
        <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
          {{ substr($w, 0, 3) }}
        </div>
      @endforeach
    </div>

    {{-- Grid Cells --}}
    <div class="grid grid-cols-7 grid-rows-6 divide-x divide-y divide-slate-100">
      
      {{-- Blank cells from previous month --}}
      @for($i = 0; $i < $blankDays; $i++)
        <div class="min-h-[120px] bg-slate-50/50 p-2 text-slate-300"></div>
      @endfor

      {{-- Month day cells --}}
      @for($dayNum = 1; $dayNum <= $daysInMonth; $dayNum++)
        @php
          $dayDateStr = $month->copy()->day($dayNum)->format('Y-m-d');
          $dayAssignments = $assignmentsByDate->get($dayDateStr, collect());
          $isToday = today()->format('Y-m-d') === $dayDateStr;
        @endphp
        <div class="min-h-[120px] p-2 flex flex-col justify-between hover:bg-slate-50/30 transition group relative">
          
          {{-- Day Cell Top header --}}
          <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-bold {{ $isToday ? 'w-6 h-6 rounded-full bg-primary-500 text-white flex items-center justify-center shadow-soft' : 'text-slate-500' }}">
              {{ $dayNum }}
            </span>
            <a href="{{ route('business.schedule.show', $dayDateStr) }}" 
               class="text-[9px] font-extrabold text-primary-500 opacity-0 group-hover:opacity-100 hover:text-primary-600 transition shrink-0">
              Build Route
            </a>
          </div>

          {{-- Day Cell Job List --}}
          <div class="flex-1 space-y-1 overflow-y-auto max-h-[85px] scrollbar-thin">
            @forelse($dayAssignments->take(3) as $as)
              @php
                $sc = [
                  'completed'   => 'bg-green-50 text-green-700 border-green-100',
                  'in_progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                  'accepted'    => 'bg-blue-50 text-blue-700 border-blue-100',
                  'assigned'    => 'bg-blue-50 text-blue-700 border-blue-100',
                  'rejected'    => 'bg-red-50 text-red-700 border-red-100',
                  'reassigned'  => 'bg-purple-50 text-purple-700 border-purple-100',
                ][$as->status] ?? 'bg-slate-50 text-slate-700 border-slate-100';
              @endphp
              <a href="{{ route('provider.requests.show', $as->service_request_id) }}" 
                 class="block px-1.5 py-1 text-[10px] font-bold rounded-lg border {{ $sc }} truncate hover:scale-[1.02] active:scale-95 transition"
                 title="{{ $as->request?->request_number }} ({{ $as->member?->full_name }})">
                {{ $as->scheduled_start_time ? $as->scheduled_start_time->format('H:i') : '—' }} · {{ $as->request?->request_number }}
              </a>
            @empty
              {{-- Empty space placeholder --}}
            @endforelse

            {{-- "+X more" link if more than 3 jobs --}}
            @if($dayAssignments->count() > 3)
              <a href="{{ route('business.schedule.show', $dayDateStr) }}" 
                 class="block text-center text-[10px] font-black text-slate-400 hover:text-primary-600 py-0.5 rounded bg-slate-50 transition border border-dashed border-slate-200">
                +{{ $dayAssignments->count() - 3 }} more
              </a>
            @endif
          </div>

        </div>
      @endfor

      {{-- Blank cells after the month ends --}}
      @php
        $totalCells = $blankDays + $daysInMonth;
        $remainingBlank = (7 - ($totalCells % 7)) % 7;
      @endphp
      @for($i = 0; $i < $remainingBlank; $i++)
        <div class="min-h-[120px] bg-slate-50/50 p-2 text-slate-300"></div>
      @endfor

    </div>
  </div>
</div>

<script>
document.getElementById('sched-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const d = document.getElementById('sched-date').value;
    if (d) {
        window.location = '{{ route("business.schedule.show", "_DATE_") }}'.replace('_DATE_', d);
    }
});
</script>
@endsection
