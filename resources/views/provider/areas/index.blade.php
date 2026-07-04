@extends('layouts.dashboard')
@section('title', 'Service Areas')

@section('content')
<div class="space-y-5 text-sm">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Service Areas</h2>
      <p class="text-slate-500 text-xs mt-0.5">Define the geographic zones where you provide services</p>
    </div>
    <a href="{{ route('provider.areas.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Area
    </a>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  @if($areas->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      <p class="text-slate-500 font-medium">No service areas defined</p>
      <p class="text-slate-400 text-xs mt-1">Add areas to tell customers where you operate</p>
      <a href="{{ route('provider.areas.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">Add First Area</a>
    </div>
  @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @foreach($areas as $area)
      <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col gap-3">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <p class="font-semibold text-slate-900">
              {{ $area->district?->name ?? '—' }}{{ $area->area ? ', ' . $area->area->name : '' }}
            </p>
            <p class="text-xs text-slate-500 mt-0.5">{{ $area->division?->name }} · {{ $area->country?->name }}</p>
            @if($area->address)
              <p class="text-xs text-slate-400 mt-1">{{ $area->address }}</p>
            @endif
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('provider.areas.edit', $area) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
            <form action="{{ route('provider.areas.destroy', $area) }}" method="POST" onsubmit="return confirm('Remove this service area?')">
              @csrf @method('DELETE')
              <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
              </button>
            </form>
          </div>
        </div>

        @if($area->radius_km)
          <div class="flex items-center gap-2 text-xs text-slate-500">
            <svg class="w-3.5 h-3.5 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
            {{ $area->radius_km }} km service radius
          </div>
        @endif

        @if($area->latitude && $area->longitude)
          <div class="text-[11px] text-slate-400 font-mono">{{ number_format($area->latitude, 4) }}, {{ number_format($area->longitude, 4) }}</div>
        @endif
      </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
