@extends('layouts.dashboard')

@section('title', 'Account Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ tab: '{{ request('tab', 'general') }}', photoPreview: null }">
    <!-- Header Summary Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-soft p-6 flex flex-col md:flex-row items-center gap-6">
        <div class="relative group">
            <div class="w-24 h-24 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 text-3xl font-black uppercase ring-4 ring-primary-50 overflow-hidden border-2 border-white shadow-md">
                <template x-if="photoPreview">
                    <img :src="photoPreview" class="w-full h-full object-cover">
                </template>
                <template x-if="!photoPreview">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-full h-full object-cover">
                    @else
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </template>
            </div>
            <button @click="$dispatch('open-photo-upload')" class="absolute bottom-0 end-0 p-1.5 bg-white rounded-full border border-slate-200 shadow-sm text-slate-400 hover:text-primary-600 transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </button>
        </div>
        <div class="text-center md:text-start flex-1">
            <div x-show="photoPreview" x-transition class="mb-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-accent-50 text-accent-700 text-[10px] font-bold uppercase tracking-wider animate-pulse">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
                    Click "Save Profile" below to save your new photo
                </span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">{{ auth()->user()->name }}</h2>
            <p class="text-slate-500 text-sm mt-1">{{ auth()->user()->email }}</p>
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-3">
                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider">
                    Member since {{ auth()->user()->created_at->format('M Y') }}
                </span>
                @foreach(auth()->user()->getRoleNames() as $role)
                <span class="px-2.5 py-0.5 rounded-full bg-primary-50 text-primary-700 text-[10px] font-bold uppercase tracking-wider">
                    {{ $role }}
                </span>
                @endforeach
            </div>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-red-200 text-red-600 text-sm font-bold hover:bg-red-50 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-8 border-b border-slate-200 px-2 overflow-x-auto no-scrollbar">
        <button @click="tab = 'general'" :class="tab === 'general' ? 'text-primary-600 border-primary-500' : 'text-slate-400 border-transparent hover:text-slate-600'" class="pb-4 text-sm font-bold border-b-2 transition whitespace-nowrap">General Profile</button>
        <button @click="tab = 'security'" :class="tab === 'security' ? 'text-primary-600 border-primary-500' : 'text-slate-400 border-transparent hover:text-slate-600'" class="pb-4 text-sm font-bold border-b-2 transition whitespace-nowrap">Security</button>
        <button @click="tab = 'notifications'" :class="tab === 'notifications' ? 'text-primary-600 border-primary-500' : 'text-slate-400 border-transparent hover:text-slate-600'" class="pb-4 text-sm font-bold border-b-2 transition whitespace-nowrap">Notifications</button>
    </div>

    <!-- Forms Sections -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Tab: General -->
        <div x-show="tab === 'general'" x-transition class="bg-white rounded-2xl border border-slate-200 shadow-soft overflow-hidden">
            <div class="p-6 md:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Tab: Security -->
        <div x-show="tab === 'security'" x-transition style="display:none" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-soft overflow-hidden">
                <div class="p-6 md:p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            
            <div class="bg-red-50/30 rounded-2xl border border-red-100 overflow-hidden">
                <div class="p-6 md:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

        <!-- Tab: Notifications -->
        <div x-show="tab === 'notifications'" x-transition style="display:none" class="bg-white rounded-2xl border border-slate-200 shadow-soft overflow-hidden">
            <div class="p-6 md:p-8">
                @include('profile.partials.update-notification-preferences-form')
            </div>
        </div>
    </div>
</div>
@endsection
