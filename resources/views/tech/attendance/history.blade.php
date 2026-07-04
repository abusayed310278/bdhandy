@extends('layouts.dashboard')
@section('title', 'My Attendance')

@section('content')
<div class="space-y-6 text-sm" x-data="{ showClockIn: false }">

  <div>
    <h2 class="text-xl font-bold text-slate-900">My Attendance</h2>
    <p class="text-slate-500 text-xs mt-0.5">Clock in/out and view your history</p>
  </div>

  {{-- ── Clock-In / Clock-Out Widget ───────────────────────────────────────── --}}
  @if($canClockInOut)
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    @if($openRecord)
    {{-- Currently clocked in → Clock Out --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4"
         x-data="{ lat: null, lng: null, busy: false }">
      <div class="flex items-center gap-3 flex-1">
        <span class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </span>
        <div>
          <p class="font-bold text-slate-900">Currently Clocked In</p>
          <p class="text-xs text-slate-500 mt-0.5">Since {{ $openRecord->clock_in_time->format('H:i') }} — {{ $openRecord->clock_in_time->diffForHumans() }}</p>
          @if($openRecord->clock_in_address)
            <p class="text-xs text-slate-400 mt-0.5">{{ $openRecord->clock_in_address }}</p>
          @endif
        </div>
      </div>

      <form id="clock-out-form" action="{{ route('tech.attendance.clock-out') }}" method="POST">
        @csrf
        <input type="hidden" name="latitude"  :value="lat">
        <input type="hidden" name="longitude" :value="lng">
        <button type="button" :disabled="busy"
                @click="
                  busy = true;
                  if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                      p  => { lat = p.coords.latitude; lng = p.coords.longitude; $nextTick(() => $el.closest('form').submit()); },
                      () => { $el.closest('form').submit(); },
                      { timeout: 6000 }
                    );
                  } else {
                    $el.closest('form').submit();
                  }
                "
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-red-500 text-white font-bold text-xs hover:bg-red-600 disabled:opacity-60 disabled:cursor-not-allowed transition shadow-sm flex items-center gap-2">
          <svg x-show="busy" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <span x-text="busy ? 'Getting location…' : 'Clock Out'">Clock Out</span>
        </button>
      </form>
    </div>

    @else
    {{-- Not clocked in → Clock In --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4"
         x-data="{ open: false, lat: null, lng: null, locStatus: 'idle' }">
      {{-- Status info --}}
      <div class="flex items-center gap-3 flex-1">
        <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
              :class="open ? 'bg-primary-100' : 'bg-slate-100'">
          <svg class="w-5 h-5" :class="open ? 'text-primary-500' : 'text-slate-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </span>
        <div>
          <p class="font-bold text-slate-700" x-text="open ? 'Clock In' : 'Not Clocked In'">Not Clocked In</p>
          <p class="text-xs text-slate-400 mt-0.5">{{ now()->format('l, d F Y') }}</p>

          {{-- Location status badge --}}
          <p x-show="locStatus === 'getting'" class="text-[11px] text-primary-500 mt-0.5 flex items-center gap-1">
            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Getting your location…
          </p>
          <p x-show="locStatus === 'ok'" class="text-[11px] text-green-600 mt-0.5 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Location captured
          </p>
          <p x-show="locStatus === 'denied'" class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            Location unavailable — clocking in without it
          </p>
        </div>
      </div>

      {{-- Clock In button --}}
      <div x-show="!open">
        <button
          @click="
            open = true;
            locStatus = 'getting';
            if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(
                p  => { lat = p.coords.latitude; lng = p.coords.longitude; locStatus = 'ok'; },
                () => { locStatus = 'denied'; },
                { timeout: 6000 }
              );
            } else {
              locStatus = 'denied';
            }
          "
          class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-primary-500 text-white font-bold text-xs hover:bg-primary-600 transition shadow-sm">
          Clock In
        </button>
      </div>

      {{-- Expanded form --}}
      <form x-show="open" action="{{ route('tech.attendance.clock-in') }}" method="POST"
            class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto" style="display:none">
        @csrf
        <input type="hidden" name="latitude"  :value="lat">
        <input type="hidden" name="longitude" :value="lng">
        <input type="text" name="address" placeholder="Location note (optional)"
               class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition w-full sm:w-52">
        <div class="flex gap-2">
          <button type="submit"
                  class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-primary-500 text-white font-bold text-xs hover:bg-primary-600 transition">
            Confirm
          </button>
          <button type="button" @click="open = false; locStatus = 'idle'; lat = null; lng = null;"
                  class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold text-xs hover:bg-slate-200 transition">
            Cancel
          </button>
        </div>
      </form>
    </div>
    @endif
  </div>
  @endif

  {{-- ── History Table ──────────────────────────────────────────────────────── --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
      <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Attendance History</p>
    </div>
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Date</th>
          <th class="px-4 py-3 text-start font-semibold">Clock In</th>
          <th class="px-4 py-3 text-start font-semibold">Clock Out</th>
          <th class="px-4 py-3 text-start font-semibold">Hours</th>
          <th class="px-4 py-3 text-start font-semibold">Status</th>
          <th class="px-4 py-3 text-start font-semibold">Verified</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($records as $rec)
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3 font-medium text-slate-900">{{ $rec->clock_in_time->format('d M Y') }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $rec->clock_in_time->format('H:i') }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $rec->clock_out_time?->format('H:i') ?? '—' }}</td>
          <td class="px-4 py-3 font-semibold text-slate-900">{{ $rec->total_hours ? $rec->total_hours.'h' : '—' }}</td>
          <td class="px-4 py-3">
            @php $c = ['clocked_in'=>'blue','on_break'=>'amber','clocked_out'=>'green'][$rec->status] ?? 'slate'; @endphp
            <span class="px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 text-[11px] font-semibold capitalize">{{ str_replace('_',' ',$rec->status) }}</span>
          </td>
          <td class="px-4 py-3">
            @if($rec->is_verified)
              <span class="text-[11px] font-semibold text-green-600">✓ Verified</span>
            @else
              <span class="text-[11px] text-slate-400">Pending</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400 italic">No records yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($records->hasPages()) <div>{{ $records->links() }}</div> @endif
</div>
@endsection
