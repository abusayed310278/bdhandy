@extends('layouts.dashboard')

@section('title', auth()->user()->hasRole('admin') ? __('dashboard.admin_title') : __('dashboard.provider_title'))

@section('content')
<div class="space-y-6">
    @role('admin')
    <!-- ===================== ADMIN VIEW ===================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">{{ __('dashboard.overview_heading') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('dashboard.overview_subtext', ['app' => config('app.name')]) }}</p>
        </div>
        <div class="flex gap-2">
            <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                {{ __('dashboard.reports') }}
            </button>
            <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition shadow-soft">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                {{ __('dashboard.system_status') }}
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('dashboard.total_users') }}</p>
                <span class="w-9 h-9 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-slate-900">8,429</p>
            <div class="mt-2 flex items-center gap-2 text-xs">
                <span class="text-green-600 font-bold inline-flex items-center gap-0.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                    14%
                </span>
                <span class="text-slate-400">{{ __('dashboard.vs_last_month') }}</span>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('dashboard.revenue_monthly') }}</p>
                <span class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-slate-900">৳ 1.2M</p>
            <div class="mt-2 flex items-center gap-2 text-xs">
                <span class="text-green-600 font-bold inline-flex items-center gap-0.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                    22%
                </span>
                <span class="text-slate-400">{{ __('dashboard.vs_last_month') }}</span>
            </div>
        </div>

        <!-- Pending Verifications -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('dashboard.pending_verify') }}</p>
                <span class="w-9 h-9 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-slate-900">42</p>
            <div class="mt-2 flex items-center gap-2 text-xs">
                <span class="text-accent-600 font-bold inline-flex items-center gap-0.5 underline">
                    {{ __('dashboard.review_queue') }}
                </span>
                <span class="text-slate-400">{{ __('dashboard.action_required') }}</span>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('dashboard.active_subs') }}</p>
                <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-slate-900">1,204</p>
            <div class="mt-2 flex items-center gap-2 text-xs">
                <span class="text-green-600 font-bold inline-flex items-center gap-0.5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                    5%
                </span>
                <span class="text-slate-400">{{ __('dashboard.churn_rate', ['rate' => '1.2%']) }}</span>
            </div>
        </div>
    </div>

    <!-- Platform Stats & Verification Queue -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Growth Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ __('dashboard.revenue_growth') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('dashboard.revenue_growth_subtext') }}</p>
                </div>
                <select class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <option>{{ __('dashboard.last_30_days') }}</option>
                    <option>{{ __('dashboard.last_90_days') }}</option>
                    <option>{{ __('dashboard.last_year') }}</option>
                </select>
            </div>
            <div class="h-64 w-full bg-slate-50 rounded-xl relative overflow-hidden flex items-end">
                <svg viewBox="0 0 800 200" class="w-full h-full" preserveAspectRatio="none">
                    <path d="M0,180 Q100,160 200,140 T400,100 T600,60 T800,20 L800,200 L0,200 Z" fill="rgba(15, 148, 234, 0.05)" />
                    <path d="M0,180 Q100,160 200,140 T400,100 T600,60 T800,20" fill="none" stroke="#0F94EA" stroke-width="3" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <span class="text-slate-300 text-xs font-medium uppercase tracking-widest italic">{{ __('dashboard.chart_visualization') }}</span>
                </div>
            </div>
        </div>

        <!-- Verification Queue -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-900">{{ __('dashboard.verification_queue') }}</h3>
                <a href="#" class="text-sm font-semibold text-primary-600 hover:text-primary-700">{{ __('dashboard.view_all') }}</a>
            </div>
            <div class="flex-1 space-y-4">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition">
                    <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">AK</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">Abdur Karim</p>
                        <p class="text-xs text-slate-500">Electrician · Dhaka</p>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-accent-100 text-accent-700">{{ __('dashboard.new') }}</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition">
                    <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">SH</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">Service Hub Ltd</p>
                        <p class="text-xs text-slate-500">Cleaning · Sylhet</p>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-accent-100 text-accent-700">{{ __('dashboard.new') }}</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition">
                    <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">NS</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">Nishat Sultana</p>
                        <p class="text-xs text-slate-500">Beauty · Chittagong</p>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-accent-100 text-accent-700">{{ __('dashboard.new') }}</span>
                </div>
            </div>
            <button class="mt-6 w-full py-3 rounded-xl bg-primary-50 text-primary-700 font-bold hover:bg-primary-100 transition text-sm">
                {{ __('dashboard.open_moderation_panel') }}
            </button>
        </div>
    </div>

    <!-- Recent System Activity -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">{{ __('dashboard.recent_activity') }}</h3>
            <div class="flex gap-2">
                <button class="p-2 rounded-lg hover:bg-slate-50 text-slate-400">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-widest font-bold text-slate-500">
                    <tr>
                        <th class="px-6 py-4">{{ __('dashboard.event') }}</th>
                        <th class="px-6 py-4">{{ __('dashboard.category') }}</th>
                        <th class="px-6 py-4">{{ __('dashboard.user') }}</th>
                        <th class="px-6 py-4">{{ __('dashboard.timestamp') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('dashboard.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50 transition cursor-pointer">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">Subscription Upgraded</p>
                            <p class="text-xs text-slate-500">Yearly Plan · ৳ 12,000</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold">REVENUE</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-slate-900">Rahim AC Service</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">2 minutes ago</td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-flex items-center gap-1 text-green-600 text-xs font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Success
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition cursor-pointer">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900">New Support Ticket</p>
                            <p class="text-xs text-slate-500">#TKT-2026-0045 · Urgent</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full bg-red-50 text-red-600 text-[10px] font-bold">SUPPORT</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-slate-900">John Doe (Customer)</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">15 minutes ago</td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-flex items-center gap-1 text-accent-600 text-xs font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent-500"></span> Open
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-center">
            <a href="#" class="text-sm font-bold text-slate-600 hover:text-primary-600 transition">{{ __('dashboard.view_full_audit') }}</a>
        </div>
    </div>
    @else
    <!-- ===================== PROVIDER VIEW (Fallback to dashboard.html design) ===================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900">{{ __('dashboard.welcome_provider', ['name' => auth()->user()->name]) }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('dashboard.provider_subtext') }}</p>
      </div>
      <div class="flex gap-2">
        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          {{ __('dashboard.export') }}
        </button>
        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition shadow-soft">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          {{ __('dashboard.add_service') }}
        </button>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
      <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-slate-500">{{ __('dashboard.active_leads') }}</p>
          <span class="w-7 h-7 rounded-md bg-primary-50 text-primary-600 flex items-center justify-center">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-4V5l-2-2h-4L8 5v2H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
          </span>
        </div>
        <p class="text-2xl sm:text-3xl font-bold text-slate-900">142</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-slate-500">{{ __('dashboard.requests') }}</p>
          <span class="w-7 h-7 rounded-md bg-primary-50 text-primary-600 flex items-center justify-center">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </span>
        </div>
        <p class="text-2xl sm:text-3xl font-bold text-slate-900">38</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-slate-500">{{ __('dashboard.earnings_est') }}</p>
          <span class="w-7 h-7 rounded-md bg-accent-50 text-accent-600 flex items-center justify-center">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </span>
        </div>
        <p class="text-2xl sm:text-3xl font-bold text-slate-900">৳ 24.5K</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-slate-500">{{ __('dashboard.avg_rating') }}</p>
          <span class="w-7 h-7 rounded-md bg-accent-50 text-accent-600 flex items-center justify-center text-accent-500">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </span>
        </div>
        <p class="text-2xl sm:text-3xl font-bold text-slate-900">4.9</p>
      </div>
    </div>
    @endrole
</div>
@endsection
