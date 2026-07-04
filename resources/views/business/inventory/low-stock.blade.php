@extends('layouts.dashboard')
@section('title', 'Low Stock Alerts')
@section('content')
<div class="space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Low Stock Alerts</h2>
      <p class="text-slate-500 text-xs mt-0.5">{{ $items->count() }} items need restocking</p>
    </div>
    <a href="{{ route('business.inventory.index') }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">← All Inventory</a>
  </div>
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($items as $item)
    <div class="bg-white rounded-2xl border-2 border-amber-200 p-5">
      <div class="flex items-start justify-between mb-3">
        <div>
          <p class="font-bold text-slate-900">{{ $item->name }}</p>
          <p class="text-xs text-slate-400">{{ $item->sku ?? 'No SKU' }}</p>
        </div>
        <span class="px-2 py-1 rounded-lg bg-amber-100 text-amber-700 text-[10px] font-bold uppercase">Low</span>
      </div>
      <div class="space-y-1.5 text-xs">
        <div class="flex justify-between">
          <span class="text-slate-500">In stock</span>
          <span class="font-bold text-amber-700">{{ rtrim(rtrim(number_format($item->quantity_in_stock, 2), '0'), '.') }} {{ $item->unit }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Threshold</span>
          <span class="text-slate-600">{{ rtrim(rtrim(number_format($item->low_stock_threshold, 2), '0'), '.') }} {{ $item->unit }}</span>
        </div>
      </div>
      <form action="{{ route('business.inventory.restock', $item) }}" method="POST" class="mt-3 flex gap-2">
        @csrf
        <input type="number" name="quantity" step="0.01" min="0.01" placeholder="Qty" required class="flex-1 text-xs rounded-lg border border-slate-200 bg-slate-50 px-2 py-1.5 outline-none focus:border-primary-500">
        <button class="px-3 py-1.5 rounded-lg bg-green-500 text-white text-xs font-bold hover:bg-green-600 transition">Restock</button>
      </form>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-200 py-12 text-center text-slate-400 italic">All stock levels are healthy. ✓</div>
    @endforelse
  </div>
</div>
@endsection
