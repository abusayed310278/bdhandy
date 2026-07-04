@extends('layouts.dashboard')
@section('title', $vehicle->plate_number . ' — Maintenance')
@section('content')
<div class="max-w-3xl mx-auto space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-900">{{ $vehicle->plate_number }} — Maintenance</h2>
    <a href="{{ route('business.vehicles.index') }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold">← Back</a>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-900 mb-4">Add Maintenance Record</h3>
    <form action="{{ route('business.vehicles.maintenance.store', $vehicle) }}" method="POST" class="grid sm:grid-cols-2 gap-4">
      @csrf
      <div><label class="block text-xs font-semibold mb-1">Type *</label>
        <select name="maintenance_type" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 appearance-none">
          @foreach(['oil_change'=>'Oil Change','tyre'=>'Tyre','brake'=>'Brake','engine'=>'Engine','body'=>'Body','inspection'=>'Inspection','other'=>'Other'] as $v=>$l)
          <option value="{{ $v }}">{{ $l }}</option>@endforeach
        </select></div>
      <div><label class="block text-xs font-semibold mb-1">Status *</label>
        <select name="status" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 appearance-none">
          <option value="scheduled">Scheduled</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option>
        </select></div>
      <div><label class="block text-xs font-semibold mb-1">Date *</label><input type="date" name="maintenance_date" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div><label class="block text-xs font-semibold mb-1">Next Date</label><input type="date" name="next_service_date" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div><label class="block text-xs font-semibold mb-1">Odometer</label><input type="number" name="odometer_at_service" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div><label class="block text-xs font-semibold mb-1">Workshop</label><input type="text" name="workshop_name" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div><label class="block text-xs font-semibold mb-1">Cost</label><input type="number" step="0.01" name="cost" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div class="sm:col-span-2"><label class="block text-xs font-semibold mb-1">Description</label><textarea name="description" rows="2" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 resize-none"></textarea></div>
      <div class="sm:col-span-2 flex justify-end"><button class="px-5 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold">Save</button></div>
    </form>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100"><tr class="text-xs text-slate-500 uppercase tracking-wider">
        <th class="px-5 py-3 text-start font-semibold">Date</th>
        <th class="px-4 py-3 text-start font-semibold">Type</th>
        <th class="px-4 py-3 text-start font-semibold">Workshop</th>
        <th class="px-4 py-3 text-end font-semibold">Cost</th>
        <th class="px-4 py-3 text-start font-semibold">Status</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($records as $r)
        <tr>
          <td class="px-5 py-3">{{ $r->maintenance_date->format('d M Y') }}</td>
          <td class="px-4 py-3 capitalize text-slate-600">{{ str_replace('_',' ',$r->maintenance_type) }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $r->workshop_name ?? '—' }}</td>
          <td class="px-4 py-3 text-end">{{ $r->cost ? number_format($r->cost, 2) : '—' }}</td>
          <td class="px-4 py-3">
            @php $c=['scheduled'=>'blue','completed'=>'green','cancelled'=>'slate'][$r->status]??'slate'; @endphp
            <span class="px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 text-[11px] font-semibold capitalize">{{ $r->status }}</span>
          </td>
        </tr>
        @empty<tr><td colspan="5" class="px-5 py-8 text-center text-slate-400 italic">No maintenance records.</td></tr>@endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
