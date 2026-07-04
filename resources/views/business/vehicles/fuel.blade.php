@extends('layouts.dashboard')
@section('title', $vehicle->plate_number . ' — Fuel Records')
@section('content')
<div class="max-w-3xl mx-auto space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">{{ $vehicle->plate_number }} — Fuel Log</h2>
    </div>
    <a href="{{ route('business.vehicles.index') }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold">← Back</a>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-900 mb-4">Add Fuel Record</h3>
    <form action="{{ route('business.vehicles.fuel.store', $vehicle) }}" method="POST" class="grid sm:grid-cols-2 gap-4">
      @csrf
      <div><label class="block text-xs font-semibold text-slate-700 mb-1">Date *</label><input type="date" name="fuel_date" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div><label class="block text-xs font-semibold text-slate-700 mb-1">Liters *</label><input type="number" step="0.01" name="liters_filled" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div><label class="block text-xs font-semibold text-slate-700 mb-1">Cost/Liter</label><input type="number" step="0.01" name="cost_per_liter" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div><label class="block text-xs font-semibold text-slate-700 mb-1">Odometer</label><input type="number" name="odometer_reading" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div class="sm:col-span-2"><label class="block text-xs font-semibold text-slate-700 mb-1">Station</label><input type="text" name="station_name" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"></div>
      <div class="sm:col-span-2 flex justify-end"><button class="px-5 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition">Save</button></div>
    </form>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100"><tr class="text-xs text-slate-500 uppercase tracking-wider">
        <th class="px-5 py-3 text-start font-semibold">Date</th>
        <th class="px-4 py-3 text-end font-semibold">Liters</th>
        <th class="px-4 py-3 text-end font-semibold">Cost</th>
        <th class="px-4 py-3 text-end font-semibold">Odometer</th>
        <th class="px-4 py-3 text-start font-semibold">Station</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($records as $r)
        <tr>
          <td class="px-5 py-3">{{ $r->fuel_date->format('d M Y') }}</td>
          <td class="px-4 py-3 text-end">{{ $r->liters_filled }}</td>
          <td class="px-4 py-3 text-end font-semibold">{{ $r->total_cost ? number_format($r->total_cost, 2) : '—' }}</td>
          <td class="px-4 py-3 text-end text-slate-600">{{ $r->odometer_reading ? number_format($r->odometer_reading) : '—' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $r->station_name ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400 italic">No fuel records.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
