<x-guest-layout>
<div x-data="{
    role: '',
    phoneSearch: '',
    countryCodeOpen: false,
    selectedCode: '+880',
    selectedFlag: '🇧🇩',
    countryCodes: [
        { code: '+880', flag: '🇧🇩', name: 'Bangladesh' },
        { code: '+971', flag: '🇦🇪', name: 'UAE' },
        { code: '+966', flag: '🇸🇦', name: 'Saudi Arabia' },
        { code: '+974', flag: '🇶🇦', name: 'Qatar' },
        { code: '+965', flag: '🇰🇼', name: 'Kuwait' },
        { code: '+973', flag: '🇧🇭', name: 'Bahrain' },
        { code: '+968', flag: '🇴🇲', name: 'Oman' },
        { code: '+91',  flag: '🇮🇳', name: 'India' },
        { code: '+1',   flag: '🇺🇸', name: 'USA / Canada' },
        { code: '+44',  flag: '🇬🇧', name: 'UK' },
        { code: '+60',  flag: '🇲🇾', name: 'Malaysia' },
        { code: '+65',  flag: '🇸🇬', name: 'Singapore' },
        { code: '+61',  flag: '🇦🇺', name: 'Australia' },
        { code: '+998', flag: '🇺🇿', name: 'Uzbekistan' },
    ],
    get filteredCodes() {
        if (!this.phoneSearch) return this.countryCodes;
        const q = this.phoneSearch.toLowerCase();
        return this.countryCodes.filter(c => c.name.toLowerCase().includes(q) || c.code.includes(q));
    },
    selectCode(code, flag) { this.selectedCode = code; this.selectedFlag = flag; this.countryCodeOpen = false; this.phoneSearch = ''; }
}" @click.away="countryCodeOpen = false">

    <!-- Social profile header -->
    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100">
        @if($social['photo'])
            <img src="{{ $social['photo'] }}" alt="" class="w-12 h-12 rounded-full object-cover border border-slate-200">
        @else
            <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-lg">
                {{ strtoupper(substr($social['name'], 0, 1)) }}
            </div>
        @endif
        <div>
            <p class="font-semibold text-slate-900 text-sm">{{ $social['name'] }}</p>
            <p class="text-xs text-slate-500">{{ $social['email'] }}</p>
        </div>
        <span class="ms-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-xs font-medium border border-green-200">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            {{ __('auth/social-role-select.verified') }}
        </span>
    </div>

    <h1 class="text-xl font-bold text-slate-900 mb-1">{{ __('auth/social-role-select.one_last_step') }}</h1>
    <p class="text-sm text-slate-500 mb-5">{{ __('auth/social-role-select.subtitle') }}</p>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('social.role-select.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="phone_country_code" :value="selectedCode">

        <!-- Role selection -->
        <div>
            <p class="text-sm font-medium text-slate-700 mb-2.5">{{ __('auth/social-role-select.joining_as') }}</p>
            <div class="grid gap-2.5">
                <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition"
                    :class="role === 'customer' ? 'border-primary-500 bg-primary-50' : 'border-slate-200 hover:border-slate-300'">
                    <input type="radio" name="role" value="customer" x-model="role" class="sr-only">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition"
                        :class="role === 'customer' ? 'bg-primary-500 text-white' : 'bg-slate-100 text-slate-500'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" :class="role === 'customer' ? 'text-primary-700' : 'text-slate-900'">{{ __('auth/social-role-select.customer') }}</p>
                        <p class="text-xs text-slate-500">{{ __('auth/social-role-select.customer_desc') }}</p>
                    </div>
                    <div class="ms-auto w-5 h-5 rounded-full border-2 flex items-center justify-center transition"
                        :class="role === 'customer' ? 'border-primary-500 bg-primary-500' : 'border-slate-300'">
                        <div class="w-2 h-2 rounded-full bg-white" x-show="role === 'customer'"></div>
                    </div>
                </label>

                <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition"
                    :class="role === 'freelancer' ? 'border-accent-500 bg-accent-50' : 'border-slate-200 hover:border-slate-300'">
                    <input type="radio" name="role" value="freelancer" x-model="role" class="sr-only">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition"
                        :class="role === 'freelancer' ? 'bg-accent-500 text-white' : 'bg-slate-100 text-slate-500'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" :class="role === 'freelancer' ? 'text-accent-700' : 'text-slate-900'">{{ __('auth/social-role-select.freelancer') }}</p>
                        <p class="text-xs text-slate-500">{{ __('auth/social-role-select.freelancer_desc') }}</p>
                    </div>
                    <div class="ms-auto w-5 h-5 rounded-full border-2 flex items-center justify-center transition"
                        :class="role === 'freelancer' ? 'border-accent-500 bg-accent-500' : 'border-slate-300'">
                        <div class="w-2 h-2 rounded-full bg-white" x-show="role === 'freelancer'"></div>
                    </div>
                </label>

                <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition"
                    :class="role === 'business' ? 'border-accent-500 bg-accent-50' : 'border-slate-200 hover:border-slate-300'">
                    <input type="radio" name="role" value="business" x-model="role" class="sr-only">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition"
                        :class="role === 'business' ? 'bg-accent-500 text-white' : 'bg-slate-100 text-slate-500'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" :class="role === 'business' ? 'text-accent-700' : 'text-slate-900'">{{ __('auth/social-role-select.business') }}</p>
                        <p class="text-xs text-slate-500">{{ __('auth/social-role-select.business_desc') }}</p>
                    </div>
                    <div class="ms-auto w-5 h-5 rounded-full border-2 flex items-center justify-center transition"
                        :class="role === 'business' ? 'border-accent-500 bg-accent-500' : 'border-slate-300'">
                        <div class="w-2 h-2 rounded-full bg-white" x-show="role === 'business'"></div>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/social-role-select.phone_number') }}</label>
            <div class="flex gap-2">
                <div class="relative shrink-0" style="width: 108px">
                    <button type="button" @click="countryCodeOpen = !countryCodeOpen"
                        class="flex items-center gap-1.5 w-full h-full rounded-lg border border-slate-300 bg-white px-2.5 py-2.5 text-sm text-slate-700 hover:bg-slate-50 focus:border-primary-500 focus:outline-none transition justify-between">
                        <span x-text="selectedFlag + ' ' + selectedCode" class="font-medium text-xs"></span>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="countryCodeOpen" x-transition
                        class="absolute start-0 top-full mt-1 z-50 w-56 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden">
                        <div class="p-2 border-b border-slate-100">
                            <input type="text" x-model="phoneSearch" placeholder="{{ __('auth/social-role-select.search_country') }}"
                                class="w-full text-xs rounded-lg border border-slate-200 px-2.5 py-1.5 focus:border-primary-500 focus:outline-none">
                        </div>
                        <div class="max-h-44 overflow-y-auto py-1">
                            <template x-for="c in filteredCodes" :key="c.code">
                                <button type="button" @click="selectCode(c.code, c.flag)"
                                    class="flex items-center gap-2 w-full px-3 py-2 text-xs text-slate-700 hover:bg-primary-50 hover:text-primary-700 text-start transition"
                                    :class="{ 'bg-primary-50 text-primary-700 font-semibold': selectedCode === c.code }">
                                    <span x-text="c.flag"></span>
                                    <span x-text="c.name"></span>
                                    <span class="ms-auto text-slate-400 font-mono" x-text="c.code"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
                <input type="tel" name="phone" value="{{ old('phone') }}" required
                    placeholder="01X XXXX XXXX"
                    class="flex-1 block rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            </div>
            <p class="mt-1 text-xs text-slate-500">{{ __('auth/social-role-select.phone_hint') }}</p>
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
        </div>

        <button type="submit"
            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 active:bg-primary-700 transition">
            {{ __('auth/social-role-select.complete_registration') }}
            <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </form>
</div>
</x-guest-layout>
