@extends('layouts.dashboard')
@section('title', 'Wallet')

@section('content')
<div class="space-y-6 text-sm">

  @if(session('success'))
    <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
  @endif

  <div>
    <h2 class="text-xl font-bold text-slate-900">Wallet</h2>
    <p class="text-slate-500 text-xs mt-0.5">Top up your balance to pay for subscriptions — plans are auto-renewed from this balance every month.</p>
  </div>

  {{-- Balance + Top-up --}}
  <div class="bg-gradient-to-r from-primary-50 to-accent-50 rounded-2xl border border-primary-100 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
    <div>
      <p class="text-xs font-semibold uppercase tracking-wider text-primary-600 mb-1">Available Balance</p>
      <p class="text-3xl font-black text-slate-900">৳{{ number_format($balance, 2) }}</p>
    </div>

    <form method="POST" action="{{ route('provider.wallet.topup') }}" class="flex items-center gap-2">
      @csrf
      <span class="text-slate-500 font-semibold">৳</span>
      <input type="number" name="amount" min="1" step="0.01" placeholder="Amount" required
        class="w-32 rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
      <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition shadow-soft whitespace-nowrap">
        Add Balance
      </button>
    </form>
  </div>

  {{-- Transaction history --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-900">Transaction History</h3>
    </div>

    @if($transactions->isEmpty())
      <div class="p-12 text-center">
        <p class="text-slate-500 text-sm font-medium">No transactions yet</p>
        <p class="text-slate-400 text-xs mt-1">Top up your wallet to get started</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Date</th>
              <th class="px-5 py-3.5">Description</th>
              <th class="px-5 py-3.5">Type</th>
              <th class="px-5 py-3.5 text-right">Amount</th>
              <th class="px-5 py-3.5 text-right">Balance After</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($transactions as $txn)
            <tr class="hover:bg-slate-50/50 transition">
              <td class="px-5 py-4 text-slate-500 text-xs">{{ $txn->created_at->format('d M Y, h:i A') }}</td>
              <td class="px-5 py-4 text-slate-700">{{ $txn->description ?? ucfirst(str_replace('_', ' ', $txn->type)) }}</td>
              <td class="px-5 py-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                  {{ $txn->amount >= 0 ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                  {{ str_replace('_', ' ', $txn->type) }}
                </span>
              </td>
              <td class="px-5 py-4 text-right font-bold {{ $txn->amount >= 0 ? 'text-green-600' : 'text-slate-900' }}">
                {{ $txn->amount >= 0 ? '+' : '' }}৳{{ number_format($txn->amount, 2) }}
              </td>
              <td class="px-5 py-4 text-right text-slate-500">৳{{ number_format($txn->balance_after, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($transactions->hasPages())
        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100">{{ $transactions->links() }}</div>
      @endif
    @endif
  </div>

</div>
@endsection
