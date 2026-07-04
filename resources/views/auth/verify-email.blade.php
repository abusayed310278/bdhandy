<x-guest-layout>
    <div class="mb-6 text-sm text-slate-500 leading-relaxed">
        {{ __('auth/verify-email.thanks_text') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-100">
            {{ __('auth/verify-email.sent_text') }}
        </div>
    @endif

    <div class="mt-8 flex flex-col gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" 
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-bold transition text-sm border border-transparent bg-primary-500 text-white hover:bg-primary-600 shadow-soft">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>{{ __('auth/verify-email.resend_btn') }}</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="flex justify-center">
            @csrf
            <button type="submit" class="text-xs text-slate-400 hover:text-primary-600 underline underline-offset-4 font-medium transition">
                {{ __('auth/verify-email.logout_btn') }}
            </button>
        </form>
    </div>
</x-guest-layout>
