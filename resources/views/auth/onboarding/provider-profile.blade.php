<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth/onboarding/provider-profile.provider_profile') }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: {
                    primary: { 50:'#F0F8FF',100:'#E0F1FE',200:'#BAE0FD',400:'#38ADF7',500:'#0F94EA',600:'#0277C7',700:'#0561A1' },
                    accent:  { 50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',500:'#F97316',600:'#EA580C',700:'#C2410C' }
                }
            }}
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>html,body{font-family:'Inter',system-ui,sans-serif;}[dir="rtl"] .rtl-flip{transform:scaleX(-1);}</style>
</head>
<body class="min-h-full text-slate-700 antialiased">

<div class="min-h-screen flex flex-col">
    <!-- Top bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-2xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 shrink-0" dir="ltr">
                <span class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </span>
                <span class="font-bold text-slate-900">{{ config('app.name') }}</span>
            </a>
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <span class="w-6 h-6 rounded-full bg-primary-500 text-white flex items-center justify-center font-bold text-xs">1</span>
                <span class="font-semibold text-primary-600 hidden sm:inline">{{ __('auth/onboarding/provider-profile.profile') }}</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs hidden sm:flex">2</span>
                <span class="hidden sm:inline">{{ __('auth/onboarding/provider-profile.services') }}</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs hidden sm:flex">3</span>
                <span class="hidden sm:inline">{{ __('auth/onboarding/provider-profile.area') }}</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs hidden sm:flex">4</span>
                <span class="hidden sm:inline">{{ __('auth/onboarding/provider-profile.documents') }}</span>
            </div>
        </div>
    </header>

    <main class="flex-1 py-8 px-4">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">{{ __('auth/onboarding/provider-profile.title') }}</h1>
                <p class="text-slate-500 text-sm mt-1">{{ __('auth/onboarding/provider-profile.subtitle') }}</p>
            </div>

            @if($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    <ul class="space-y-1 list-disc list-inside">
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('provider.onboarding.profile.store') }}"
                  enctype="multipart/form-data" class="space-y-5"
                  @submit="if (window.phoneVerifyData && !window.phoneVerifyData.isVerified && window.phoneVerifyData.otpEnabled && !window.phoneVerifyData.verifyLater) { $event.preventDefault(); alert('{{ __('auth/onboarding/provider-profile.alert_verify_phone') }}'); }">
                @csrf

                {{-- Cover Photo + Profile Photo --}}
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden"
                     x-data="{
                        coverPreview: '{{ $profile?->cover_photo ? asset('storage/' . $profile->cover_photo) : '' }}',
                        photoPreview: '{{ $profile?->logo ? asset('storage/' . $profile->logo) : '' }}',
                        onCover(e) {
                            const f = e.target.files[0];
                            if (f) this.coverPreview = URL.createObjectURL(f);
                        },
                        onPhoto(e) {
                            const f = e.target.files[0];
                            if (f) this.photoPreview = URL.createObjectURL(f);
                        }
                     }">
                    <!-- Cover photo zone -->
                    <div class="relative w-full aspect-[1600/890] bg-gradient-to-r from-primary-100 to-primary-200">
                        <template x-if="coverPreview">
                            <img :src="coverPreview" class="absolute inset-0 w-full h-full object-cover">
                        </template>
                        <label class="absolute inset-0 flex items-center justify-center cursor-pointer group">
                            <input type="file" name="cover_photo" accept="image/*" class="sr-only" @change="onCover">
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/40 text-white text-xs font-medium group-hover:bg-black/60 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                                    {{ __('auth/onboarding/provider-profile.cover_photo') }}
                                </span>
                                <span class="text-[10px] text-white/90 bg-black/35 px-2 py-0.5 rounded backdrop-blur-xs font-normal">
                                    {{ __('web.onboarding.cover_recommended') }}
                                </span>
                            </div>
                        </label>
                        <!-- Profile photo overlay -->
                        <div class="absolute -bottom-8 left-5 flex items-end gap-3.5">
                            <div class="relative w-16 h-16 shrink-0">
                                <div class="w-full h-full rounded-xl border-4 border-white bg-slate-200 overflow-hidden shadow relative">
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" class="absolute inset-0 w-full h-full object-cover">
                                    </template>
                                    <template x-if="!photoPreview">
                                        <div class="absolute inset-0 flex items-center justify-center text-slate-400">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                        </div>
                                    </template>
                                </div>
                                <label class="absolute -bottom-1.5 -right-1.5 cursor-pointer z-10 w-6 h-6 rounded-full bg-primary-600 border-2 border-white shadow flex items-center justify-center text-white hover:bg-primary-700 transition active:scale-90">
                                    <input type="file" name="logo" accept="image/*" class="sr-only" @change="onPhoto">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </label>
                            </div>
                            <span class="text-[11px] text-slate-500 font-medium mb-1 shrink-0">
                                {{ __('web.onboarding.logo_recommended') }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5 pt-10 space-y-5">
                        <!-- Business / Display name -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                @auth
                                    {{ Auth::user()->hasRole('freelancer') ? __('auth/onboarding/provider-profile.display_name') : __('auth/onboarding/provider-profile.business_name') }}
                                @endauth
                                <span class="text-red-500">*</span>
                            </label>
                            @if(Auth::user()->hasRole('freelancer'))
                                <div class="relative">
                                    <input type="text" name="business_name"
                                        value="{{ Auth::user()->name }}" readonly
                                        class="block w-full rounded-lg border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-600 shadow-sm text-sm cursor-not-allowed">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">{{ __('auth/onboarding/provider-profile.from_account') }}</span>
                                </div>
                                <p class="mt-1 text-xs text-slate-400">{{ __('auth/onboarding/provider-profile.from_account_hint') }}</p>
                            @else
                                <input type="text" name="business_name" value="{{ old('business_name', $profile?->business_name) }}" required
                                    placeholder="e.g. Rahim Services Ltd."
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                                <x-input-error :messages="$errors->get('business_name')" class="mt-1" />
                            @endif
                        </div>

                        <!-- Tagline tags input -->
                        <div class="relative" x-data="taglineInput({{ Js::from(old('tagline', $profile?->tagline ?? '')) }}, {{ Js::from($taglineSuggestions ?? []) }})">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-profile.tagline_tags') }}</label>
                            <p class="text-xs text-slate-400 mb-2">{{ __('auth/onboarding/provider-profile.tagline_tags_hint') }}</p>

                            {{-- Tag chips + input --}}
                            <div @click="$refs.tagInput.focus(); updateSuggestions();"
                                class="min-h-[44px] flex flex-wrap gap-1.5 items-center rounded-lg border border-slate-300 px-3 py-2 cursor-text focus-within:ring-2 focus-within:ring-primary-100 focus-within:border-primary-500 bg-white transition shadow-sm">
                                <template x-for="tag in tags" :key="tag">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 text-xs font-semibold border border-primary-100">
                                        <span x-text="tag"></span>
                                        <button type="button" @click.stop="removeTag(tag)" class="text-primary-400 hover:text-primary-600 font-bold leading-none select-none transition">&times;</button>
                                    </span>
                                </template>
                                <input
                                    x-ref="tagInput"
                                    x-model="query"
                                    @input="updateSuggestions()"
                                    @keydown.enter.prevent="addFromQuery()"
                                    @keydown.comma.prevent="addFromQuery()"
                                    @keydown.backspace="if (query === '' && tags.length > 0) removeTag(tags[tags.length - 1]);"
                                    @blur="setTimeout(() => { showSuggestions = false; }, 200)"
                                    @focus="updateSuggestions(); showSuggestions = true;"
                                    placeholder="{{ __('auth/onboarding/provider-profile.tagline_placeholder') }}"
                                    class="flex-1 bg-transparent border-0 p-0 text-sm text-slate-900 placeholder-slate-400 focus:ring-0 focus:outline-none min-w-[120px]">
                            </div>

                            {{-- Hidden input to submit standard tags string --}}
                            <input type="hidden" name="tagline" :value="tags.join(', ')">

                            {{-- Suggestions dropdown --}}
                            <div x-show="showSuggestions && filtered.length > 0"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute z-50 mt-1 w-full rounded-lg bg-white border border-slate-200 shadow-lg py-1.5 max-h-48 overflow-y-auto"
                                style="display: none;">
                                <template x-for="suggestion in filtered" :key="suggestion">
                                    <button type="button" @mousedown.prevent="selectSuggestion(suggestion)"
                                        class="w-full text-left px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition flex items-center justify-between">
                                        <span x-text="suggestion"></span>
                                        <span class="text-[10px] text-slate-400 font-normal">{{ __('auth/onboarding/provider-profile.popular') }}</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-profile.about_description') }}</label>
                            <textarea name="description" rows="3"
                                placeholder="{{ __('auth/onboarding/provider-profile.about_placeholder') }}"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm resize-none">{{ old('description', $profile?->description) }}</textarea>
                        </div>

                        <!-- Experience -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-profile.years_experience') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="years_of_experience" min="0" max="60" required
                                    value="{{ old('years_of_experience', $profile?->years_of_experience) }}"
                                    placeholder="0"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                                <x-input-error :messages="$errors->get('years_of_experience')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-profile.experience_level') }} <span class="text-red-500">*</span></label>
                                <select name="experience_level" required
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                                    <option value="">{{ __('auth/onboarding/provider-profile.select_level') }}</option>
                                    @foreach(['beginner' => __('auth/onboarding/provider-profile.beginner'), 'intermediate' => __('auth/onboarding/provider-profile.intermediate'), 'expert' => __('auth/onboarding/provider-profile.expert')] as $val => $label)
                                        <option value="{{ $val }}" @selected(old('experience_level', $profile?->experience_level) === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('experience_level')" class="mt-1" />
                            </div>

                            <!-- Default Currency -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Default Currency <span class="text-red-500">*</span></label>
                                <select name="currency_id" required
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" @selected(old('currency_id', $profile?->currency_id ?? 1) == $currency->id)>
                                            {{ $currency->name }} ({{ $currency->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-slate-400">Used as the default currency across your services and invoices.</p>
                                <x-input-error :messages="$errors->get('currency_id')" class="mt-1" />
                            </div>
                        </div>

                        <!-- Languages spoken -->
                        @if($languages->isNotEmpty())
                        <div x-data="{
                            selected: {{ Js::from(old('languages', $profile?->languages ?? [])) }},
                            opts: {{ Js::from($languages->map(fn($l) => ['id' => $l->name, 'name' => $l->name])->values()) }},
                            toggle(id){ this.selected.includes(id) ? this.selected.splice(this.selected.indexOf(id),1) : this.selected.push(id) }
                        }">
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('auth/onboarding/provider-profile.languages_speak') }} <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="lang in opts" :key="lang.id">
                                    <button type="button" @click="toggle(lang.id)"
                                        class="px-3 py-1.5 rounded-full border text-xs font-medium transition"
                                        :class="selected.includes(lang.id) ? 'bg-primary-500 border-primary-500 text-white' : 'border-slate-300 text-slate-600 hover:border-primary-300'">
                                        <span x-text="lang.name"></span>
                                    </button>
                                </template>
                            </div>
                            <template x-for="(l,i) in selected" :key="i">
                                <input type="hidden" name="languages[]" :value="l">
                            </template>
                            <x-input-error :messages="$errors->get('languages')" class="mt-1" />
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Contact info -->
                @php
                    $isFreelancer  = auth()->user()->hasRole('freelancer');
                    $otpEnabled    = (bool) \App\Models\Setting::get('otp_verification_enabled', '0');
                    $isPhoneVerified = (bool) auth()->user()->phone_verified_at;
                    $defaultPhone  = old('primary_phone', $profile?->primary_phone ?? auth()->user()->phone ?? '');
                @endphp
                <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4"
                     x-data="phoneVerify({
                          phone:      {{ Js::from($defaultPhone) }},
                          isVerified: {{ $isPhoneVerified ? 'true' : 'false' }},
                          otpEnabled: {{ $otpEnabled ? 'true' : 'false' }},
                          sendUrl:    '{{ route('provider.otp.send') }}',
                          verifyUrl:  '{{ route('provider.otp.verify') }}',
                          inputId:    'onboarding_phone',
                          verifyLater: {{ old('verify_later') ? 'true' : 'false' }}
                      })"
                     @keydown.escape.window="closeModal()">

                    <h3 class="text-sm font-semibold text-slate-900">{{ __('auth/onboarding/provider-profile.contact_details') }}</h3>



                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Business Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                {{ __('auth/onboarding/provider-profile.business_phone') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="tel" name="primary_phone"
                                    id="onboarding_phone"
                                    x-model="currentPhone"
                                    required
                                    placeholder="+880 1X XXXX XXXX"
                                    :readonly="isVerified"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pe-28 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm"
                                    :class="isVerified ? 'bg-slate-50 border-slate-200 text-slate-600' : ''">

                                {{-- Badge / Button --}}
                                <div class="absolute right-2 top-1/2 -translate-y-1/2">
                                    <template x-if="isVerified">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-bold border border-green-200 select-none">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            {{ __('auth/onboarding/provider-profile.verified') }}
                                        </span>
                                    </template>
                                    <template x-if="!isVerified && otpEnabled && !verifyLater">
                                        <button type="button" @click="openSendOtp()"
                                            :disabled="sending"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-500 hover:bg-primary-600 text-white text-xs font-bold transition disabled:opacity-50">
                                            <svg x-show="sending" class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            <span x-text="sending ? '' : '{{ __('auth/onboarding/provider-profile.verify') }}'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            @error('primary_phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- Verify Later checkbox --}}
                            @if($otpEnabled && !$isPhoneVerified)
                            <label class="mt-2.5 flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="verify_later" value="1"
                                    x-model="verifyLater"
                                    class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-100">
                                <span class="text-xs text-slate-500">{{ __('auth/onboarding/provider-profile.skip_phone_verification') }}</span>
                            </label>
                            @endif
                        </div>

                        {{-- WhatsApp --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-profile.whatsapp_number') }}</label>
                            <input type="tel" name="whatsapp_number"
                                value="{{ old('whatsapp_number', $profile?->whatsapp_number) }}"
                                placeholder="+880 1X XXXX XXXX"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                        </div>
                    </div>

                    {{-- OTP Modal (shared partial) --}}
                    @include('partials.otp-modal')

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-profile.website') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            </span>
                            <input type="text" name="website" value="{{ old('website', $profile?->website) }}"
                                placeholder="https://yourwebsite.com"
                                class="block w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-profile.facebook') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </span>
                                <input type="text" name="facebook_url" value="{{ old('facebook_url', $profile?->facebook_url) }}"
                                    placeholder="https://facebook.com/..."
                                    class="block w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-profile.instagram') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-pink-500">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </span>
                                <input type="text" name="instagram_url" value="{{ old('instagram_url', $profile?->instagram_url) }}"
                                    placeholder="https://instagram.com/..."
                                    class="block w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-profile.youtube') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-red-500">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
                                </span>
                                <input type="text" name="youtube_url" value="{{ old('youtube_url', $profile?->youtube_url) }}"
                                    placeholder="https://youtube.com/..."
                                    class="block w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Emergency available -->
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="emergency_available" value="0">
                        <input type="checkbox" name="emergency_available" value="1"
                            @checked(old('emergency_available', $profile?->emergency_available))
                            class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-100">
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ __('auth/onboarding/provider-profile.emergency_available') }}</p>
                            <p class="text-xs text-slate-500">{{ __('auth/onboarding/provider-profile.emergency_badge_hint') }}</p>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 active:bg-primary-700 transition">
                        {{ __('auth/onboarding/provider-profile.save_continue') }}
                        <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
<script>
function taglineInput(initial, suggestions) {
  return {
    tags: initial ? initial.split(',').map(t => t.trim()).filter(Boolean) : [],
    query: '',
    suggestions: suggestions || [],
    filtered: [],
    showSuggestions: false,
    updateSuggestions() {
      const q = this.query.trim().toLowerCase();
      const currentTags = this.tags.map(t => t.toLowerCase());
      if (!q) {
        this.filtered = this.suggestions.filter(s => !currentTags.includes(s.toLowerCase())).slice(0, 8);
        this.showSuggestions = this.filtered.length > 0;
      } else {
        this.filtered = this.suggestions.filter(s => s.toLowerCase().includes(q) && !currentTags.includes(s.toLowerCase())).slice(0, 8);
        this.showSuggestions = this.filtered.length > 0;
      }
    },
    addFromQuery(e) {
      if (e && e.key === ',') e.preventDefault();
      const tag = this.query.replace(/,$/, '').trim();
      const currentTags = this.tags.map(t => t.toLowerCase());
      if (tag && !currentTags.includes(tag.toLowerCase())) this.tags.push(tag);
      this.query = '';
      this.filtered = [];
      this.showSuggestions = false;
    },
    selectSuggestion(s) {
      const currentTags = this.tags.map(t => t.toLowerCase());
      if (!currentTags.includes(s.toLowerCase())) this.tags.push(s);
      this.query = '';
      this.filtered = [];
      this.showSuggestions = false;
    },
    removeTag(tag) { this.tags = this.tags.filter(t => t !== tag); },
    onBackspace() { if (!this.query && this.tags.length > 0) this.tags.pop(); }
  };
}

function phoneVerify({ phone, isVerified, otpEnabled, sendUrl, verifyUrl, inputId, verifyLater }) {
    return {
        init() {
            window.phoneVerifyData = this;
        },
        currentPhone: phone,
        isVerified:   isVerified,
        otpEnabled:   otpEnabled,
        verifyLater:  verifyLater || false,
        sendingPhone: '',
        modalOpen:    false,
        sending:      false,
        verifying:    false,
        verified:     false,
        otp:          ['','','','','',''],
        otpError:     '',
        countdown:    0,
        _timer:       null,

        openSendOtp() {
            this.sendingPhone = this.currentPhone;
            this.otp          = ['','','','','',''];
            this.otpError     = '';
            this.verified     = false;
            this._doSend();
        },
        closeModal() {
            this.modalOpen = false;
            clearInterval(this._timer);
        },
        editPhone() {
            this.modalOpen = false;
            clearInterval(this._timer);
            this.$nextTick(() => document.getElementById(inputId)?.focus());
        },
        async resendOtp() {
            this.otp      = ['','','','','',''];
            this.otpError = '';
            document.querySelectorAll('.otp-box').forEach(el => el.value = '');
            await this._doSend();
        },
        async _doSend() {
            this.sending = true;
            try {
                const r    = await fetch(sendUrl, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body:    JSON.stringify({ phone: this.sendingPhone }),
                });
                const data = await r.json();
                if (data.success || data.throttled) {
                    this.modalOpen = true;
                    this._startCountdown(data.retry_after || 300);
                } else {
                    alert(data.message || '{{ __('auth/onboarding/provider-profile.failed_send_otp') }}');
                }
            } catch { alert('{{ __('auth/onboarding/provider-profile.network_error') }}'); }
            finally   { this.sending = false; }
        },
        async verifyOtp() {
            const code = this.otp.join('');
            if (code.length < 6) return;
            this.verifying = true;
            this.otpError  = '';
            try {
                const r    = await fetch(verifyUrl, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body:    JSON.stringify({ phone: this.sendingPhone, code }),
                });
                const data = await r.json();
                if (data.success) {
                    this.verified    = true;
                    this.isVerified  = true;
                    clearInterval(this._timer);
                    setTimeout(() => this.modalOpen = false, 2000);
                } else {
                    this.otpError = data.message || 'Invalid OTP.';
                }
            } catch { this.otpError = '{{ __('auth/onboarding/provider-profile.network_error') }}'; }
            finally   { this.verifying = false; }
        },
        otpInput(e, i) {
            const v = e.target.value.replace(/\D/g,'').slice(-1);
            e.target.value = v;
            this.otp[i]    = v;
            this.otpError  = '';
            if (v && i < 5) {
                const boxes = document.querySelectorAll('.otp-box');
                boxes[i+1]?.focus();
            }
            if (this.otp.join('').length === 6) this.verifyOtp();
        },
        otpKeydown(e, i) {
            if (e.key === 'Backspace' && !e.target.value && i > 0) {
                document.querySelectorAll('.otp-box')[i-1]?.focus();
            }
        },
        otpPaste(e) {
            const txt   = e.clipboardData.getData('text').replace(/\D/g,'').slice(0,6);
            const boxes = document.querySelectorAll('.otp-box');
            txt.split('').forEach((c,i) => { this.otp[i] = c; if (boxes[i]) boxes[i].value = c; });
            if (txt.length === 6) this.verifyOtp();
        },
        _startCountdown(secs) {
            this.countdown = secs;
            clearInterval(this._timer);
            this._timer = setInterval(() => { if (this.countdown > 0) this.countdown--; else clearInterval(this._timer); }, 1000);
        },
        fmtCountdown(s) { return `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`; }
    };
}
</script>
</html>
