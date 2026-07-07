@extends('layouts.dashboard')

@section('title', isset($currency) ? 'Edit Currency' : 'Add New Currency')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($currency) ? 'Edit Currency' : 'Create New Currency' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Configure global currency settings for the platform</p>
        </div>
        <a href="{{ route('admin.currencies.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <form action="{{ isset($currency) ? route('admin.currencies.update', $currency->id) : route('admin.currencies.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf
            @if(isset($currency)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Currency Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $currency->name ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. US Dollar" required>
                @error('name') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Symbol <span class="text-red-500">*</span></label>
                    <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. $" maxlength="10" required>
                    @error('symbol') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                        <option value="active" {{ old('status', $currency->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $currency->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Affiliate Commission Cap</label>
                <input type="number" step="0.01" min="0" name="affiliate_commission_cap" value="{{ old('affiliate_commission_cap', $currency->affiliate_commission_cap ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. 500 (leave blank for no cap)">
                <p class="text-xs text-slate-400 mt-1.5">Maximum affiliate commission paid on a referral's first transaction, in this currency. Leave blank for uncapped.</p>
                @error('affiliate_commission_cap') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Footer Actions -->
            <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.currencies.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    {{ isset($currency) ? 'Save Changes' : 'Create Currency' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
