@extends('layouts.dashboard')
@section('title', $item->name . ' — Transactions')
@section('content')
<div class="max-w-4xl mx-auto space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">{{ $item->name }}</h2>
      <p class="text-slate-500 text-xs mt-0.5">Transaction history · Current stock: <span class="font-bold text-slate-900">{{ rtrim(rtrim(number_format($item->quantity_in_stock, 2), '0'), '.') }} {{ $item->unit }}</span></p>
    </div>
    <a href="{{ route('business.inventory.index') }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">← Back</a>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Date</th>
          <th class="px-4 py-3 text-start font-semibold">Type</th>
          <th class="px-4 py-3 text-end font-semibold">Change</th>
          <th class="px-4 py-3 text-end font-semibold">Before → After</th>
          <th class="px-4 py-3 text-start font-semibold">Reference</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($transactions as $tx)
        <tr>
          <td class="px-5 py-3 text-slate-600">{{ $tx->created_at->format('d M Y H:i') }}</td>
          <td class="px-4 py-3">
            @php $c=['restock'=>'green','usage'=>'red','adjustment'=>'blue','return'=>'amber','loss'=>'red'][$tx->transaction_type]??'slate'; @endphp
            <span class="px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 text-[11px] font-semibold capitalize">{{ $tx->transaction_type }}</span>
          </td>
          <td class="px-4 py-3 text-end font-bold {{ $tx->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $tx->quantity > 0 ? '+' : '' }}{{ $tx->quantity }}
          </td>
          <td class="px-4 py-3 text-end text-slate-600 text-xs">{{ $tx->quantity_before }} → {{ $tx->quantity_after }}</td>
          <td class="px-4 py-3 text-xs text-slate-500">{{ $tx->reference_type ?? 'manual' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400 italic">No transactions yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($transactions->hasPages())<div>{{ $transactions->links() }}</div>@endif
</div>
@endsection
