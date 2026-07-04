@extends('layouts.dashboard')
@section('title', isset($equipment) ? 'Edit Equipment' : 'Add Equipment')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-900">{{ isset($equipment) ? 'Edit Equipment' : 'Add Equipment' }}</h2>
    <a href="{{ route('business.equipment.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>Back
    </a>
  </div>

  <form action="{{ isset($equipment) ? route('business.equipment.update', $equipment) : route('business.equipment.store') }}" method="POST" class="space-y-6">
    @csrf @if(isset($equipment)) @method('PUT') @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6 grid sm:grid-cols-2 gap-5">
      @foreach([
        ['name'=>'name','label'=>'Name *','type'=>'text','required'=>true,'placeholder'=>'e.g. Drill Machine'],
        ['name'=>'category','label'=>'Category','type'=>'text','required'=>false,'placeholder'=>'e.g. Power Tools'],
        ['name'=>'brand','label'=>'Brand','type'=>'text','required'=>false,'placeholder'=>'e.g. Bosch'],
        ['name'=>'model','label'=>'Model','type'=>'text','required'=>false,'placeholder'=>''],
        ['name'=>'serial_number','label'=>'Serial Number','type'=>'text','required'=>false,'placeholder'=>''],
        ['name'=>'purchase_date','label'=>'Purchase Date','type'=>'date','required'=>false,'placeholder'=>''],
        ['name'=>'purchase_price','label'=>'Purchase Price','type'=>'number','required'=>false,'placeholder'=>'0.00'],
      ] as $f)
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ $f['label'] }}</label>
        <input type="{{ $f['type'] }}" name="{{ $f['name'] }}"
          value="{{ old($f['name'], $equipment->{$f['name']} ?? '') }}"
          @if($f['required']) required @endif
          placeholder="{{ $f['placeholder'] }}"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
      </div>
      @endforeach

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Condition <span class="text-red-500">*</span></label>
        <select name="condition" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
          @foreach(['new','good','fair','needs_repair','retired'] as $c)
          <option value="{{ $c }}" @selected(old('condition', $equipment->condition ?? 'good') === $c)>{{ ucwords(str_replace('_',' ',$c)) }}</option>
          @endforeach
        </select>
      </div>

      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Notes</label>
        <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition resize-none">{{ old('notes', $equipment->notes ?? '') }}</textarea>
      </div>
    </div>

    <div class="flex justify-end gap-3">
      <a href="{{ route('business.equipment.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Cancel</a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">Save</button>
    </div>
  </form>
</div>
@endsection
