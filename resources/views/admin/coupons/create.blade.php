@extends('layouts.dashboard')

@section('title', isset($coupon) ? 'Edit Coupon' : 'Create Coupon')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($coupon) ? 'Edit Coupon' : 'Create New Coupon' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Setup discount codes for subscription plans or services</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <form action="{{ isset($coupon) ? route('admin.coupons.update', $coupon->id) : route('admin.coupons.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf
            @if(isset($coupon)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Coupon Code <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="code" id="couponCode" value="{{ old('code', isset($coupon) ? $coupon->code : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition font-black tracking-widest uppercase" placeholder="SUMMER2024" required>
                        <button type="button" onclick="generateCode()" class="absolute inset-y-0 right-0 px-4 text-primary-600 hover:text-primary-700 font-bold text-xs uppercase tracking-wider">
                            Generate
                        </button>
                    </div>
                    @error('code') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Internal Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', isset($coupon) ? $coupon->title : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. Summer Promo 20%" required>
                    @error('title') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Discount Type <span class="text-red-500">*</span></label>
                    <select name="discount_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                        <option value="percentage" {{ old('discount_type', isset($coupon) ? $coupon->discount_type : '') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type', isset($coupon) ? $coupon->discount_type : '') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Discount Value <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', isset($coupon) ? $coupon->discount_value : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="0.00" required>
                    @error('discount_value') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Usage Limit</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', isset($coupon) ? $coupon->usage_limit : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="Unlimited">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', isset($coupon->start_date) ? $coupon->start_date->format('Y-m-d') : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', isset($coupon->end_date) ? $coupon->end_date->format('Y-m-d') : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                    <option value="active" {{ old('status', isset($coupon) ? $coupon->status : '') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', isset($coupon) ? $coupon->status : '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="expired" {{ old('status', isset($coupon) ? $coupon->status : '') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>

            <!-- Footer Actions -->
            <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.coupons.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    {{ isset($coupon) ? 'Save Changes' : 'Create Coupon' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function generateCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('couponCode').value = code;
    }
</script>
@endsection
