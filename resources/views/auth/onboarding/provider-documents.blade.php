<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth/onboarding/provider-documents.documents') }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:{50:'#F0F8FF',100:'#E0F1FE',200:'#BAE0FD',400:'#38ADF7',500:'#0F94EA',600:'#0277C7',700:'#0561A1'},accent:{50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',500:'#F97316',600:'#EA580C',700:'#C2410C'}}}}}</script>
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
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                <span class="text-slate-400 hidden sm:inline">Profile</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                <span class="text-slate-400 hidden sm:inline">Services</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                <span class="text-slate-400 hidden sm:inline">Area</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-primary-500 text-white flex items-center justify-center font-bold text-xs">4</span>
                <span class="font-semibold text-primary-600 hidden sm:inline">{{ __('auth/onboarding/provider-documents.documents') }}</span>
            </div>
        </div>
    </header>

    <main class="flex-1 py-8 px-4">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">{{ __('auth/onboarding/provider-documents.title') }}</h1>
                <p class="text-slate-500 text-sm mt-1">{{ __('auth/onboarding/provider-documents.subtitle') }}</p>
            </div>

            @if($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    <ul class="space-y-1 list-disc list-inside">
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('provider.onboarding.documents.store') }}"
                  enctype="multipart/form-data" class="space-y-4">
                @csrf

                @forelse($documentTypes as $docType)
                    @php
                        $existing = $uploadedDocs->get($docType->id);
                        $isRejected = $existing && $existing->verification_status === 'rejected';
                        $isApproved = $existing && $existing->verification_status === 'approved';
                    @endphp

                    <div x-data="{ fileName: '', dragging: false }"
                        class="bg-white rounded-xl border border-slate-200 p-5"
                        :class="{ 'border-red-300 bg-red-50': dragging === false && false }">

                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900">{{ $docType->name }}</h3>
                                @if($docType->instruction)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $docType->instruction }}</p>
                                @endif
                            </div>
                            @if($isApproved)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-xs font-medium border border-green-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ __('auth/onboarding/provider-documents.approved') }}
                                </span>
                            @elseif($isRejected)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-xs font-medium border border-red-200">
                                    {{ __('auth/onboarding/provider-documents.rejected_reupload') }}
                                </span>
                            @elseif($existing)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-700 text-xs font-medium border border-yellow-200">
                                    {{ __('auth/onboarding/provider-documents.under_review') }}
                                </span>
                            @endif
                        </div>

                        @if($isRejected && $existing->rejection_reason)
                            <div class="mb-3 rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-700">
                                <strong>{{ __('auth/onboarding/provider-documents.reason') }}</strong> {{ $existing->rejection_reason }}
                            </div>
                        @endif

                        @if(!$isApproved)
                            <!-- Document number -->
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-documents.document_number') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="doc_number_{{ $docType->id }}"
                                    value="{{ old('doc_number_' . $docType->id, $existing?->document_number) }}"
                                    placeholder="e.g. NID-1234567890"
                                    required
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
                            </div>

                            <!-- File upload zone -->
                            <label
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; fileName = $event.dataTransfer.files[0]?.name || ''"
                                class="flex flex-col items-center justify-center w-full rounded-xl border-2 border-dashed cursor-pointer transition py-6 px-4 text-center"
                                :class="dragging ? 'border-primary-400 bg-primary-50' : (fileName ? 'border-green-400 bg-green-50' : 'border-slate-300 hover:border-primary-300 hover:bg-primary-50/50')">
                                <input type="file" name="doc_{{ $docType->id }}" class="sr-only"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    @change="fileName = $event.target.files[0]?.name || ''"
                                    {{ !$existing || $isRejected ? 'required' : '' }}>

                                <template x-if="!fileName">
                                    <div>
                                        <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <p class="text-sm font-medium text-slate-600">
                                            @if($existing && !$isRejected)
                                                {{ __('auth/onboarding/provider-documents.replace_file') }}
                                            @else
                                                {{ __('auth/onboarding/provider-documents.click_to_upload') }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ __('auth/onboarding/provider-documents.file_requirements') }}</p>
                                        @if($existing && !$isRejected)
                                            <p class="text-xs text-green-600 mt-1 font-medium">{{ __('auth/onboarding/provider-documents.current') }} {{ basename($existing->document_file) }}</p>
                                        @endif
                                    </div>
                                </template>
                                <template x-if="fileName">
                                    <div>
                                        <svg class="w-8 h-8 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-sm font-medium text-green-700" x-text="fileName"></p>
                                        <p class="text-xs text-green-600 mt-0.5">{{ __('auth/onboarding/provider-documents.click_to_change') }}</p>
                                    </div>
                                </template>
                            </label>
                        @else
                            <div class="flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-3 py-2 text-xs text-green-700">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('auth/onboarding/provider-documents.document_approved_notice') }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-xl border border-slate-200 p-8 text-center">
                        <p class="text-slate-500 text-sm">{{ __('auth/onboarding/provider-documents.no_documents') }}</p>
                    </div>
                @endforelse

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('provider.onboarding.service-area') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        {{ __('auth/onboarding/provider-documents.back') }}
                    </a>
                    @if($documentTypes->isNotEmpty())
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 active:bg-accent-700 transition">
                            {{ __('auth/onboarding/provider-documents.submit_btn') }}
                            <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
