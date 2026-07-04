@extends('layouts.dashboard')

@section('title', 'System Settings')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">General Settings</h3>
            <p class="text-sm text-slate-500">Configure global platform behavior</p>
        </div>
        
        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-5 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Site Name</label>
                    <input type="text" name="site_name" value="{{ \App\Models\Setting::get('site_name', 'ServiceHub BD') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Support Email</label>
                    <input type="email" name="support_email" value="{{ \App\Models\Setting::get('support_email', 'support@servicehub.com') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Support Phone</label>
                    <input type="text" name="support_phone" value="{{ \App\Models\Setting::get('support_phone', '+880 1234 567890') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Default Currency</label>
                    <input type="text" name="default_currency" value="{{ \App\Models\Setting::get('default_currency', 'BDT') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Site Description</label>
                    <textarea name="site_description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">{{ \App\Models\Setting::get('site_description') }}</textarea>
                </div>
            </div>

            <div class="pt-6 flex items-center gap-3 border-t border-slate-50">
                <button type="submit" class="px-6 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
