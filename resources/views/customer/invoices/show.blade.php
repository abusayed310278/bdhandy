@extends('layouts.dashboard')
@section('title', 'Invoice — ' . $invoice->invoice_number)

@push('styles')
<style>
@media print {
  .no-print, nav, aside, header, footer, [class*="sidebar"], [class*="navbar"] { display: none !important; }
  body, html { background: white !important; }
  .max-w-3xl { max-width: 100% !important; }
  .bg-white { box-shadow: none !important; }
  a[href]::after { content: none !important; }
}
</style>
@endpush

@section('content')
@php
  $sym = $invoice->currency?->symbol ?? '';
  $statusColors = ['draft'=>'slate','pending'=>'yellow','due'=>'orange','partial'=>'blue','paid'=>'green'];
  $sc = $statusColors[$invoice->payment_status] ?? 'slate';
  $methodLabels = ['cash'=>'Cash','card'=>'Card','online'=>'Online / Mobile Banking','cheque'=>'Cheque','bank_transfer'=>'Bank Transfer','other'=>'Other'];
@endphp

<div class="max-w-3xl space-y-5 text-sm">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
    <div>
      <a href="{{ route('customer.requests.show', $invoice->serviceRequest) }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1.5">
        <svg class="w-3.5 h-3.5 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        Back to Request
      </a>
      <h2 class="text-xl font-bold text-slate-900">{{ $invoice->invoice_number }}</h2>
      <p class="text-xs text-slate-400 mt-0.5">
        Issued {{ $invoice->issued_at?->format('d M Y') ?? $invoice->created_at->format('d M Y') }}
        @if($invoice->due_date) · Due {{ $invoice->due_date->format('d M Y') }} @endif
      </p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
      <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold uppercase bg-{{ $sc }}-50 text-{{ $sc }}-700 ring-1 ring-{{ $sc }}-200">
        {{ ucfirst($invoice->payment_status) }}
      </span>
      <button onclick="window.print()"
              class="no-print inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print
      </button>
    </div>
  </div>

  {{-- Invoice Card --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">

    {{-- Provider → Customer --}}
    <div class="grid sm:grid-cols-2 gap-6 p-6 border-b border-slate-100">
      <div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">From</p>
        <p class="font-bold text-slate-900">{{ $invoice->provider?->providerProfile?->business_name ?? $invoice->provider?->name }}</p>
        <p class="text-xs text-slate-500 mt-0.5">{{ $invoice->provider?->email }}</p>
      </div>
      <div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Bill To</p>
        <p class="font-bold text-slate-900">{{ $invoice->customer?->name }}</p>
        <p class="text-xs text-slate-500 mt-0.5">{{ $invoice->customer?->email }}</p>
      </div>
    </div>

    {{-- Request ref --}}
    <div class="px-6 py-3 bg-slate-50 border-b border-slate-100 flex flex-wrap gap-4 text-xs">
      <div>
        <span class="text-slate-400 font-medium">Service Request:</span>
        <span class="ms-1.5 font-bold text-slate-700">{{ $invoice->serviceRequest?->request_number }}</span>
      </div>
      <div>
        <span class="text-slate-400 font-medium">Service:</span>
        <span class="ms-1.5 font-semibold text-slate-700">{{ $invoice->serviceRequest?->service?->getTranslation('translations','en')['name'] ?? '—' }}</span>
      </div>
      @if($invoice->serviceRequest?->preferred_date)
      <div>
        <span class="text-slate-400 font-medium">Service Date:</span>
        <span class="ms-1.5 font-semibold text-slate-700">{{ $invoice->serviceRequest->preferred_date->format('d M Y') }}</span>
      </div>
      @endif
    </div>

    {{-- Pricing breakdown --}}
    <div class="p-6 space-y-3">
      <div class="flex items-center justify-between py-2 border-b border-slate-100">
        <span class="text-slate-600">{{ $invoice->serviceRequest?->title ?? 'Service' }}</span>
        <span class="font-semibold text-slate-900">{{ $sym }} {{ number_format($invoice->subtotal, 2) }}</span>
      </div>

      @if($invoice->discount_type !== 'none' && $invoice->discount_amount > 0)
      <div class="flex items-center justify-between text-green-700">
        <span>Discount{{ $invoice->discount_type === 'percent' ? ' (' . number_format($invoice->discount_value, 0) . '%)' : '' }}</span>
        <span class="font-semibold">− {{ $sym }} {{ number_format($invoice->discount_amount, 2) }}</span>
      </div>
      @endif

      @if($invoice->tax_rate > 0)
      <div class="flex items-center justify-between text-slate-600">
        <span>{{ $invoice->tax_label ?: 'Tax' }} ({{ number_format($invoice->tax_rate, 2) }}%)</span>
        <span class="font-semibold">+ {{ $sym }} {{ number_format($invoice->tax_amount, 2) }}</span>
      </div>
      @endif

      @if($invoice->adjustment_amount != 0)
      <div class="flex items-center justify-between text-slate-600">
        <span>Adjustment{{ $invoice->adjustment_note ? ' — ' . $invoice->adjustment_note : '' }}</span>
        <span class="font-semibold">{{ $invoice->adjustment_amount > 0 ? '+ ' : '− ' }}{{ $sym }} {{ number_format(abs($invoice->adjustment_amount), 2) }}</span>
      </div>
      @endif

      <div class="flex items-center justify-between pt-3 border-t-2 border-slate-200">
        <span class="font-black text-slate-900 text-base uppercase tracking-wide">Total</span>
        <span class="font-black text-slate-900 text-xl">{{ $sym }} {{ number_format($invoice->total, 2) }}</span>
      </div>
    </div>

    {{-- Payment info --}}
    @if($invoice->payment_method || $invoice->payment_reference || $invoice->paid_at)
    <div class="px-6 py-4 bg-green-50/60 border-t border-green-100 flex flex-wrap gap-5 text-xs">
      @if($invoice->payment_method)
      <div>
        <span class="text-slate-500 font-medium">Method:</span>
        <span class="ms-1.5 font-bold text-slate-800">{{ $methodLabels[$invoice->payment_method] ?? ucfirst($invoice->payment_method) }}</span>
      </div>
      @endif
      @if($invoice->payment_reference)
      <div>
        <span class="text-slate-500 font-medium">Reference:</span>
        <span class="ms-1.5 font-bold text-slate-800">{{ $invoice->payment_reference }}</span>
      </div>
      @endif
      @if($invoice->paid_at)
      <div>
        <span class="text-slate-500 font-medium">Paid at:</span>
        <span class="ms-1.5 font-bold text-slate-800">{{ $invoice->paid_at->format('d M Y, h:i A') }}</span>
      </div>
      @endif
    </div>
    @endif

    {{-- Due notice --}}
    @if(in_array($invoice->payment_status, ['pending','due','partial']) && $invoice->due_date)
    <div class="px-6 py-3 border-t border-amber-100 bg-amber-50 flex items-center gap-2 text-xs text-amber-700 font-medium">
      <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      Payment due by {{ $invoice->due_date->format('d M Y') }}
      @if($invoice->due_date->isPast())
        — <span class="font-bold text-red-600">Overdue</span>
      @endif
    </div>
    @endif

    {{-- Notes --}}
    @if($invoice->notes)
    <div class="px-6 py-4 border-t border-slate-100">
      <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Notes</p>
      <p class="text-slate-600 leading-relaxed text-xs whitespace-pre-line">{{ $invoice->notes }}</p>
    </div>
    @endif
  </div>

</div>
@endsection
