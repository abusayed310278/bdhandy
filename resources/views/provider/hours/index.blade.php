@extends('layouts.dashboard')
@section('title', 'Hours & Holidays')

@section('content')
<div class="space-y-6 text-sm">

  <div>
    <h2 class="text-xl font-bold text-slate-900">Hours & Holidays</h2>
    <p class="text-slate-500 text-xs mt-0.5">Set your weekly schedule and mark holiday dates</p>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  {{-- Business Hours --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-900">Weekly Schedule</h3>
      <p class="text-xs text-slate-500 mt-0.5">Set your working hours for each day</p>
    </div>

    <form action="{{ route('provider.hours.update') }}" method="POST" 
      x-data="{
        globalStart: '09:00',
        globalEnd: '17:00',
        schedule: [
          @foreach($days as $day)
          @php
            $h = $existing->get($day->id);
            $isClosed = $h ? $h->is_closed : false;
          @endphp
          {
            id: {{ $day->id }},
            closed: {{ $isClosed ? 'true' : 'false' }},
            start: '{{ $h?->start_time ?? '09:00' }}',
            end: '{{ $h?->end_time ?? '17:00' }}'
          },
          @endforeach
        ],
        applyGlobal() {
          this.schedule.forEach(d => {
            if (!d.closed) {
              d.start = this.globalStart;
              d.end = this.globalEnd;
            }
          });
        }
      }" class="p-5 space-y-3">
      @csrf

      {{-- Bulk Actions --}}
      <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 mb-6">
        <div class="flex flex-col sm:flex-row items-center gap-4">
          <div class="flex items-center gap-2 grow">
            <span class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20"/></svg>
            </span>
            <div>
              <p class="text-xs font-bold text-slate-900">Bulk Set Times</p>
              <p class="text-[10px] text-slate-500">Apply same hours to all open days</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <input type="time" x-model="globalStart" class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-primary-500 transition">
            <span class="text-slate-400 text-xs">to</span>
            <input type="time" x-model="globalEnd" class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-primary-500 transition">
            <button type="button" @click="applyGlobal()" 
              class="px-3 py-1.5 rounded-lg bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition shadow-sm">
              Apply All
            </button>
          </div>
        </div>
      </div>

      @foreach($days as $day)
      @php
        $dayName = $day->getTranslation('translations','en');
      @endphp
      <div class="flex flex-col sm:flex-row sm:items-center gap-3 py-3 border-b border-slate-100 last:border-0"
           x-data="{ get item() { return schedule.find(i => i.id === {{ $day->id }}) } }">
        <div class="w-28 shrink-0">
          <p class="font-medium text-slate-800">{{ $dayName }}</p>
        </div>
        <label class="flex items-center gap-2 cursor-pointer shrink-0">
          <input type="hidden" name="hours[{{ $day->id }}][is_closed]" value="0">
          <input type="checkbox" name="hours[{{ $day->id }}][is_closed]" value="1"
            x-model="item.closed"
            class="w-4 h-4 rounded border-slate-300 text-slate-500 focus:ring-slate-200">
          <span class="text-xs text-slate-500">Closed</span>
        </label>
        <div x-show="!item.closed" class="flex items-center gap-3 flex-1">
          <div class="flex items-center gap-2 flex-1">
            <label class="text-xs text-slate-500 shrink-0">From</label>
            <input type="time" name="hours[{{ $day->id }}][start_time]" x-model="item.start"
              class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>
          <div class="flex items-center gap-2 flex-1">
            <label class="text-xs text-slate-500 shrink-0">To</label>
            <input type="time" name="hours[{{ $day->id }}][end_time]" x-model="item.end"
              class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>
        </div>
        <div x-show="item.closed" class="flex-1">
          <span class="text-xs text-slate-400 italic">Not available</span>
        </div>
      </div>
      @endforeach

      <div class="flex justify-end pt-2">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
          Save Schedule
        </button>
      </div>
    </form>
  </div>

  {{-- Holidays --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-900">Holidays</h3>
      <p class="text-xs text-slate-500 mt-0.5">Mark specific dates when you won't be available</p>
    </div>

    <div class="p-5 space-y-4">
      {{-- Add Holiday Form --}}
      <form action="{{ route('provider.holidays.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="date" name="date_of_holiday" required
          min="{{ now()->toDateString() }}"
          class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        <input type="text" name="reason" placeholder="Reason (optional)"
          class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent-500 text-white text-sm font-bold hover:bg-accent-600 transition shrink-0">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add
        </button>
      </form>

      @if($holidays->isEmpty())
        <p class="text-slate-400 text-xs py-4 text-center">No holidays added. Customers can book any day your schedule allows.</p>
      @else
        <div class="space-y-2">
          @foreach($holidays as $holiday)
          <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex flex-col items-center justify-center shrink-0">
                <span class="text-[10px] font-bold uppercase">{{ $holiday->date_of_holiday->format('M') }}</span>
                <span class="text-sm font-bold leading-none">{{ $holiday->date_of_holiday->format('d') }}</span>
              </div>
              <div>
                <p class="font-medium text-slate-800">{{ $holiday->date_of_holiday->format('l, d F Y') }}</p>
                @if($holiday->reason)
                  <p class="text-xs text-slate-500 mt-0.5">{{ $holiday->reason }}</p>
                @endif
              </div>
            </div>
            <form action="{{ route('provider.holidays.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Remove this holiday?')">
              @csrf @method('DELETE')
              <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
              </button>
            </form>
          </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

</div>
@endsection
