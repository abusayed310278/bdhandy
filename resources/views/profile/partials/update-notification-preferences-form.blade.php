@php
    $prefs = auth()->user()->notificationPreference ?: new \App\Models\NotificationPreference();
@endphp

<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">
            {{ __('Notification Preferences') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ __('Choose how you want to be notified about updates and activities.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-2">
        @csrf
        @method('patch')

        <input type="hidden" name="tab" value="notifications">

        <!-- Email Notifications -->
        <div class="flex items-center justify-between py-4 border-b border-slate-50">
            <div class="flex-1 pe-4">
                <h4 class="text-sm font-bold text-slate-900">Email Notifications</h4>
                <p class="text-xs text-slate-500 mt-0.5">Receive important updates and alerts via email.</p>
            </div>
            <x-toggle-switch 
                name="notification_preferences[email_enabled]" 
                :checked="$prefs->email_enabled" 
            />
        </div>

        <!-- SMS Notifications -->
        <div class="flex items-center justify-between py-4 border-b border-slate-50">
            <div class="flex-1 pe-4">
                <h4 class="text-sm font-bold text-slate-900">SMS Notifications</h4>
                <p class="text-xs text-slate-500 mt-0.5">Get real-time alerts on your phone for urgent tasks.</p>
            </div>
            <x-toggle-switch 
                name="notification_preferences[sms_enabled]" 
                :checked="$prefs->sms_enabled" 
            />
        </div>

        <!-- Push Notifications -->
        <div class="flex items-center justify-between py-4 border-b border-slate-50">
            <div class="flex-1 pe-4">
                <h4 class="text-sm font-bold text-slate-900">Push Notifications</h4>
                <p class="text-xs text-slate-500 mt-0.5">Stay updated with browser or app-level notifications.</p>
            </div>
            <x-toggle-switch 
                name="notification_preferences[push_enabled]" 
                :checked="$prefs->push_enabled" 
            />
        </div>

        <!-- WhatsApp Notifications -->
        <div class="flex items-center justify-between py-4 border-b border-slate-50">
            <div class="flex-1 pe-4">
                <h4 class="text-sm font-bold text-slate-900">WhatsApp Notifications</h4>
                <p class="text-xs text-slate-500 mt-0.5">Connect with us on WhatsApp for faster support.</p>
            </div>
            <x-toggle-switch 
                name="notification_preferences[whatsapp_enabled]" 
                :checked="$prefs->whatsapp_enabled" 
            />
        </div>

        <!-- Marketing -->
        <div class="flex items-center justify-between py-6 mt-2">
            <div class="flex-1 pe-4">
                <h4 class="text-sm font-bold text-primary-600">Marketing & Promotions</h4>
                <p class="text-xs text-slate-500 mt-0.5">Receive news about special offers and new features.</p>
            </div>
            <x-toggle-switch 
                name="notification_preferences[marketing_enabled]" 
                :checked="$prefs->marketing_enabled" 
            />
        </div>

        <div class="flex items-center gap-4 pt-6">
            <x-primary-button>{{ __('Save Preferences') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-500"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
