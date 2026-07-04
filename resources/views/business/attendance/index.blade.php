@extends('layouts.dashboard')
@section('title', 'Attendance — ' . $date->format('d M Y'))

@section('content')
<div class="space-y-6 text-sm">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Attendance Board</h2>
      <p class="text-slate-500 text-xs mt-0.5">{{ $date->format('l, d F Y') }}</p>
    </div>
    {{-- Date picker --}}
    <form method="GET" class="flex items-center gap-2">
      <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none">
      <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">Go</button>
    </form>
  </div>

  @php
    $clockedIn = $records->where('status', 'clocked_in')->count();
    $completed = $records->where('status', 'clocked_out')->count();
    $absent    = $members->count() - $records->count();
  @endphp
  <div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Clocked In Now</p>
      <p class="text-2xl font-black text-blue-600 mt-1">{{ $clockedIn }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Completed</p>
      <p class="text-2xl font-black text-green-600 mt-1">{{ $completed }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Absent / Not Clocked In</p>
      <p class="text-2xl font-black text-slate-400 mt-1">{{ max(0, $absent) }}</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Member</th>
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
          <td class="px-5 py-3 font-medium text-slate-900">{{ $rec->member?->full_name ?? '—' }}</td>
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
        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400 italic">No attendance records for this date.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
