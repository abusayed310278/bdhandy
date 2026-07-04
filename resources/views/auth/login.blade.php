<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if($errors->has('social'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('social') }}
        </div>
    @endif

    <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ __('auth/login.welcome_back') }}</h1>
    <p class="text-sm text-slate-500 mb-6">{{ __('auth/login.subtitle') }}</p>

    <!-- Social login -->
    <div class="space-y-3 mb-6">
        <a href="{{ route('auth.social', 'google') }}"
           class="flex items-center justify-center gap-3 w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            {{ __('auth/login.continue_google') }}
        </a>
        <a href="{{ route('auth.social', 'facebook') }}"
           class="flex items-center justify-center gap-3 w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
            <svg class="w-5 h-5 shrink-0 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            {{ __('auth/login.continue_facebook') }}
        </a>
    </div>

    <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
        <div class="relative flex justify-center"><span class="bg-white px-3 text-xs text-slate-400 font-medium">{{ __('auth/login.or_email') }}</span></div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/login.email_address') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div x-data="{ showPass: false }">
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/login.password') }}</label>
            <div class="relative">
                <input id="password" :type="showPass ? 'text' : 'password'" name="password" required autocomplete="current-password"
                    placeholder="{{ __('auth/login.your_password') }}"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pe-11 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                <button type="button" @click="showPass = !showPass"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-slate-400 hover:text-slate-600">
                    <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showPass" class="w-4 h-4" style="display:none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            <div class="flex items-center justify-between mt-1.5">
                <x-input-error :messages="$errors->get('password')" />
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium ms-auto">{{ __('auth/login.forgot_password') }}</a>
                @endif
            </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" id="remember_me"
                class="rounded border-slate-300 text-primary-600 shadow-sm focus:ring-primary-100">
            <span class="text-sm text-slate-600">{{ __('auth/login.remember_me') }}</span>
        </label>

        <button type="submit"
            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 active:bg-primary-700 transition">
            {{ __('auth/login.sign_in') }}
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        {{ __('auth/login.no_account') }}
        <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-semibold">{{ __('auth/login.create_free') }}</a>
    </p>
</x-guest-layout>
