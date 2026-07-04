<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            {{ __("Update your account's profile information and primary identity.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-8 space-y-6">
        @csrf
        @method('patch')
        
        <!-- Tab Identifier -->
        <input type="hidden" name="tab" value="general">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div class="col-span-1">
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" name="name" type="text" 
                    class="mt-1 block w-full bg-slate-50 cursor-not-allowed text-slate-500" 
                    :value="old('name', $user->name)" 
                    readonly 
                    required 
                    autocomplete="name" />
                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Full Name is Read Only
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <!-- Email -->
            <div class="col-span-1">
                <x-input-label for="email" :value="__('Email Address')" />
                <x-text-input id="email" name="email" type="email" 
                    class="mt-1 block w-full {{ $user->email_verified_at ? 'bg-slate-50 cursor-not-allowed text-slate-500' : '' }}" 
                    :value="old('email', $user->email)" 
                    :readonly="$user->email_verified_at"
                    required 
                    autocomplete="username" />
                @if($user->email_verified_at)
                    <p class="text-[10px] text-green-600 mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Verified Email - Read Only
                    </p>
                @endif
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-slate-800">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Phone -->
            <div class="col-span-1">
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" name="phone" type="text" 
                    class="mt-1 block w-full {{ $user->phone_verified_at ? 'bg-slate-50 cursor-not-allowed text-slate-500' : '' }}" 
                    :value="old('phone', $user->phone)" 
                    :readonly="$user->phone_verified_at"
                    placeholder="01XXXXXXXXX" />
                @if($user->phone_verified_at)
                    <p class="text-[10px] text-green-600 mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Verified Phone - Read Only
                    </p>
                @endif
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- Photo Upload (Hidden by default) -->
            <div class="col-span-1 hidden" x-on:open-photo-upload.window="$refs.photo.click()">
                <input type="file" class="hidden" x-ref="photo" name="photo" x-on:change="
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            ">
            </div>

            <!-- Gender -->
            <div class="col-span-1">
                <x-input-label for="gender" :value="__('Gender')" />
                <select id="gender" name="gender" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
            </div>

            <!-- Date of Birth -->
            <div class="col-span-1">
                <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))" />
                <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
            </div>

            <!-- Preferred Language -->
            <div class="col-span-1">
                <x-input-label for="preferred_language" :value="__('Preferred Language')" />
                <select id="preferred_language" name="preferred_language" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                    @foreach(\App\Models\Language::getActiveLanguages() as $lang)
                        <option value="{{ $lang->code }}" {{ old('preferred_language', $user->preferred_language) == $lang->code ? 'selected' : '' }}>
                            {{ $lang->name }} ({{ strtoupper($lang->code) }})
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('preferred_language')" />
            </div>

            <!-- Bio -->
            <div class="col-span-2">
                <x-input-label for="bio" :value="__('Short Bio')" />
                <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm" placeholder="Tell us a little about yourself...">{{ old('bio', $user->bio) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>{{ __('Save Profile') }}</x-primary-button>

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
