@extends('layouts.dashboard')

@section('title', 'Subscription Plans')

@section('content')
<div class="space-y-6 text-sm">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Subscription Plans</h3>
            <p class="text-sm text-slate-500 mt-1">Configure monetization options for service providers</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.subscription_plans.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition shadow-soft">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Create Plan
            </a>
        </div>
    </div>

    <!-- Stats/Cards View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            <div class="relative bg-white rounded-3xl border {{ $plan->is_featured ? 'border-primary-500 ring-4 ring-primary-50' : 'border-slate-200' }} shadow-soft overflow-hidden group hover:translate-y-[-4px] transition-all duration-300">
                @if($plan->is_featured)
                    <div class="absolute top-0 right-0">
                        <div class="bg-primary-500 text-white text-[10px] font-bold px-4 py-1 rounded-bl-2xl uppercase tracking-widest shadow-sm">
                            Featured
                        </div>
                    </div>
                @endif

                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-600 group-hover:bg-primary-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900">{{ $plan->name }}</h4>
                            <p class="text-xs text-slate-500">{{ $plan->duration_months }} Months Duration</p>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-3xl font-black text-slate-900">{{ number_format($plan->price, 2) }}</span>
                        <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">{{ $plan->currency->symbol }}</span>
                        @if($plan->discount_percent > 0)
                            <span class="ms-2 px-2 py-0.5 rounded-lg bg-green-100 text-green-700 text-[10px] font-bold">-{{ (int)$plan->discount_percent }}%</span>
                        @endif
                    </div>

                    <div class="space-y-3 mb-8">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Lead Limit</span>
                            <span class="font-bold text-slate-900">{{ $plan->lead_limit ?: 'Unlimited' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Service Areas</span>
                            <span class="font-bold text-slate-900">{{ $plan->service_area_limit ?: 'Unlimited' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Gallery Items</span>
                            <span class="font-bold text-slate-900">{{ $plan->gallery_limit ?: 'Unlimited' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-50">
                            <span class="text-slate-500">Verified Badge</span>
                            @if($plan->is_verified_badge_included)
                                <svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1.707-7.707l6.364-6.364-1.414-1.414-4.95 4.95-2.12-2.122-1.415 1.415 3.535 3.535z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            @if($plan->status == 'active')
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                <span class="text-[10px] font-bold text-green-600 uppercase">Active</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Disabled</span>
                            @endif
                            <span class="ms-1 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase
                                {{ $plan->target === 'provider' ? 'bg-blue-100 text-blue-700' : ($plan->target === 'business' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-500') }}">
                                {{ $plan->target === 'both' ? 'All' : ucfirst($plan->target) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.subscription_plans.edit', $plan->id) }}" class="p-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-primary-50 hover:text-primary-600 transition shadow-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form action="{{ route('admin.subscription_plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Delete this plan?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-red-50 hover:text-red-600 transition shadow-sm">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-slate-200 border-dashed">
                <p class="text-slate-500 italic">No subscription plans found. Create your first plan to get started.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination Section -->
    @if($plans->hasPages())
        <div class="mt-8">
            {{ $plans->links() }}
        </div>
    @endif
</div>
@endsection
