@extends('layouts.dashboard')
@section('title', 'Provider Profile')

@section('content')
<div class="space-y-6 text-sm max-w-3xl">

  <!-- Header -->
  <div>
    <h2 class="text-xl font-bold text-slate-900">Provider Profile</h2>
    <p class="text-slate-500 text-xs mt-0.5">Update your public profile information</p>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
      {{ session('success') }}
    </div>
  @endif

  <form action="{{ route('provider.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
        @submit="if (window.phoneVerifyData && !window.phoneVerifyData.isVerified && window.phoneVerifyData.otpEnabled) { $event.preventDefault(); alert('Please verify your phone number before saving.'); }">
    @csrf
    @method('PUT')

    {{-- Cover Photo + Profile Photo --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden"
         x-data="{
            coverPreview: '{{ $profile->cover_photo ? Storage::url($profile->cover_photo) : '' }}',
            photoPreview: '{{ $profile->logo ? Storage::url($profile->logo) : '' }}',
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
      <div class="relative w-full aspect-[1600/500] sm:aspect-[1600/400] bg-gradient-to-r from-primary-100 to-primary-200">
        <template x-if="coverPreview">
          <img :src="coverPreview" class="absolute inset-0 w-full h-full object-cover">
        </template>
        <label class="absolute inset-0 flex items-center justify-center cursor-pointer group">
          <input type="file" name="cover_photo" accept="image/*" class="sr-only" @change="onCover">
          <div class="flex flex-col items-center gap-1.5">
            <span class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/40 text-white text-xs font-medium group-hover:bg-black/60 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
              Cover photo
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
          <div class="pb-1 text-left">
            <p class="font-bold text-slate-900 text-sm leading-tight mb-0.5">
            </p>
            <span class="text-[10px] text-slate-500 font-medium">
              {{ __('web.onboarding.logo_recommended') }}
            </span>
          </div>
        </div>
      </div>
      <div class="h-10"></div>
    </div>

    {{-- Basic Info --}}
    <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100">
      <div class="px-5 py-4">
        <h3 class="font-semibold text-slate-900">Basic Information</h3>
      </div>
      <div class="p-5 grid sm:grid-cols-2 gap-5">

        <!-- Business / Display name -->
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1.5">
            {{ Auth::user()->hasRole('freelancer') ? 'Display name' : 'Business name' }}
            <span class="text-red-500">*</span>
          </label>
          @if(Auth::user()->hasRole('freelancer'))
            <div class="relative">
              <input type="text" name="business_name"
                value="{{ Auth::user()->name }}" readonly
                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-600 shadow-sm text-sm cursor-not-allowed">
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">From account</span>
            </div>
            <p class="mt-1 text-xs text-slate-400">Taken from your account profile — edit it in account settings.</p>
          @else
            <input type="text" name="business_name" value="{{ old('business_name', $profile->business_name) }}" required
              placeholder="e.g. Rahim Services Ltd."
              class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            @error('business_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          @endif

          {{-- Public Profile Link Preview & Warning --}}
          <div class="mt-2.5 flex flex-col gap-1.5">
            <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500 bg-slate-50 border border-slate-100 rounded-xl px-3.5 py-2">
              <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
              </svg>
              <span class="font-medium text-slate-600">Public Profile Link:</span>
              <a href="{{ route('provider.profile.public', $profile->slug) }}" target="_blank" class="text-primary-600 hover:text-primary-700 hover:underline font-semibold break-all inline-flex items-center gap-1">
                {{ route('provider.profile.public', $profile->slug) }}
                <svg class="w-3 h-3 text-primary-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
              </a>
            </div>
            @if(!Auth::user()->hasRole('freelancer'))
              <div class="flex items-start gap-2 text-[11px] leading-relaxed text-amber-700 bg-amber-50/70 border border-amber-100/70 rounded-xl px-3.5 py-2">
                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span><strong>Important Note:</strong> Changing your business name will automatically update your public profile URL (slug) to match the new name. Any old links shared previously will become inactive.</span>
              </div>
            @endif
          </div>
        </div>

        {{-- Tagline tag input --}}
        <div class="sm:col-span-2 relative" x-data="taglineInput({{ Js::from(old('tagline', $profile->tagline ?? '')) }}, {{ Js::from($taglineSuggestions) }})">
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Tagline Tags</label>
          <p class="text-xs text-slate-400 mb-2">Add comma-separated tags that describe your service (e.g. Fast Service, Affordable, Licensed). Click or type to see suggestions.</p>

          {{-- Tag chips + input --}}
          <div @click="$refs.tagInput.focus(); updateSuggestions();"
            class="min-h-[44px] flex flex-wrap gap-1.5 items-center rounded-xl border border-slate-300 px-3 py-2 cursor-text focus-within:ring-2 focus-within:ring-primary-100 focus-within:border-primary-500 bg-white transition shadow-sm">
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
              @focus="updateSuggestions()"
              @keydown.enter.prevent="addFromQuery"
              @keydown.","="addFromQuery"
              @keydown.backspace="onBackspace"
              @keydown.escape="showSuggestions = false"
              @blur="setTimeout(() => showSuggestions = false, 150)"
              type="text"
              placeholder="Type or select tagline..."
              class="flex-1 min-w-[120px] border-none outline-none bg-transparent text-sm placeholder-slate-400 py-0.5">
          </div>

          {{-- Suggestions dropdown --}}
          <div x-show="showSuggestions && filtered.length > 0"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden max-h-48 overflow-y-auto">
            <template x-for="s in filtered" :key="s">
              <button type="button" @mousedown.prevent="selectSuggestion(s)"
                class="w-full text-left px-4 py-2.5 text-sm hover:bg-primary-50 hover:text-primary-700 transition flex items-center gap-2 font-medium">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span x-text="s"></span>
              </button>
            </template>
          </div>

          {{-- Hidden field for form submit --}}
          <input type="hidden" name="tagline" :value="tags.join(', ')">

          <p class="mt-1.5 text-xs text-slate-400">
            <span x-text="tags.length" class="font-semibold text-slate-600"></span> tag<span x-show="tags.length !== 1">s</span> added
          </p>
          @error('tagline') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1.5">About / Description</label>
          <textarea name="description" rows="3"
            placeholder="Tell customers what you do, your specialties, and what makes you the best choice..."
            class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm resize-none">{{ old('description', $profile->description) }}</textarea>
          @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Years of Experience --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Years of experience <span class="text-red-500">*</span></label>
          <input type="number" name="years_of_experience" min="0" max="60" required
            value="{{ old('years_of_experience', $profile->years_of_experience) }}"
            placeholder="0"
            class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
          @error('years_of_experience') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Experience Level --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Experience level <span class="text-red-500">*</span></label>
          <select name="experience_level" required
            class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            <option value="">Select level</option>
            @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'expert' => 'Expert'] as $val => $label)
              <option value="{{ $val }}" @selected(old('experience_level', $profile->experience_level) === $val)>{{ $label }}</option>
            @endforeach
          </select>
          @error('experience_level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Default Currency --}}
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Default Currency <span class="text-red-500">*</span></label>
          <select name="currency_id" required
            class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            @foreach($currencies as $currency)
              <option value="{{ $currency->id }}" @selected(old('currency_id', $profile->currency_id ?? 1) == $currency->id)>
                {{ $currency->name }} ({{ $currency->symbol }})
              </option>
            @endforeach
          </select>
          <p class="mt-1 text-xs text-slate-400">Used as the default currency across your services and invoices.</p>
          @error('currency_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

      </div>
    </div>

    {{-- Contact & Links --}}
    @php
        $otpEnabled      = (bool) \App\Models\Setting::get('otp_verification_enabled', '0');
        $isPhoneVerified = (bool) auth()->user()->phone_verified_at;
        $defaultPhone    = old('primary_phone', $profile->primary_phone ?? auth()->user()->phone ?? '');
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100"
         x-data="phoneVerify({
             phone:      {{ Js::from($defaultPhone) }},
             isVerified: {{ $isPhoneVerified ? 'true' : 'false' }},
             otpEnabled: {{ $otpEnabled ? 'true' : 'false' }},
             sendUrl:    '{{ route('provider.otp.send') }}',
             verifyUrl:  '{{ route('provider.otp.verify') }}',
             inputId:    'edit_phone'
         })"
         @keydown.escape.window="closeModal()">
      <div class="px-5 py-4">
        <h3 class="font-semibold text-slate-900">Contact & Links</h3>
      </div>
      <div class="p-5 space-y-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {{-- Business/Primary Phone --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
              {{ Auth::user()->hasRole('freelancer') ? 'Phone' : 'Business phone' }} <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <input type="tel" name="primary_phone"
                id="edit_phone"
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
                    Verified
                  </span>
                </template>
                <template x-if="!isVerified && otpEnabled">
                  <button type="button" @click="openSendOtp()" :disabled="sending"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-500 hover:bg-primary-600 text-white text-xs font-bold transition disabled:opacity-50">
                    <svg x-show="sending" class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span x-text="sending ? '' : 'Verify'"></span>
                  </button>
                </template>
              </div>
            </div>
            @error('primary_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

            @if($otpEnabled && !$isPhoneVerified)
            <p class="mt-1.5 text-xs text-amber-600 flex items-center gap-1">
              <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75h.007v.008H12v-.008z"/></svg>
              Phone not verified. Click <strong class="font-semibold">Verify</strong> to verify via SMS.
            </p>
            @endif
          </div>

          {{-- WhatsApp --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">WhatsApp number</label>
            <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number', $profile->whatsapp_number) }}"
              placeholder="+880 1X XXXX XXXX"
              class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            @error('whatsapp_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Website --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Website</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </span>
            <input type="text" name="website" value="{{ old('website', $profile->website) }}"
              placeholder="https://yourwebsite.com"
              class="block w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
          </div>
          @error('website') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Social Links --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Facebook</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </span>
              <input type="text" name="facebook_url" value="{{ old('facebook_url', $profile->facebook_url) }}"
                placeholder="https://facebook.com/..."
                class="block w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            </div>
            @error('facebook_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Instagram</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-pink-500">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
              </span>
              <input type="text" name="instagram_url" value="{{ old('instagram_url', $profile->instagram_url) }}"
                placeholder="https://instagram.com/..."
                class="block w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            </div>
            @error('instagram_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">YouTube</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-red-500">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
              </span>
              <input type="text" name="youtube_url" value="{{ old('youtube_url', $profile->youtube_url) }}"
                placeholder="https://youtube.com/..."
                class="block w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            </div>
            @error('youtube_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- OTP Modal --}}
        @include('partials.otp-modal')

      </div>
    </div>

    {{-- Languages & Preference --}}
    <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100">
      <div class="px-5 py-4">
        <h3 class="font-semibold text-slate-900">Languages & Availability</h3>
      </div>
      <div class="p-5 space-y-5">

        <div x-data="{
          selected: {{ Js::from(old('languages', $profile->languages ?? [])) }},
          toggle(langName) {
            if (this.selected.includes(langName)) {
              this.selected = this.selected.filter(item => item !== langName);
            } else {
              this.selected.push(langName);
            }
          }
        }">
          <label class="block text-sm font-medium text-slate-700 mb-2">Languages Spoken <span class="text-red-500">*</span></label>
          <div class="flex flex-wrap gap-2">
            @foreach($languages as $lang)
              <button type="button" @click="toggle('{{ $lang['name'] }}')"
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border cursor-pointer select-none transition focus:outline-none"
                :class="selected.includes('{{ $lang['name'] }}') 
                  ? 'bg-primary-50 border-primary-300 text-primary-800 font-semibold' 
                  : 'bg-white border-slate-200 text-slate-600 hover:border-primary-200 hover:bg-slate-50'">
                
                <!-- Checkbox visualization -->
                <span class="w-4 h-4 rounded border flex items-center justify-center transition-colors"
                  :class="selected.includes('{{ $lang['name'] }}') ? 'bg-primary-600 border-primary-600 text-white' : 'border-slate-300 bg-white'">
                  <svg class="w-2.5 h-2.5 text-white" :class="selected.includes('{{ $lang['name'] }}') ? 'block' : 'hidden'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </span>
                
                <span class="text-xs font-medium">{{ $lang['name'] }}</span>
              </button>
            @endforeach
          </div>

          <!-- Hidden inputs to submit selection -->
          <template x-for="lang in selected" :key="lang">
            <input type="hidden" name="languages[]" :value="lang">
          </template>

          @error('languages') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between py-3 border border-slate-100 rounded-xl px-4"
          x-data="{ on: {{ old('emergency_available', $profile->emergency_available ?? false) ? 'true' : 'false' }} }">
          <div>
            <p class="text-sm font-medium text-slate-900">Emergency Available</p>
            <p class="text-xs text-slate-500">Accept urgent / emergency service requests</p>
          </div>
          <button type="button" @click="on = !on"
            class="relative inline-flex h-6 w-11 shrink-0 rounded-full transition-colors duration-200 ease-in-out focus:outline-none"
            :class="on ? 'bg-primary-500' : 'bg-slate-200'">
            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ms-0.5"
              :class="on ? 'translate-x-5' : 'translate-x-0'"></span>
          </button>
          <input type="hidden" name="emergency_available" :value="on ? '1' : '0'">
        </div>

      </div>
    </div>

    <div class="flex justify-end gap-3">
      <button type="submit"
        class="px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">
        Save Changes
      </button>
    </div>

  </form>
</div>
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
        // Show first 8 suggestions that aren't already selected
        this.filtered = this.suggestions.filter(s => !currentTags.includes(s.toLowerCase())).slice(0, 8);
        this.showSuggestions = this.filtered.length > 0;
      } else {
        // Filter based on input characters
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

function phoneVerify({ phone, isVerified, otpEnabled, sendUrl, verifyUrl, inputId }) {
    return {
        init() {
            window.phoneVerifyData = this;
        },
        currentPhone: phone,
        isVerified:   isVerified,
        otpEnabled:   otpEnabled,
        verifyLater:  false,
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
            this.sendingPhone = this.currentPhone; this.otp = ['','','','','','']; this.otpError = ''; this.verified = false; this._doSend();
        },
        closeModal() { this.modalOpen = false; clearInterval(this._timer); },
        editPhone()  { this.modalOpen = false; clearInterval(this._timer); this.$nextTick(() => document.getElementById(inputId)?.focus()); },
        async resendOtp() { this.otp = ['','','','','','']; this.otpError = ''; document.querySelectorAll('.otp-box').forEach(el => el.value = ''); await this._doSend(); },
        async _doSend() {
            this.sending = true;
            try {
                const r    = await fetch(sendUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ phone: this.sendingPhone }) });
                const data = await r.json();
                if (data.success || data.throttled) { this.modalOpen = true; this._startCountdown(data.retry_after || 300); }
                else alert(data.message || 'Failed to send OTP.');
            } catch { alert('Network error. Please try again.'); }
            finally { this.sending = false; }
        },
        async verifyOtp() {
            const code = this.otp.join('');
            if (code.length < 6) return;
            this.verifying = true; this.otpError = '';
            try {
                const r    = await fetch(verifyUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ phone: this.sendingPhone, code }) });
                const data = await r.json();
                if (data.success) { this.verified = true; this.isVerified = true; clearInterval(this._timer); setTimeout(() => this.modalOpen = false, 2000); }
                else this.otpError = data.message || 'Invalid OTP.';
            } catch { this.otpError = 'Network error. Please try again.'; }
            finally { this.verifying = false; }
        },
        otpInput(e, i) {
            const v = e.target.value.replace(/\D/g,'').slice(-1);
            e.target.value = v; this.otp[i] = v; this.otpError = '';
            if (v && i < 5) document.querySelectorAll('.otp-box')[i+1]?.focus();
            if (this.otp.join('').length === 6) this.verifyOtp();
        },
        otpKeydown(e, i) { if (e.key === 'Backspace' && !e.target.value && i > 0) document.querySelectorAll('.otp-box')[i-1]?.focus(); },
        otpPaste(e) {
            const txt = e.clipboardData.getData('text').replace(/\D/g,'').slice(0,6);
            const boxes = document.querySelectorAll('.otp-box');
            txt.split('').forEach((c,i) => { this.otp[i] = c; if(boxes[i]) boxes[i].value = c; });
            if (txt.length === 6) this.verifyOtp();
        },
        _startCountdown(secs) { this.countdown = secs; clearInterval(this._timer); this._timer = setInterval(() => { if(this.countdown > 0) this.countdown--; else clearInterval(this._timer); }, 1000); },
        fmtCountdown(s) { return `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`; }
    };
}
</script>
@endsection
