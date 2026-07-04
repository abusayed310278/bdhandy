@extends('layouts.dashboard')
@section('title', $member->full_name . ' — Attendance History')

@section('content')
<div class="space-y-6 text-sm">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <a href="{{ route('business.attendance.index') }}" class="text-xs text-primary-600 hover:underline font-semibold">← Back to Attendance</a>
      <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $member->full_name }}</h2>
      <p class="text-slate-500 text-xs mt-0.5">{{ $member->designation ?? 'Team Member' }} · {{ $member->employee_code }}</p>
    </div>
    <a href="{{ route('business.team.show', $member) }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      View Profile
    </a>
  </div>

  {{-- Summary KPIs --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Total Days</p>
      <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats->total_days ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Completed</p>
      <p class="text-2xl font-black text-green-600 mt-1">{{ $stats->completed_days ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Total Hours</p>
      <p class="text-2xl font-black text-primary-600 mt-1">{{ number_format($stats->total_hours ?? 0, 1) }}h</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Verified</p>
      <p class="text-2xl font-black text-slate-700 mt-1">{{ $stats->verified_days ?? 0 }}</p>
    </div>
  </div>

  {{-- History Table --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Date</th>
          <th class="px-4 py-3 text-start font-semibold">Clock In</th>
          <th class="px-4 py-3 text-start font-semibold">Clock Out</th>
          <th class="px-4 py-3 text-start font-semibold">Hours</th>
          <th class="px-4 py-3 text-start font-semibold">Status</th>
          <th class="px-4 py-3 text-end font-semibold">Verify</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($records as $rec)
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3 font-medium text-slate-900">
            <a href="{{ route('business.attendance.show', $rec->clock_in_time->format('Y-m-d')) }}"
               class="hover:text-primary-600 transition">{{ $rec->clock_in_time->format('d M Y') }}</a>
          </td>
          <td class="px-4 py-3 text-slate-600">{{ $rec->clock_in_time->format('H:i') }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $rec->clock_out_time?->format('H:i') ?? '—' }}</td>
          <td class="px-4 py-3 font-semibold text-slate-900">{{ $rec->total_hours ? $rec->total_hours . 'h' : '—' }}</td>
          <td class="px-4 py-3">
            @php $c = ['clocked_in'=>'blue','on_break'=>'amber','clocked_out'=>'green'][$rec->status] ?? 'slate'; @endphp
            <span class="px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 text-[11px] font-semibold capitalize">{{ str_replace('_',' ',$rec->status) }}</span>
          </td>
          <td class="px-4 py-3 text-end">
            @if(!$rec->is_verified)
            <form action="{{ route('business.attendance.verify', $rec) }}" method="POST">
              @csrf
              <button class="text-xs text-primary-600 hover:underline font-semibold">Verify</button>
            </form>
            @else
            <span class="text-xs text-green-600 font-semibold">✓ Verified</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400 italic">No attendance records.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($records->hasPages()) <div>{{ $records->links() }}</div> @endif

</div>
@endsection
