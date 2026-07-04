@extends('layouts.dashboard')
@section('title', 'Lead — ' . $requirement->title)

@section('content')
<div class="max-w-3xl space-y-5 text-sm">

  <div>
    <a href="{{ route('provider.leads.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1">
      <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      Leads
    </a>
    <h2 class="text-xl font-bold text-slate-900">{{ $requirement->title }}</h2>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
      <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  {{-- Requirement Details --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
    @php
      $urgColors = ['emergency'=>'red','urgent'=>'yellow','normal'=>'slate'];
      $uc = $urgColors[$requirement->urgency] ?? 'slate';
    @endphp
    <div class="flex items-start gap-3">
      <div class="flex-1">
        <div class="flex flex-wrap gap-2 mb-2">
          <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-{{ $uc }}-50 text-{{ $uc }}-700">{{ $requirement->urgency }}</span>
          @if($requirement->category)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-primary-50 text-primary-700">{{ $requirement->category->getTranslation('translations','en') ?: $requirement->category->slug }}</span>
          @endif
          @if($requirement->service)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ ($requirement->service->getTranslation('translations','en')['name'] ?? null) ?: $requirement->service->slug }}</span>
          @endif
        </div>
        <p class="text-slate-700 leading-relaxed">{{ $requirement->description }}</p>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
      {{-- Budget --}}
      <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
        <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">Budget</p>
        @if($requirement->budget_type === 'fixed' && $requirement->budget_fixed)
          <p class="font-bold text-slate-900">{{ $requirement->currency?->symbol }}{{ number_format($requirement->budget_fixed, 0) }}</p>
        @elseif($requirement->budget_type === 'range')
          <p class="font-bold text-slate-900">{{ $requirement->currency?->symbol }}{{ number_format($requirement->budget_min, 0) }} – {{ $requirement->currency?->symbol }}{{ number_format($requirement->budget_max, 0) }}</p>
        @else
          <p class="text-slate-500 italic">Flexible</p>
        @endif
      </div>

      {{-- Date --}}
      <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
        <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">Needed By</p>
        <p class="font-semibold text-slate-900">{{ $requirement->preferred_date ? $requirement->preferred_date->format('d F Y') : 'Flexible' }}</p>
      </div>

      {{-- Location --}}
      @if($requirement->address)
      <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 sm:col-span-2">
        <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">Location</p>
        <p class="text-slate-700">{{ $requirement->address }}</p>
      </div>
      @endif
    </div>

    <div class="flex items-center gap-4 text-[11px] text-slate-400 pt-1 border-t border-slate-100">
      <span>Posted {{ $requirement->created_at->diffForHumans() }}</span>
      <span>{{ $proposalsCount }} proposal{{ $proposalsCount !== 1 ? 's' : '' }} received</span>
      @if($requirement->expiry_at)
        <span class="text-red-400">Expires {{ $requirement->expiry_at->diffForHumans() }}</span>
      @endif
    </div>
  </div>

  {{-- Proposal form / existing proposal --}}
  @if($myProposal)
    <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
      <div class="flex items-center gap-2 mb-3">
        <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <p class="font-semibold text-green-800">You submitted a proposal</p>
        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
          {{ $myProposal->status === 'accepted' ? 'bg-green-100 text-green-700' : ($myProposal->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
          {{ $myProposal->status }}
        </span>
      </div>
      <p class="text-xs text-green-700 leading-relaxed">{{ $myProposal->message }}</p>
      @if($myProposal->proposed_price)
        <p class="mt-2 text-xs font-semibold text-green-800">Proposed: {{ $myProposal->currency?->symbol }}{{ number_format($myProposal->proposed_price, 0) }}</p>
      @endif
      @if($myProposal->estimated_arrival_time)
        <p class="mt-1 text-xs text-green-700">Arrival: {{ $myProposal->estimated_arrival_time }}</p>
      @endif
    </div>
  @else
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
      <h3 class="font-semibold text-slate-900 mb-4">Submit Your Proposal</h3>
      <form action="{{ route('provider.leads.propose', $requirement) }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Your Message <span class="text-red-500">*</span></label>
          <textarea name="message" rows="5" required
            placeholder="Introduce yourself and explain how you can help with this requirement..."
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition resize-none">{{ old('message') }}</textarea>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Your Price</label>
            <input type="number" name="proposed_price" value="{{ old('proposed_price') }}" min="0" step="0.01"
              placeholder="0.00"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Currency</label>
            <select name="currency_id"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
              <option value="">Select</option>
              @foreach($currencies as $cur)
                <option value="{{ $cur->id }}" {{ old('currency_id', $requirement->currency_id) == $cur->id ? 'selected' : '' }}>{{ $cur->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Estimated Arrival</label>
            <input type="text" name="estimated_arrival_time" value="{{ old('estimated_arrival_time') }}"
              placeholder="e.g. Within 2 hours"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
            Submit Proposal
          </button>
        </div>
      </form>
    </div>
  @endif
</div>
@endsection
