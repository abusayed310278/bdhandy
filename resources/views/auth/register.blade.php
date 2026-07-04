<x-guest-layout>
<div
    x-data="{
        step: {{ old('role') ? 2 : 1 }},
        role: '{{ old('role', '') }}',
        showPass: false,
        showConfirm: false,
        phoneSearch: '',
        countryCodeOpen: false,
        selectedCode: '{{ old('phone_country_code', '+880') }}',
        selectedFlag: '{{ old('phone_country_code', '+880') === '+971' ? '🇦🇪' : '🇧🇩' }}',
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
            { code: '+49',  flag: '🇩🇪', name: 'Germany' },
            { code: '+33',  flag: '🇫🇷', name: 'France' },
            { code: '+998', flag: '🇺🇿', name: 'Uzbekistan' },
        ],
        get filteredCodes() {
            if (!this.phoneSearch) return this.countryCodes;
            const q = this.phoneSearch.toLowerCase();
            return this.countryCodes.filter(c => c.name.toLowerCase().includes(q) || c.code.includes(q));
        },
        selectCode(code, flag) {
            this.selectedCode = code;
            this.selectedFlag = flag;
            this.countryCodeOpen = false;
            this.phoneSearch = '';
        },
        selectRole(r) {
            this.role = r;
            this.step = 2;
        }
    }"
    @click.away="countryCodeOpen = false"
