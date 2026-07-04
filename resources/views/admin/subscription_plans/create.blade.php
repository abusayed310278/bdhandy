@extends('layouts.dashboard')

@section('title', isset($subscriptionPlan) ? 'Edit Subscription Plan' : 'Create Subscription Plan')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($subscriptionPlan) ? 'Edit Plan' : 'Create New Plan' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Define benefits and pricing for this membership tier</p>
        </div>
        <a href="{{ route('admin.subscription_plans.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <form action="{{ isset($subscriptionPlan) ? route('admin.subscription_plans.update', $subscriptionPlan->id) : route('admin.subscription_plans.store') }}" method="POST" class="space-y-6">
        @csrf
        @if(isset($subscriptionPlan)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Basic Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-soft border border-slate-200 p-6 md:p-8">
                    <h4 class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-6">General Information</h4>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Plan Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', isset($subscriptionPlan) ? $subscriptionPlan->name : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. Premium Professional" required>
                            @error('name') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Price <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="price" value="{{ old('price', isset($subscriptionPlan) ? $subscriptionPlan->price : '') }}" class="w-full pl-4 pr-12 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="0.00" required>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                                        CUR
                                    </div>
                                </div>
                                @error('price') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Currency <span class="text-red-500">*</span></label>
                                <select name="currency_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ (old('currency_id', isset($subscriptionPlan) ? $subscriptionPlan->currency_id : '') == $currency->id) ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Duration (Months) <span class="text-red-500">*</span></label>
                                <input type="number" name="duration_months" value="{{ old('duration_months', isset($subscriptionPlan) ? $subscriptionPlan->duration_months : 1) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" required>
                                @error('duration_months') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Discount (%)</label>
                                <input type="number" step="0.1" name="discount_percent" value="{{ old('discount_percent', isset($subscriptionPlan) ? $subscriptionPlan->discount_percent : 0) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-soft border border-slate-200 p-6 md:p-8">
                    <h4 class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-6">Limits & Features</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Lead Limit (0 for unlimted)</label>
                            <input type="number" name="lead_limit" value="{{ old('lead_limit', isset($subscriptionPlan) ? $subscriptionPlan->lead_limit : 0) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Service Area Limit</label>
                            <input type="number" name="service_area_limit" value="{{ old('service_area_limit', isset($subscriptionPlan) ? $subscriptionPlan->service_area_limit : 0) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Gallery Limit</label>
                            <input type="number" name="gallery_limit" value="{{ old('gallery_limit', isset($subscriptionPlan) ? $subscriptionPlan->gallery_limit : 0) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Search Rank Weight</label>
                            <input type="number" name="search_rank_weight" value="{{ old('search_rank_weight', isset($subscriptionPlan) ? $subscriptionPlan->search_rank_weight : 0) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Status & Highlights -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-soft border border-slate-200 p-6">
                    <h4 class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-6">Visibility</h4>
                    
                    <div class="space-y-4">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', isset($subscriptionPlan) ? $subscriptionPlan->is_featured : false) ? 'checked' : '' }} class="w-5 h-5 rounded-lg text-primary-500 border-slate-300 focus:ring-primary-500">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Featured Plan</p>
                                <p class="text-[10px] text-slate-500">Highlight this plan on the frontend</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer">
                            <input type="checkbox" name="is_verified_badge_included" value="1" {{ old('is_verified_badge_included', isset($subscriptionPlan) ? $subscriptionPlan->is_verified_badge_included : false) ? 'checked' : '' }} class="w-5 h-5 rounded-lg text-primary-500 border-slate-300 focus:ring-primary-500">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Verified Badge</p>
                                <p class="text-[10px] text-slate-500">Include a trust badge for providers</p>
                            </div>
                        </label>

                        <hr class="border-slate-50 my-4">

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Plan For</label>
                            <select name="target" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer">
                                <option value="both" {{ (old('target', isset($subscriptionPlan) ? $subscriptionPlan->target : 'both') == 'both') ? 'selected' : '' }}>Provider & Business</option>
                                <option value="provider" {{ (old('target', isset($subscriptionPlan) ? $subscriptionPlan->target : '') == 'provider') ? 'selected' : '' }}>Provider Only</option>
                                <option value="business" {{ (old('target', isset($subscriptionPlan) ? $subscriptionPlan->target : '') == 'business') ? 'selected' : '' }}>Business Only</option>
                            </select>
                        </div>

                        <hr class="border-slate-50 my-4">

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Publish Status</label>
                            <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer">
                                <option value="active" {{ (old('status', isset($subscriptionPlan) ? $subscriptionPlan->status : '') == 'active') ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ (old('status', isset($subscriptionPlan) ? $subscriptionPlan->status : '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    {{ isset($subscriptionPlan) ? 'Save Changes' : 'Create Plan' }}
                </button>
                
                <a href="{{ route('admin.subscription_plans.index') }}" class="w-full py-4 rounded-2xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition flex items-center justify-center">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
