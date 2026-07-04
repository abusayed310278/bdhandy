@extends('layouts.dashboard')
@section('title', 'Inventory')

@section('content')
<div class="space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Inventory</h2>
      <p class="text-slate-500 text-xs mt-0.5">Parts and consumables stock</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('business.inventory.low-stock') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-amber-200 text-amber-700 text-xs font-semibold hover:bg-amber-50 transition shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
        Low Stock
      </a>
      <a href="{{ route('business.inventory.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition shadow-soft">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Item
      </a>
    </div>
  </div>

  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Item</th>
          <th class="px-4 py-3 text-start font-semibold">SKU</th>
          <th class="px-4 py-3 text-start font-semibold">Category</th>
          <th class="px-4 py-3 text-end font-semibold">Stock</th>
          <th class="px-4 py-3 text-end font-semibold">Unit Cost</th>
          <th class="px-4 py-3 text-end font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($items as $item)
        <tr class="hover:bg-slate-50 transition {{ $item->isLowStock() ? 'bg-amber-50/50' : '' }}">
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              @if($item->photo)
                <img src="{{ Storage::url($item->photo) }}" class="w-10 h-10 rounded-xl object-cover border border-slate-100 shrink-0">
              @else
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center border border-slate-200 border-dashed text-slate-400 shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
              @endif
              <div>
                <p class="font-semibold text-slate-900">{{ $item->name }}</p>
                <p class="text-[11px] text-slate-400">{{ $item->supplier_name ?? 'No supplier' }}</p>
              </div>
            </div>
          </td>
          <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $item->sku ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $item->category ?? '—' }}</td>
          <td class="px-4 py-3 text-end">
            <p class="font-bold text-slate-900">{{ rtrim(rtrim(number_format($item->quantity_in_stock, 2), '0'), '.') }} <span class="text-xs text-slate-400 font-medium">{{ $item->unit }}</span></p>
            @if($item->isLowStock())
              <p class="text-[10px] text-amber-600 font-bold">⚠ Low stock</p>
            @endif
          </td>
          <td class="px-4 py-3 text-end text-slate-600">{{ $item->unit_cost ? number_format($item->unit_cost, 2) : '—' }}</td>
          <td class="px-4 py-3 text-end">
            <div class="flex items-center justify-end gap-1">
              <form action="{{ route('business.inventory.restock', $item) }}" method="POST" class="inline-flex items-center gap-1">
                @csrf
                <input type="number" name="quantity" step="0.01" min="0.01" placeholder="Qty" class="w-16 text-xs rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 outline-none focus:border-primary-500">
                <button class="px-2 py-1 rounded-lg bg-green-50 text-green-600 text-xs font-semibold hover:bg-green-100 transition">+ Restock</button>
              </form>
              <a href="{{ route('business.inventory.transactions', $item) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Transactions">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
              </a>
              <a href="{{ route('business.inventory.edit', $item) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">No inventory items.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($items->hasPages())<div>{{ $items->links() }}</div>@endif
</div>
@endsection
