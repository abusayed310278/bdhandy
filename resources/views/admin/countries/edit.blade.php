@extends('layouts.dashboard')

@section('title', isset($country) ? 'Edit Country' : 'Add New Country')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($country) ? 'Edit Country' : 'Create New Country' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Configure regional and localization settings for the platform</p>
        </div>
        <a href="{{ route('admin.countries.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <form action="{{ isset($country) ? route('admin.countries.update', $country->id) : route('admin.countries.store') }}" method="POST" class="p-6 md:p-8 space-y-8">
            @csrf
            @if(isset($country)) @method('PUT') @endif

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h4 class="text-xs font-bold text-primary-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                        Basic Information
                    </h4>
                </div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Country Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $country->name ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. Bangladesh" required>
                    @error('name') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">ISO Code <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                        </div>
                        <input type="text" name="iso_code" value="{{ old('iso_code', $country->iso_code ?? '') }}" class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition uppercase" placeholder="BD" maxlength="2" required>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-wide">2-letter country code (e.g. US, BD, AE)</p>
                    @error('iso_code') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Localization & Currency -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-8 border-t border-slate-100">
                <div class="md:col-span-2 lg:col-span-3">
                    <h4 class="text-xs font-bold text-primary-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                        Localization & Currency
                    </h4>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Phone Code <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-semibold text-sm">+</div>
                        <input type="text" name="phone_code" value="{{ old('phone_code', $country->phone_code ?? '') }}" class="w-full pl-8 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="880" required>
                    </div>
                    @error('phone_code') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Currency Code <span class="text-red-500">*</span></label>
                    <input type="text" name="currency_code" value="{{ old('currency_code', $country->currency_code ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition uppercase" placeholder="BDT" maxlength="3" required>
                    @error('currency_code') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Currency Symbol <span class="text-red-500">*</span></label>
                    <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $country->currency_symbol ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="৳" required>
                    @error('currency_symbol') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Default Locale <span class="text-red-500">*</span></label>
                    <input type="text" name="locale" value="{{ old('locale', $country->locale ?? 'en') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="en" required>
                    @error('locale') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Text Direction <span class="text-red-500">*</span></label>
                    <select name="direction" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer">
                        <option value="ltr" {{ old('direction', $country->direction ?? '') == 'ltr' ? 'selected' : '' }}>LTR (Left to Right)</option>
                        <option value="rtl" {{ old('direction', $country->direction ?? '') == 'rtl' ? 'selected' : '' }}>RTL (Right to Left)</option>
                    </select>
                    @error('direction') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer">
                        <option value="active" {{ old('status', $country->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $country->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.countries.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    {{ isset($country) ? 'Save Changes' : 'Create Country' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
