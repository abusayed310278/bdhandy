@extends('layouts.dashboard')
@section('title', $member->full_name)

@section('content')
<div class="space-y-6 text-sm">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
      @if($member->profile_photo)
        <img src="{{ Storage::url($member->profile_photo) }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-200">
      @else
        <div class="w-14 h-14 rounded-2xl bg-primary-100 flex items-center justify-center text-primary-600 font-black text-lg">
          {{ strtoupper(substr($member->full_name, 0, 2)) }}
        </div>
      @endif
      <div>
        <h2 class="text-xl font-bold text-slate-900">{{ $member->full_name }}</h2>
        <p class="text-xs text-slate-500">{{ $member->employee_code }} · {{ $member->designation ?: 'No designation' }}</p>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('business.team.edit', $member) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
      </a>
      <a href="{{ route('business.team.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">Back</a>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
  @endif

  <div class="grid lg:grid-cols-3 gap-6">

    {{-- Left: details --}}
    <div class="lg:col-span-2 space-y-6">

      {{-- Info cards --}}
      <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-50">
        <div class="px-5 py-4"><h3 class="font-semibold text-slate-900">Details</h3></div>
        <div class="p-5 grid sm:grid-cols-2 gap-4">
          <div>
            <p class="text-xs text-slate-500">Phone</p>
            <p class="font-semibold text-slate-900 mt-0.5">{{ $member->phone }}</p>
          </div>
          <div>
            <p class="text-xs text-slate-500">Email</p>
            <p class="font-semibold text-slate-900 mt-0.5">{{ $member->email ?: '—' }}</p>
          </div>
          <div>
            <p class="text-xs text-slate-500">Status</p>
            <p class="font-semibold text-slate-900 mt-0.5">{{ ucfirst($member->status) }}</p>
          </div>
          <div>
            <p class="text-xs text-slate-500">Role</p>
            <p class="font-semibold text-slate-900 mt-0.5">{{ $member->role?->role_name ?? '—' }}</p>
          </div>
          <div>
            <p class="text-xs text-slate-500">Compensation</p>
            <p class="font-semibold text-slate-900 mt-0.5">{{ ucfirst($member->compensation_type) }}</p>
          </div>
          <div>
            <p class="text-xs text-slate-500">Joining Date</p>
            <p class="font-semibold text-slate-900 mt-0.5">{{ $member->joining_date?->format('d M Y') ?? '—' }}</p>
          </div>
          <div>
            <p class="text-xs text-slate-500">Renewal Date</p>
            @if($member->renewal_date)
              @php
                $days = now()->startOfDay()->diffInDays($member->renewal_date->startOfDay(), false);
              @endphp
              <div class="flex flex-wrap items-center gap-2 mt-0.5">
                <span class="font-semibold text-slate-900">{{ $member->renewal_date->format('d M Y') }}</span>
                @if($days < 0)
                  <span class="inline-flex items-center text-[10px] font-bold text-red-700 bg-red-50 px-1.5 py-0.5 rounded">
                    Expired {{ abs($days) }} {{ Str::plural('day', abs($days)) }} ago
                  </span>
                @elseif($days == 0)
                  <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded animate-pulse">
                    Expires today
                  </span>
                @elseif($days <= 30)
                  <span class="inline-flex items-center text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">
                    {{ $days }} {{ Str::plural('day', $days) }} left
                  </span>
                @else
                  <span class="inline-flex items-center text-[10px] font-bold text-green-700 bg-green-50 px-1.5 py-0.5 rounded">
                    {{ $days }} {{ Str::plural('day', $days) }} left
                  </span>
                @endif
              </div>
            @else
              <p class="font-semibold text-slate-900 mt-0.5">—</p>
            @endif
          </div>
        </div>
      </div>

      {{-- Service Skills --}}
      <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-50">
        <div class="px-5 py-4"><h3 class="font-semibold text-slate-900">Service Skills</h3></div>
        <div class="p-5">
          @if($member->services->isEmpty())
            <p class="text-slate-400 italic">No services assigned.</p>
          @else
          <div class="flex flex-wrap gap-2">
            @foreach($member->services as $ms)
            @php $name = $ms->service?->getTranslation('translations', 'en')['name'] ?? $ms->service?->slug ?? '?'; @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border text-xs font-medium
              {{ $ms->is_primary ? 'bg-primary-50 border-primary-200 text-primary-700' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
              {{ $name }}
              <span class="text-[10px] opacity-70">{{ ucfirst($ms->skill_level) }}</span>
              @if($ms->is_primary) <span class="text-[10px] font-bold text-primary-600">★ Primary</span> @endif
            </span>
            @endforeach
          </div>
          @endif
        </div>
      </div>

      {{-- Recent Jobs --}}
      <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-50">
        <div class="px-5 py-4"><h3 class="font-semibold text-slate-900">Recent Job Assignments</h3></div>
        <div class="divide-y divide-slate-50">
          @forelse($recentJobs as $job)
          <div class="px-5 py-3 flex items-center justify-between">
            <div>
              <p class="font-medium text-slate-900">{{ $job->request?->request_number ?? 'REQ-?' }}</p>
              <p class="text-xs text-slate-400">{{ $job->scheduled_start_time?->format('d M Y, H:i') ?? $job->assigned_at->format('d M Y') }}</p>
            </div>
            @php $sc=['assigned'=>'slate','accepted'=>'blue','en_route'=>'yellow','arrived'=>'cyan','in_progress'=>'orange','completed'=>'green','rejected'=>'red','reassigned'=>'purple']; $c=$sc[$job->status]??'slate'; @endphp
            <span class="px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 text-[11px] font-semibold capitalize">{{ str_replace('_',' ',$job->status) }}</span>
          </div>
          @empty
          <p class="px-5 py-4 text-slate-400 italic">No jobs assigned yet.</p>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Right: attendance summary + assets --}}
    <div class="space-y-6">

      {{-- Attendance --}}
      <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-50">
        <div class="px-5 py-4"><h3 class="font-semibold text-slate-900">Recent Attendance</h3></div>
        <div class="divide-y divide-slate-50">
          @forelse($recentAttendance as $att)
          <div class="px-4 py-2.5">
            <div class="flex justify-between">
              <p class="font-medium text-slate-800">{{ $att->clock_in_time->format('d M') }}</p>
              @php $c = $att->status === 'clocked_out' ? 'green' : ($att->status === 'on_break' ? 'amber' : 'blue'); @endphp
              <span class="text-[11px] text-{{ $c }}-600 font-semibold capitalize">{{ str_replace('_',' ',$att->status) }}</span>
            </div>
            <p class="text-xs text-slate-400">{{ $att->clock_in_time->format('H:i') }} → {{ $att->clock_out_time?->format('H:i') ?? 'ongoing' }}
              @if($att->total_hours) · <span class="font-medium text-slate-600">{{ $att->total_hours }}h</span> @endif
            </p>
          </div>
          @empty
          <p class="px-4 py-4 text-slate-400 italic text-xs">No attendance records.</p>
          @endforelse
        </div>
      </div>

      {{-- Assigned Equipment --}}
      <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-50">
        <div class="px-5 py-4"><h3 class="font-semibold text-slate-900">Assigned Equipment</h3></div>
        <div class="p-4">
          @forelse($member->equipmentAssignments as $ea)
          <div class="flex items-center gap-2 py-1.5">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            <span class="text-slate-700 font-medium text-xs">{{ $ea->equipment?->name }}</span>
            <span class="ms-auto text-[11px] text-slate-400">{{ $ea->equipment?->code }}</span>
          </div>
          @empty
          <p class="text-slate-400 italic text-xs">No equipment assigned.</p>
          @endforelse
        </div>
      </div>

      {{-- Vehicle Assignment History --}}
      <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-50">
        <div class="px-5 py-4"><h3 class="font-semibold text-slate-900">Vehicle History</h3></div>
        <div class="divide-y divide-slate-50">
          @forelse($vehicleHistory as $vh)
          <div class="px-4 py-3 space-y-1">
            <div class="flex items-center justify-between">
              <span class="font-bold text-slate-900 text-xs">{{ $vh->vehicle?->plate_number }}</span>
              @if($vh->status === 'active')
                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-semibold">Active</span>
              @else
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-semibold">Returned</span>
              @endif
            </div>
            <p class="text-[11px] text-slate-500 capitalize">{{ $vh->vehicle?->make }} {{ $vh->vehicle?->model }} · {{ $vh->vehicle?->vehicle_type }}</p>
            <div class="text-[10px] text-slate-400 space-y-0.5">
              <p>Assigned: <span class="font-medium text-slate-600">{{ $vh->assigned_at->format('d M Y, H:i') }}</span> ({{ number_format($vh->odometer_at_assignment) }} km)</p>
              @if($vh->returned_at)
                <p>Returned: <span class="font-medium text-slate-600">{{ $vh->returned_at->format('d M Y, H:i') }}</span> ({{ number_format($vh->odometer_at_return) }} km)</p>
                @if($vh->odometer_at_return && $vh->odometer_at_assignment)
                  <p class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded inline-block mt-1">
                    ⚡ {{ number_format($vh->odometer_at_return - $vh->odometer_at_assignment) }} km driven
                  </p>
                @endif
                @if($vh->notes)
                  <p class="italic text-slate-500 bg-slate-50 border border-slate-100 rounded px-1.5 py-0.5 mt-0.5">Notes: {{ $vh->notes }}</p>
                @endif
              @else
                <p class="text-blue-600 font-medium">Currently driving this vehicle</p>
              @endif
            </div>
          </div>
          @empty
          <p class="px-4 py-4 text-slate-400 italic text-xs">No vehicle assignment history.</p>
          @endforelse
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
