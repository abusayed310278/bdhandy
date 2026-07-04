@extends('layouts.dashboard')
@section('title', isset($item) ? 'Edit Item' : 'Add Inventory Item')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-900">{{ isset($item) ? 'Edit Item' : 'Add Inventory Item' }}</h2>
    <a href="{{ route('business.inventory.index') }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">← Back</a>
  </div>

  <form action="{{ isset($item) ? route('business.inventory.update', $item) : route('business.inventory.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf @if(isset($item)) @method('PUT') @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6 grid sm:grid-cols-2 gap-5">
      @foreach([
        ['name'=>'name','label'=>'Name *','type'=>'text','req'=>true,'ph'=>'e.g. AC Capacitor 25µF'],
        ['name'=>'sku','label'=>'SKU','type'=>'text','req'=>false,'ph'=>'optional'],
        ['name'=>'category','label'=>'Category','type'=>'text','req'=>false,'ph'=>'e.g. HVAC Parts'],
        ['name'=>'unit','label'=>'Unit','type'=>'text','req'=>false,'ph'=>'pcs, meters, kg'],
        ['name'=>'quantity_in_stock','label'=>'Quantity in Stock','type'=>'number','req'=>false,'ph'=>'0'],
        ['name'=>'low_stock_threshold','label'=>'Low Stock Threshold','type'=>'number','req'=>false,'ph'=>'5'],
        ['name'=>'unit_cost','label'=>'Unit Cost','type'=>'number','req'=>false,'ph'=>'0.00'],
      ] as $f)
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ $f['label'] }}</label>
        <input type="{{ $f['type'] }}" name="{{ $f['name'] }}" @if($f['type']==='number') step="0.01" min="0" @endif
          value="{{ old($f['name'], $item->{$f['name']} ?? '') }}"
          @if($f['req']) required @endif placeholder="{{ $f['ph'] }}"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
      </div>
      @endforeach

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Currency</label>
        <select name="cost_currency_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
          <option value="">— Default —</option>
          @foreach($currencies as $c)
          <option value="{{ $c->id }}" @selected(old('cost_currency_id', $item->cost_currency_id ?? null) == $c->id)>{{ $c->name }} ({{ $c->symbol }})</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Supplier</label>
        <input type="text" name="supplier_name" value="{{ old('supplier_name', $item->supplier_name ?? '') }}"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Supplier Contact</label>
        <input type="text" name="supplier_contact" value="{{ old('supplier_contact', $item->supplier_contact ?? '') }}"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition">
      </div>

      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Item Photo</label>
        <div class="flex items-center gap-4">
          @if(isset($item) && $item->photo)
            <img src="{{ Storage::url($item->photo) }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shrink-0">
          @else
            <div class="w-16 h-16 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-200 border-dashed text-slate-400 shrink-0">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
          @endif
          <div class="flex-1">
            <input type="file" name="photo" accept="image/*"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 outline-none transition">
            <p class="text-[11px] text-slate-400 mt-1">Recommended size: 512x512 px. Images will be automatically resized and cropped to square.</p>
          </div>
        </div>
        @error('photo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Notes</label>
        <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition resize-none">{{ old('notes', $item->notes ?? '') }}</textarea>
      </div>
    </div>

    <div class="flex justify-end gap-3">
      <a href="{{ route('business.inventory.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Cancel</a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">Save</button>
    </div>
  </form>
</div>
@endsection
