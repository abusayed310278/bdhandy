@extends('layouts.dashboard')
@section('title', 'Job — ' . $assignment->request?->request_number)

@section('content')
<div class="max-w-2xl mx-auto space-y-6 text-sm">
  <div>
    <h2 class="text-xl font-bold text-slate-900">{{ $assignment->request?->request_number ?? 'Job Detail' }}</h2>
    <p class="text-slate-500 text-xs mt-0.5">{{ $assignment->request?->address }}</p>
  </div>

  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif

  {{-- Status update --}}
  @php
    $done        = in_array($assignment->status, ['completed','rejected','reassigned']);
    $isAssigned  = $assignment->status === 'assigned';
    $isAccepted  = !$isAssigned && !$done;
    $scheduledDate = $assignment->scheduled_start_time?->startOfDay();
    $canOperate  = !$scheduledDate || today()->gte($scheduledDate);
  @endphp

  @if(!$done)
  <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">

    @if($isAssigned)
      {{-- Step 1: only Accept / Reject --}}
      <h3 class="font-semibold text-slate-900">Respond to Assignment</h3>
      <form action="{{ route('tech.jobs.update-status', $assignment) }}" method="POST" class="flex flex-wrap gap-2">
        @csrf
        <button name="status" value="accepted"
                class="px-5 py-2 rounded-xl bg-green-500 text-white text-xs font-bold hover:bg-green-600 transition">
          Accept
        </button>
        <button name="status" value="rejected"
                class="px-5 py-2 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-bold hover:bg-red-100 transition">
          Reject
        </button>
      </form>

    @elseif($isAccepted)
      {{-- Step 2: operational statuses, date-gated --}}
      <h3 class="font-semibold text-slate-900">Update Job Status</h3>

      @if(!$canOperate)
      <div class="flex items-center gap-2 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3">
        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p class="text-xs font-semibold text-amber-800">
          Operational actions available from <span class="font-black">{{ $scheduledDate->format('d F Y') }}</span>
        </p>
      </div>
      @else
      <form action="{{ route('tech.jobs.update-status', $assignment) }}" method="POST" class="flex flex-wrap gap-2">
        @csrf
        @foreach([
          'en_route'    => ['En Route',     'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-primary-50 hover:border-primary-200 hover:text-primary-700'],
          'arrived'     => ['Arrived',      'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-primary-50 hover:border-primary-200 hover:text-primary-700'],
          'in_progress' => ['Start Work',   'bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100'],
          'paused'      => ['Pause',        'bg-amber-50 border border-amber-200 text-amber-700 hover:bg-amber-100'],
          'completed'   => ['Mark Complete','bg-green-500 text-white hover:bg-green-600'],
        ] as $s => [$label, $cls])
          @if($assignment->status !== $s)
          <button name="status" value="{{ $s }}"
                  class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $cls }}">{{ $label }}</button>
          @endif
        @endforeach
      </form>
      @endif

    @endif
  </div>
  @endif

  {{-- Log Materials --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4" x-data="{ items: [{ inventory_id: '', quantity: 1, notes: '' }] }">
    <h3 class="font-semibold text-slate-900">Log Materials Used</h3>
    <form action="{{ route('tech.jobs.log-materials', $assignment) }}" method="POST" class="space-y-3">
      @csrf
      <template x-for="(item, i) in items" :key="i">
        <div class="flex items-center gap-2">
          <select :name="`materials[${i}][inventory_id]`" x-model="item.inventory_id" required
            class="flex-1 text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 focus:border-primary-500 outline-none appearance-none">
            <option value="">Select item...</option>
            @foreach($inventory as $inv)
            <option value="{{ $inv->id }}">{{ $inv->name }} ({{ $inv->quantity_in_stock }} {{ $inv->unit }})</option>
            @endforeach
          </select>
          <input :name="`materials[${i}][quantity]`" x-model="item.quantity" type="number" step="0.01" min="0.01" placeholder="Qty"
            class="w-20 text-xs rounded-xl border border-slate-200 bg-slate-50 px-2 py-2 focus:border-primary-500 outline-none">
          <button type="button" @click="items.splice(i, 1)" class="text-slate-400 hover:text-red-500 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
      </template>
      <button type="button" @click="items.push({inventory_id:'',quantity:1,notes:''})"
        class="text-xs text-primary-600 font-semibold hover:underline">+ Add item</button>
      <div class="pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition">Log Materials</button>
      </div>
    </form>
  </div>

  {{-- Already logged --}}
  @if($assignment->materialUsage->isNotEmpty())
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    <h3 class="font-semibold text-slate-900 mb-3">Materials Already Logged</h3>
    <div class="space-y-2">
      @foreach($assignment->materialUsage as $m)
      <div class="flex items-center gap-3 text-xs">
        <span class="font-medium text-slate-700">{{ $m->inventory?->name }}</span>
        <span class="text-slate-400">{{ $m->quantity_used }} {{ $m->inventory?->unit }}</span>
        <span class="ms-auto text-slate-400">{{ $m->logged_at->format('H:i') }}</span>
      </div>
      @endforeach
    </div>
  </div>
  @endif
</div>
@endsection
