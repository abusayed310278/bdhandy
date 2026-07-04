@extends('layouts.dashboard')
@section('title', 'Create Invoice — ' . $serviceRequest->request_number)

@section('content')
<div class="max-w-2xl space-y-6 text-sm"
     x-data="{
       subtotal:      {{ (float)($serviceRequest->final_price ?? $serviceRequest->estimated_price ?? 0) }},
       discountType:  'none',
       discountValue: 0,
       taxRate:       0,
       adjustment:    0,
       discountAmount: 0,
       taxAmount:     0,
       total:         {{ (float)($serviceRequest->final_price ?? $serviceRequest->estimated_price ?? 0) }},
       recalc() {
         let da = 0;
         if (this.discountType === 'fixed')   da = Math.min(parseFloat(this.discountValue)||0, this.subtotal);
         if (this.discountType === 'percent') da = Math.round(this.subtotal * ((parseFloat(this.discountValue)||0)/100) * 100)/100;
         this.discountAmount = da;
         const afterDiscount = this.subtotal - da;
         this.taxAmount = Math.round(afterDiscount * ((parseFloat(this.taxRate)||0)/100) * 100)/100;
         this.total = Math.max(0, Math.round((afterDiscount + this.taxAmount + (parseFloat(this.adjustment)||0)) * 100)/100);
       }
     }"
     x-init="recalc()">

  <div>
    <a href="{{ route('provider.requests.show', $serviceRequest) }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1.5">
      <svg class="w-3.5 h-3.5 rtl-flip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      Back to Request
    </a>
    <h2 class="text-xl font-bold text-slate-900">Create Invoice</h2>
    <p class="text-xs text-slate-400 mt-0.5">{{ $serviceRequest->request_number }} · {{ $serviceRequest->customer?->name }}</p>
  </div>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
      <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('provider.invoices.store', $serviceRequest) }}" method="POST" class="space-y-5">
    @csrf

    {{-- Currency --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
      <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
        <span class="w-7 h-7 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        </span>
        Pricing
      </h3>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Currency <span class="text-red-500">*</span></label>
          <select name="currency_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-white focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
            @foreach($currencies as $cur)
              <option value="{{ $cur->id }}" {{ $serviceRequest->currency_id == $cur->id ? 'selected' : '' }}>
                {{ $cur->symbol }} {{ $cur->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Subtotal <span class="text-red-500">*</span></label>
          <input type="number" name="subtotal" step="0.01" min="0" required
                 x-model.number="subtotal" @input="recalc()"
                 class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        </div>
      </div>

      {{-- Discount --}}
      <div class="space-y-3">
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Discount</label>
        <div class="flex gap-2">
          @foreach(['none' => 'No Discount', 'fixed' => 'Fixed Amount', 'percent' => 'Percentage'] as $val => $label)
            <label class="flex-1 cursor-pointer">
              <input type="radio" name="discount_type" value="{{ $val }}" x-model="discountType" @change="recalc()" class="sr-only">
              <div class="text-center py-2 px-2 rounded-xl border-2 text-xs font-semibold transition"
                   :class="discountType === '{{ $val }}' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">
                {{ $label }}
              </div>
            </label>
          @endforeach
        </div>
        <div x-show="discountType !== 'none'" x-transition x-cloak>
          <div class="relative">
            <input type="number" name="discount_value" step="0.01" min="0"
                   x-model.number="discountValue" @input="recalc()"
                   placeholder="0"
                   class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 pe-10">
            <span class="absolute inset-y-0 end-0 pe-3 flex items-center text-slate-400 text-xs font-bold" x-text="discountType === 'percent' ? '%' : '#'"></span>
          </div>
          <p class="mt-1.5 text-xs text-slate-400" x-show="discountAmount > 0">
            Discount: <span class="font-bold text-slate-600" x-text="discountAmount.toFixed(2)"></span>
          </p>
        </div>
      </div>

      {{-- Tax --}}
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tax Label <span class="text-xs font-normal">(optional)</span></label>
          <input type="text" name="tax_label" value="{{ old('tax_label') }}" placeholder="e.g. VAT, GST"
                 class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tax Rate %</label>
          <div class="relative">
            <input type="number" name="tax_rate" step="0.01" min="0" max="100" value="{{ old('tax_rate') }}"
                   x-model.number="taxRate" @input="recalc()"
                   placeholder="0"
                   class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 pe-8">
            <span class="absolute inset-y-0 end-0 pe-3 flex items-center text-slate-400 text-xs font-bold">%</span>
          </div>
          <p class="mt-1.5 text-xs text-slate-400" x-show="taxAmount > 0">
            Tax: <span class="font-bold text-slate-600" x-text="taxAmount.toFixed(2)"></span>
          </p>
        </div>
      </div>

      {{-- Adjustment --}}
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Adjustment <span class="text-xs font-normal">(+ or −)</span></label>
          <input type="number" name="adjustment_amount" step="0.01" value="{{ old('adjustment_amount') }}"
                 x-model.number="adjustment" @input="recalc()"
                 placeholder="0"
                 class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Adjustment Note</label>
          <input type="text" name="adjustment_note" value="{{ old('adjustment_note') }}" placeholder="Reason for adjustment"
                 class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        </div>
      </div>

      {{-- Total --}}
      <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 flex items-center justify-between">
        <span class="text-sm font-bold text-slate-700 uppercase tracking-wider">Total Due</span>
        <span class="text-2xl font-black text-slate-900" x-text="total.toFixed(2)"></span>
      </div>
    </div>

    {{-- Payment --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
      <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
        <span class="w-7 h-7 rounded-lg bg-green-50 text-green-600 flex items-center justify-center shrink-0">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </span>
        Payment Details
      </h3>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Payment Status <span class="text-red-500">*</span></label>
          <select name="payment_status" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-white focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
            @foreach(['draft' => 'Draft', 'pending' => 'Pending', 'due' => 'Due', 'partial' => 'Partial', 'paid' => 'Paid'] as $val => $label)
              <option value="{{ $val }}" {{ old('payment_status', 'pending') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Payment Method</label>
          <select name="payment_method" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm bg-white focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
            <option value="">— Select —</option>
            @foreach(['cash' => 'Cash', 'card' => 'Card', 'online' => 'Online / Mobile Banking', 'cheque' => 'Cheque', 'bank_transfer' => 'Bank Transfer', 'other' => 'Other'] as $val => $label)
              <option value="{{ $val }}" {{ old('payment_method') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Reference / Card No / Cheque No</label>
          <input type="text" name="payment_reference" value="{{ old('payment_reference') }}"
                 placeholder="Optional reference number"
                 class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Due Date</label>
          <input type="date" name="due_date" value="{{ old('due_date') }}"
                 class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Notes</label>
        <textarea name="notes" rows="3" placeholder="Additional notes for the customer…"
                  class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 resize-none">{{ old('notes') }}</textarea>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pb-6">
      <a href="{{ route('provider.requests.show', $serviceRequest) }}"
         class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Cancel</a>
      <button type="submit"
              class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold shadow-sm transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Create Invoice
      </button>
    </div>
  </form>
</div>
@endsection