>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- STEP 1: Role Selection --}}
    <div x-show="step === 1" x-transition>
        <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ __('auth/register.create_account') }}</h1>
        <p class="text-sm text-slate-500 mb-6">{{ __('auth/register.subtitle') }}</p>

        <!-- Social buttons -->
        <div class="space-y-3 mb-6">
            <a href="{{ route('auth.social', 'google') }}"
               class="flex items-center justify-center gap-3 w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                {{ __('auth/register.continue_google') }}
            </a>
            <a href="{{ route('auth.social', 'facebook') }}"
               class="flex items-center justify-center gap-3 w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-5 h-5 shrink-0 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                {{ __('auth/register.continue_facebook') }}
            </a>
        </div>

        <div class="relative mb-5">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
            <div class="relative flex justify-center"><span class="bg-white px-3 text-xs text-slate-400 font-medium">{{ __('auth/register.or_email') }}</span></div>
        </div>

        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ __('auth/register.joining_as') }}</p>
        <div class="grid gap-3">
            <!-- Customer -->
            <button type="button" @click="selectRole('customer')"
                class="flex items-start gap-4 w-full text-start px-4 py-4 rounded-xl border-2 border-slate-200 hover:border-primary-400 hover:bg-primary-50 transition group">
                <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-primary-500 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900 text-sm">{{ __('auth/register.customer') }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('auth/register.customer_desc') }}</p>
                </div>
                <svg class="w-5 h-5 text-slate-300 group-hover:text-primary-500 ms-auto self-center rtl-flip transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>

            <!-- Freelancer -->
            <button type="button" @click="selectRole('freelancer')"
                class="flex items-start gap-4 w-full text-start px-4 py-4 rounded-xl border-2 border-slate-200 hover:border-accent-400 hover:bg-accent-50 transition group">
                <div class="w-10 h-10 rounded-lg bg-accent-100 text-accent-600 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-accent-500 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900 text-sm">{{ __('auth/register.freelancer') }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('auth/register.freelancer_desc') }}</p>
                </div>
                <svg class="w-5 h-5 text-slate-300 group-hover:text-accent-500 ms-auto self-center rtl-flip transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>

            <!-- Business -->
            <button type="button" @click="selectRole('business')"
                class="flex items-start gap-4 w-full text-start px-4 py-4 rounded-xl border-2 border-slate-200 hover:border-accent-400 hover:bg-accent-50 transition group">
                <div class="w-10 h-10 rounded-lg bg-accent-100 text-accent-600 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-accent-500 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-900 text-sm">{{ __('auth/register.business') }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('auth/register.business_desc') }}</p>
                </div>
                <svg class="w-5 h-5 text-slate-300 group-hover:text-accent-500 ms-auto self-center rtl-flip transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- STEP 2: Account Details --}}
    <div x-show="step === 2" x-transition style="display:none">
        <!-- Back + header -->
        <div class="flex items-center gap-3 mb-5">
            <button type="button" @click="step = 1"
                class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 transition">
                <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div>
                <h1 class="text-xl font-bold text-slate-900 leading-tight">{{ __('auth/register.create_account') }}</h1>
                <p class="text-xs text-slate-500" x-text="role === 'customer' ? '{{ __('auth/register.customer_account') }}' : (role === 'freelancer' ? '{{ __('auth/register.freelancer_provider') }}' : '{{ __('auth/register.business_provider') }}')"></p>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="role" :value="role">
            <input type="hidden" name="phone_country_code" :value="selectedCode">

            <!-- Full name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/register.full_name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                    placeholder="{{ __('auth/register.your_full_name') }}"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/register.email_address') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    placeholder="{{ __('auth/register.email_placeholder') }}"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Phone with country code -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/register.phone_number') }}</label>
                <div class="flex gap-2">
                    <!-- Country code dropdown -->
                    <div class="relative shrink-0" style="width: 108px">
                        <button type="button" @click="countryCodeOpen = !countryCodeOpen"
                            class="flex items-center gap-1.5 w-full h-full rounded-lg border border-slate-300 bg-white px-2.5 py-2.5 text-sm text-slate-700 hover:bg-slate-50 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition justify-between">
                            <span x-text="selectedFlag + ' ' + selectedCode" class="font-medium text-xs"></span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="countryCodeOpen" x-transition
                            class="absolute start-0 top-full mt-1 z-50 w-56 bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden">
                            <div class="p-2 border-b border-slate-100">
                                <input type="text" x-model="phoneSearch" placeholder="{{ __('auth/register.search_country') }}"
                                    class="w-full text-xs rounded-lg border border-slate-200 px-2.5 py-1.5 focus:border-primary-500 focus:ring-1 focus:ring-primary-100 focus:outline-none">
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
                    <!-- Phone input -->
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                        placeholder="01X XXXX XXXX"
                        class="flex-1 block rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                </div>
                <p class="mt-1 text-xs text-slate-500">{{ __('auth/register.phone_hint') }}</p>
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/register.password') }}</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required autocomplete="new-password"
                        placeholder="{{ __('auth/register.password_placeholder') }}"
                        class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pe-11 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                    <button type="button" @click="showPass = !showPass"
                        class="absolute inset-y-0 end-0 flex items-center pe-3 text-slate-400 hover:text-slate-600">
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPass" class="w-4 h-4" style="display:none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/register.confirm_password') }}</label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                        placeholder="{{ __('auth/register.confirm_password_placeholder') }}"
                        class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pe-11 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                    <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 end-0 flex items-center pe-3 text-slate-400 hover:text-slate-600">
                        <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showConfirm" class="w-4 h-4" style="display:none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <!-- Provider notice -->
            <div x-show="role !== 'customer'" class="rounded-lg bg-accent-50 border border-accent-200 p-3 text-xs text-accent-700">
                <strong>{{ __('auth/register.provider_notice_label') }}</strong> {{ __('auth/register.provider_notice_desc') }}
            </div>

            <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 active:bg-primary-700 transition">
                {{ __('auth/register.create_account_btn') }}
                <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>

            <p class="text-center text-xs text-slate-500">
                {{ __('auth/register.agree_text') }}
                <a href="#" class="text-primary-600 hover:underline">{{ __('auth/register.terms') }}</a> {{ __('auth/register.and') }}
                <a href="#" class="text-primary-600 hover:underline">{{ __('auth/register.privacy') }}</a>.
            </p>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-slate-500">
        {{ __('auth/register.already_have_account') }}
        <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-semibold">{{ __('auth/register.sign_in') }}</a>
    </p>
</div>
</x-guest-layout>
