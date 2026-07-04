@extends('layouts.dashboard')
@section('title', 'New Support Ticket')

@section('content')
<div class="max-w-2xl space-y-5 text-sm">
  <div>
    <a href="{{ route('customer.tickets.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1">
      <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      Support Tickets
    </a>
    <h2 class="text-xl font-bold text-slate-900">Open a Support Ticket</h2>
    <p class="text-slate-500 text-xs mt-0.5">Our support team typically responds within 24 hours</p>
  </div>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
      <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('customer.tickets.store') }}" method="POST" class="space-y-5">
    @csrf

    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
      <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Subject <span class="text-red-500">*</span></label>
        <input type="text" name="subject" value="{{ old('subject') }}" required
          placeholder="Brief description of your issue"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Department <span class="text-red-500">*</span></label>
          <select name="department" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            @foreach(['technical'=>'Technical','billing'=>'Billing','verification'=>'Verification','general'=>'General'] as $d=>$dl)
              <option value="{{ $d }}" {{ old('department') === $d ? 'selected' : '' }}>{{ $dl }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Priority <span class="text-red-500">*</span></label>
          <select name="priority" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $p=>$pl)
              <option value="{{ $p }}" {{ old('priority', 'medium') === $p ? 'selected' : '' }}>{{ $pl }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
        <textarea name="description" rows="6" required
          placeholder="Describe your issue in detail. Include any relevant request numbers, dates, or screenshots if possible."
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition resize-none">{{ old('description') }}</textarea>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pb-4">
      <a href="{{ route('customer.tickets.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Cancel</a>
      <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
        Submit Ticket
      </button>
    </div>
  </form>
</div>
@endsection
