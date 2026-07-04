<x-guest-layout>
    <div class="mb-6 text-sm text-slate-500 leading-relaxed">
        {{ __('auth/confirm-password.secure_area') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('auth/confirm-password.password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('auth/confirm-password.confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
