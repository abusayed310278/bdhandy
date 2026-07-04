@extends('layouts.dashboard')
@section('title', 'Schedule — ' . $day->format('d M Y'))

@section('content')
<div class="space-y-6 text-sm">

  {{-- Header + day navigation --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <a href="{{ route('business.schedule.index') }}" class="text-xs text-primary-600 hover:underline font-semibold">← Schedule Overview</a>
      <h2 class="text-xl font-bold text-slate-900 mt-1">Daily Schedule</h2>
      <p class="text-slate-500 text-xs mt-0.5">{{ $day->format('l, d F Y') }}</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('business.schedule.show', $day->copy()->subDay()->format('Y-m-d')) }}"
         class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">← Prev</a>
      <a href="{{ route('business.schedule.show', today()->format('Y-m-d')) }}"
         class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">Today</a>
      <a href="{{ route('business.schedule.show', $day->copy()->addDay()->format('Y-m-d')) }}"
         class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">Next →</a>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-xs font-semibold">{{ session('success') }}</div>
  @endif

  {{-- ── Unscheduled jobs notice ─────────────────────────────────────────── --}}
  @if($unscheduled->isNotEmpty())
  <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center gap-3 animate-fade-in">
    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <p class="text-xs font-semibold text-amber-800">
      {{ $unscheduled->count() }} {{ Str::plural('job', $unscheduled->count()) }} assigned for this date are not yet in a published schedule.
    </p>
  </div>
  @endif

  {{-- ── Published / existing schedules ─────────────────────────────────── --}}
  @php $scheduledMemberIds = $schedules->pluck('team_member_id')->toArray(); @endphp

  @forelse($schedules as $schedule)
  @php
    $memberUnscheduled = $unscheduled->where('team_member_id', $schedule->team_member_id);
  @endphp
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition duration-200">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center text-primary-700 font-black text-xs">
          {{ strtoupper(substr($schedule->member?->full_name ?? '?', 0, 2)) }}
        </div>
        <div>
          <div class="flex items-center gap-2">
            <a href="{{ route('business.team.show', $schedule->member) }}" class="font-bold text-slate-900 hover:text-primary-600 transition">
              {{ $schedule->member?->full_name }}
            </a>
            @if($memberUnscheduled->isNotEmpty())
              <span class="px-2 py-0.5 rounded-xl bg-amber-100 text-amber-800 text-[10px] font-bold animate-pulse flex items-center gap-1">
                ⚠️ {{ $memberUnscheduled->count() }} pending optimization
              </span>
            @endif
          </div>
          <p class="text-[11px] text-slate-400">{{ $schedule->waypoints->count() }} stop(s) &middot; {{ $schedule->total_jobs_assigned }} job(s)</p>
        </div>
      </div>
      <div class="flex items-center gap-2 flex-wrap justify-end">
        @if($schedule->is_published)
          <span class="px-2.5 py-1 rounded-xl bg-green-100 text-green-700 text-xs font-bold">✓ Published</span>
          {{-- Allow re-optimize and re-publish --}}
          <form action="{{ route('business.schedule.optimize') }}" method="POST">
            @csrf
            <input type="hidden" name="member_id" value="{{ $schedule->team_member_id }}">
            <input type="hidden" name="date" value="{{ $day->format('Y-m-d') }}">
            <button class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">Re-optimize</button>
          </form>
        @else
          <form action="{{ route('business.schedule.optimize') }}" method="POST">
            @csrf
            <input type="hidden" name="member_id" value="{{ $schedule->team_member_id }}">
            <input type="hidden" name="date" value="{{ $day->format('Y-m-d') }}">
            <button class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-primary-50 hover:text-primary-600 transition">Optimize</button>
          </form>
          <form action="{{ route('business.schedule.publish', $schedule) }}" method="POST">
            @csrf
            <button class="px-3 py-1.5 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition">Publish</button>
          </form>
        @endif
      </div>
    </div>

    {{-- Waypoints with Drag & Drop functionality --}}
    @if($schedule->waypoints->isNotEmpty())
    <div class="divide-y divide-slate-100 waypoint-list" data-schedule-id="{{ $schedule->id }}">
      @foreach($schedule->waypoints->sortBy('sequence_order') as $wp)
      <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50/50 transition waypoint-item group" data-id="{{ $wp->id }}">
        
        {{-- Drag Handle & Sequence Number --}}
        <div class="flex items-center gap-2 shrink-0">
          <svg class="w-4 h-4 text-slate-300 drag-handle cursor-grab active:cursor-grabbing hover:text-slate-500 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5"/>
          </svg>
          <span class="w-6 h-6 rounded-lg bg-primary-50 border border-primary-100 flex items-center justify-center text-primary-700 font-bold text-[10px] seq-number">{{ $wp->sequence_order }}</span>
        </div>

        {{-- Rich Job Details --}}
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            @if($wp->jobAssignment?->request)
              <a href="{{ route('provider.requests.show', $wp->jobAssignment->service_request_id) }}" 
                 class="font-semibold text-slate-900 hover:text-primary-600 hover:underline transition">
                {{ $wp->jobAssignment->request->request_number }}
              </a>
              @php 
                $svc = $wp->jobAssignment->request->service;
                $serviceName = $svc ? (($svc->getTranslation('translations', 'en')['name'] ?? null) ?: $svc->slug) : null;
              @endphp
              @if($serviceName)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-primary-50 text-primary-700 text-[10px] font-bold">
                  {{ $serviceName }}
                </span>
              @endif
            @else
              —
            @endif
          </div>
          
          <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500 flex-wrap">
            <span class="truncate max-w-[240px]" title="{{ $wp->jobAssignment?->request?->address }}">📍 {{ $wp->jobAssignment?->request?->address ?? 'No Address' }}</span>
            <span>•</span>
            <span class="truncate">👤 {{ $wp->jobAssignment?->request?->customer?->name ?? 'No Customer' }}</span>
          </div>
        </div>

        {{-- Time & Status --}}
        <div class="flex items-center gap-4 shrink-0">
          <span class="text-xs text-slate-500 font-semibold bg-slate-50 px-2 py-1 rounded-lg border border-slate-100 shrink-0">
            🕒 {{ $wp->jobAssignment?->scheduled_start_time?->format('H:i') ?? '—' }}
          </span>
          @php $s=$wp->jobAssignment?->status??''; $c=['assigned'=>'slate','accepted'=>'blue','en_route'=>'yellow','arrived'=>'cyan','in_progress'=>'orange','completed'=>'green','rejected'=>'red'][$s]??'slate'; @endphp
          <span class="w-[90px] text-center px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 text-[10px] font-bold capitalize shrink-0">{{ str_replace('_',' ',$s) }}</span>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div class="px-5 py-4 text-xs text-slate-400 italic">No waypoints yet — click Optimize to sequence the jobs.</div>
    @endif

    {{-- Post-Publish Assignments / New Unscheduled Jobs for Scheduled Member --}}
    @if($memberUnscheduled->isNotEmpty())
    <div class="bg-amber-50/50 p-4 border-t border-dashed border-amber-200">
      <div class="flex items-center gap-1.5 text-[11px] font-bold text-amber-800 uppercase tracking-wider mb-2">
        <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Unscheduled / Pending Optimization ({{ $memberUnscheduled->count() }})
      </div>
      <div class="space-y-2">
        @foreach($memberUnscheduled as $job)
          <div class="flex items-center gap-3 px-3 py-2.5 bg-white rounded-xl border border-dashed border-amber-200 hover:bg-slate-50/50 transition">
            <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-[10px] font-black shrink-0">?</span>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-1.5 flex-wrap">
                @if($job->request)
                  <a href="{{ route('provider.requests.show', $job->service_request_id) }}" 
                     class="font-semibold text-slate-800 hover:text-primary-600 hover:underline transition">
                    {{ $job->request->request_number }}
                  </a>
                  @php
                    $jobSvc = $job->request->service;
                    $jobSvcName = $jobSvc ? (($jobSvc->getTranslation('translations', 'en')['name'] ?? null) ?: $jobSvc->slug) : null;
                  @endphp
                  @if($jobSvcName)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium">
                      {{ $jobSvcName }}
                    </span>
                  @endif
                @else
                  —
                @endif
              </div>
              <p class="text-[11px] text-slate-400 truncate mt-0.5">📍 {{ $job->request?->address ?? 'No Address' }} &middot; 👤 {{ $job->request?->customer?->name ?? 'No Customer' }}</p>
            </div>
            <p class="text-[11px] text-slate-400 font-semibold shrink-0">🕒 {{ $job->scheduled_start_time?->format('H:i') ?? '—' }}</p>
          </div>
        @endforeach
      </div>
      <div class="mt-3 flex justify-end">
        <form action="{{ route('business.schedule.optimize') }}" method="POST">
          @csrf
          <input type="hidden" name="member_id" value="{{ $schedule->team_member_id }}">
          <input type="hidden" name="date" value="{{ $day->format('Y-m-d') }}">
          <button class="px-3.5 py-1.5 rounded-xl bg-amber-600 text-white text-xs font-bold hover:bg-amber-700 transition shadow-sm flex items-center gap-1">
            ⚡ Re-optimize & Sequence Route
          </button>
        </form>
      </div>
    </div>
    @endif
  </div>
  @empty
  @endforelse

  {{-- ── Members with assigned jobs but NO schedule yet ─────────────────── --}}
  @php
    $unscheduledByMember = $unscheduled->groupBy('team_member_id');
  @endphp
  @foreach($unscheduledByMember as $memberId => $jobs)
  @if(!in_array($memberId, $scheduledMemberIds))
  @php $memberName = $jobs->first()->member?->full_name ?? 'Unknown'; @endphp
  <div class="bg-white rounded-2xl border border-dashed border-slate-300 overflow-hidden shadow-sm">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/50">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-black text-xs">
          {{ strtoupper(substr($memberName, 0, 2)) }}
        </div>
        <div>
          <p class="font-bold text-slate-700">{{ $memberName }}</p>
          <p class="text-[11px] text-slate-400">{{ $jobs->count() }} job(s) assigned — no schedule yet</p>
        </div>
      </div>
      <form action="{{ route('business.schedule.optimize') }}" method="POST">
        @csrf
        <input type="hidden" name="member_id" value="{{ $memberId }}">
        <input type="hidden" name="date" value="{{ $day->format('Y-m-d') }}">
        <button class="px-3 py-1.5 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition">Create Schedule</button>
      </form>
    </div>
    <div class="divide-y divide-slate-50">
      @foreach($jobs as $job)
      <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50/50 transition">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            @if($job->request)
              <a href="{{ route('provider.requests.show', $job->service_request_id) }}" 
                 class="font-semibold text-slate-800 hover:text-primary-600 hover:underline transition">
                {{ $job->request->request_number }}
              </a>
              @php
                $jobSvc = $job->request->service;
                $jobSvcName = $jobSvc ? (($jobSvc->getTranslation('translations', 'en')['name'] ?? null) ?: $jobSvc->slug) : null;
              @endphp
              @if($jobSvcName)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium">
                  {{ $jobSvcName }}
                </span>
              @endif
            @else
              —
            @endif
          </div>
          <p class="text-xs text-slate-400 truncate mt-0.5">📍 {{ $job->request?->address ?? '' }} &middot; 👤 {{ $job->request?->customer?->name ?? 'No Customer' }}</p>
        </div>
        <p class="text-xs text-slate-400 shrink-0">🕒 {{ $job->scheduled_start_time?->format('H:i') ?? '—' }}</p>
        @php $c=['assigned'=>'slate','accepted'=>'blue','en_route'=>'yellow','completed'=>'green'][$job->status]??'slate'; @endphp
        <span class="px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 text-[11px] font-semibold capitalize shrink-0">{{ str_replace('_',' ',$job->status) }}</span>
      </div>
      @endforeach
    </div>
  </div>
  @endif
  @endforeach

  @if($schedules->isEmpty() && $unscheduled->isEmpty())
  <div class="bg-white rounded-2xl border border-dashed border-slate-200 py-14 text-center text-slate-400 italic">
    No jobs or schedules for this date.
  </div>
  @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const lists = document.querySelectorAll('.waypoint-list');
  lists.forEach(list => {
    new Sortable(list, {
      handle: '.drag-handle',
      animation: 150,
      ghostClass: 'bg-slate-50',
      onEnd: function (evt) {
        const items = list.querySelectorAll('.waypoint-item');
        const waypointIds = Array.from(items).map(el => el.getAttribute('data-id'));

        // Instantly update UI sequence numbers visually
        items.forEach((el, index) => {
          const seqEl = el.querySelector('.seq-number');
          if (seqEl) seqEl.textContent = index + 1;
        });

        // Send to backend
        fetch("{{ route('business.schedule.reorder') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            waypoints: waypointIds
          })
        })
        .then(res => res.json())
        .then(data => {
          if (!data.success) {
            alert('Failed to save route order. Please refresh.');
          }
        })
        .catch(err => {
          console.error(err);
          alert('Connection error. Failed to save route order.');
        });
      }
    });
  });
});
</script>
@endpush
