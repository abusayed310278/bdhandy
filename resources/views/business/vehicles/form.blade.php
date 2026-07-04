@extends('layouts.dashboard')
@section('title', isset($vehicle) ? 'Edit Vehicle' : 'Add Vehicle')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-900">{{ isset($vehicle) ? 'Edit Vehicle' : 'Add Vehicle' }}</h2>
    <a href="{{ route('business.vehicles.index') }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">← Back</a>
  </div>

  <form action="{{ isset($vehicle) ? route('business.vehicles.update', $vehicle) : route('business.vehicles.store') }}" method="POST" class="space-y-6">
    @csrf @if(isset($vehicle)) @method('PUT') @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Identity</h3>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Type <span class="text-red-500">*</span></label>
          <select name="vehicle_type" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
            @foreach(['bike','car','van','truck','other'] as $t)
            <option value="{{ $t }}" @selected(old('vehicle_type', $vehicle->vehicle_type ?? '') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Plate Number <span class="text-red-500">*</span></label>
          <input type="text" name="plate_number" required value="{{ old('plate_number', $vehicle->plate_number ?? '') }}"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition">
        </div>
        @foreach([
          ['n'=>'make','label'=>'Make','type'=>'text'],
          ['n'=>'model','label'=>'Model','type'=>'text'],
          ['n'=>'year','label'=>'Year','type'=>'number'],
          ['n'=>'color','label'=>'Color','type'=>'text'],
          ['n'=>'vin','label'=>'VIN / Chassis #','type'=>'text'],
          ['n'=>'current_odometer_km','label'=>'Current Odometer (km)','type'=>'number'],
        ] as $f)
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ $f['label'] }}</label>
          <input type="{{ $f['type'] }}" name="{{ $f['n'] }}" value="{{ old($f['n'], $vehicle->{$f['n']} ?? '') }}"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition">
        </div>
        @endforeach
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Compliance & Fuel</h3>
      <div class="grid sm:grid-cols-2 gap-5">
        @foreach([
          ['n'=>'registration_expiry','label'=>'Registration Expiry','type'=>'date'],
          ['n'=>'insurance_expiry','label'=>'Insurance Expiry','type'=>'date'],
          ['n'=>'fitness_expiry','label'=>'Fitness Expiry','type'=>'date'],
          ['n'=>'fuel_tank_capacity_liters','label'=>'Tank Capacity (L)','type'=>'number'],
        ] as $f)
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ $f['label'] }}</label>
          <input type="{{ $f['type'] }}" name="{{ $f['n'] }}"
            value="{{ old($f['n'], (isset($vehicle) && $vehicle->{$f['n']}) ? ($f['type']==='date' ? $vehicle->{$f['n']}->format('Y-m-d') : $vehicle->{$f['n']}) : '') }}"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition">
        </div>
        @endforeach
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Fuel Type <span class="text-red-500">*</span></label>
          <select name="fuel_type" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
            @foreach(['petrol','diesel','cng','electric'] as $ft)
            <option value="{{ $ft }}" @selected(old('fuel_type', $vehicle->fuel_type ?? 'petrol') === $ft)>{{ ucfirst($ft) }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Notes</label>
        <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition resize-none">{{ old('notes', $vehicle->notes ?? '') }}</textarea>
      </div>
    </div>

    <div class="flex justify-end gap-3">
      <a href="{{ route('business.vehicles.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Cancel</a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">Save</button>
    </div>
  </form>
</div>
@endsection
