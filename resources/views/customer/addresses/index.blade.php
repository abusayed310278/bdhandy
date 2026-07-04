@extends('layouts.dashboard')
@section('title', 'My Addresses')

@section('content')
<div class="space-y-5 text-sm">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h2 class="text-xl font-bold text-slate-900">My Addresses</h2>
      <p class="text-slate-500 text-xs mt-0.5">Manage your saved locations</p>
    </div>
    <a href="{{ route('customer.addresses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Address
    </a>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  @if($addresses->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      <p class="text-slate-500 font-medium">No addresses saved</p>
      <p class="text-slate-400 text-xs mt-1">Add your home or office address for faster booking</p>
      <a href="{{ route('customer.addresses.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">Add Now</a>
    </div>
  @else
    <div class="grid sm:grid-cols-2 gap-4">
      @foreach($addresses as $addr)
      <div class="bg-white rounded-2xl border {{ $addr->is_primary ? 'border-primary-300 ring-1 ring-primary-200' : 'border-slate-200' }} p-5">
        <div class="flex items-start justify-between gap-2">
          <div class="flex items-center gap-2">
            @php
              $typeIcon = match($addr->address_type) {
                'house' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
                'office' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
                default  => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
              };
            @endphp
            <div class="w-9 h-9 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $typeIcon !!}</svg>
            </div>
            <div>
              <div class="flex items-center gap-1.5">
                <p class="font-semibold text-slate-900">{{ $addr->label }}</p>
                @if($addr->is_primary)
                  <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-primary-100 text-primary-700 font-bold">Primary</span>
                @endif
              </div>
              <p class="text-[11px] text-slate-400 capitalize">{{ $addr->address_type }}</p>
            </div>
          </div>
          <div class="flex items-center gap-1 shrink-0">
            <a href="{{ route('customer.addresses.edit', $addr) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
            <form action="{{ route('customer.addresses.destroy', $addr) }}" method="POST" onsubmit="return confirm('Delete this address?')">
              @csrf @method('DELETE')
              <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
              </button>
            </form>
          </div>
        </div>

        <p class="mt-3 text-xs text-slate-600 leading-relaxed">{{ $addr->address }}</p>
        @if($addr->area || $addr->district || $addr->division)
          <p class="text-[11px] text-slate-400 mt-1">
            {{ collect([$addr->area?->name, $addr->district?->name, $addr->division?->name])->filter()->implode(', ') }}
          </p>
        @endif

        @if(!$addr->is_primary)
          <form action="{{ route('customer.addresses.primary', $addr) }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="text-[11px] text-primary-600 hover:text-primary-700 font-medium">Set as primary</button>
          </form>
        @endif
      </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
