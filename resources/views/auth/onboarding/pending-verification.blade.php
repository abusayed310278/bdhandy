<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ __('auth/onboarding/pending-verification.verification') }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:{50:'#F0F8FF',100:'#E0F1FE',200:'#BAE0FD',400:'#38ADF7',500:'#0F94EA',600:'#0277C7',700:'#0561A1'},accent:{50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',500:'#F97316',600:'#EA580C',700:'#C2410C'}}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>html,body{font-family:'Inter',system-ui,sans-serif;}</style>
</head>
<body class="min-h-full text-slate-700 antialiased">
<div class="min-h-screen flex flex-col">

    <header class="bg-white border-b border-slate-200">
        <div class="max-w-2xl mx-auto px-4 h-14 flex items-center">
            <a href="/" class="flex items-center gap-2" dir="ltr">
                <span class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </span>
                <span class="font-bold text-slate-900">{{ config('app.name') }}</span>
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center py-12 px-4">
        <div class="max-w-lg w-full space-y-4">

            @if(session('status'))
                <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @php
                $status       = $profile?->verification_status ?? 'pending';
                $allDocs      = $profile?->documents ?? collect();
                $rejectedDocs = $allDocs->where('verification_status', 'rejected');
                $hasRejected  = $rejectedDocs->isNotEmpty();
            @endphp

            {{-- Main status card --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">

                @if($status === 'approved')
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-green-50 text-green-500 flex items-center justify-center mx-auto mb-4 text-3xl">✅</div>
                        <h1 class="text-xl font-bold text-slate-900 mb-2">{{ __('auth/onboarding/pending-verification.profile_approved') }}</h1>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('auth/onboarding/pending-verification.profile_approved_desc') }}
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('provider.dashboard') }}"
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">
                                {{ __('auth/onboarding/pending-verification.go_to_dashboard') }}
                                <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                @elseif($status === 'rejected')
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-4 text-3xl">❌</div>
                        <h1 class="text-xl font-bold text-slate-900 mb-2">{{ __('auth/onboarding/pending-verification.verification_failed') }}</h1>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('auth/onboarding/pending-verification.verification_failed_desc') }}
                        </p>
                    </div>

                @elseif($status === 'in_review' && $hasRejected)
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mx-auto mb-4 text-3xl">⚠️</div>
                        <h1 class="text-xl font-bold text-slate-900 mb-2">{{ __('auth/onboarding/pending-verification.action_required') }}</h1>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('auth/onboarding/pending-verification.action_required_desc') }}
                        </p>
                    </div>

                @elseif($status === 'in_review')
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center mx-auto mb-4 text-3xl">⏳</div>
                        <h1 class="text-xl font-bold text-slate-900 mb-2">{{ __('auth/onboarding/pending-verification.under_review') }}</h1>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('auth/onboarding/pending-verification.under_review_desc') }}
                        </p>
                        <div class="mt-5 rounded-xl bg-primary-50 border border-primary-100 p-4 text-start">
                            <p class="text-xs text-primary-700 font-semibold mb-2">{{ __('auth/onboarding/pending-verification.typical_timeline') }}</p>
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 text-xs text-slate-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500 shrink-0"></span>
                                    {{ __('auth/onboarding/pending-verification.timeline_received') }}
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                                    {{ __('auth/onboarding/pending-verification.timeline_review') }}
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                                    {{ __('auth/onboarding/pending-verification.timeline_decision', ['email' => auth()->user()->email]) }}
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-primary-50 text-primary-500 flex items-center justify-center mx-auto mb-4 text-3xl">📋</div>
                        <h1 class="text-xl font-bold text-slate-900 mb-2">{{ __('auth/onboarding/pending-verification.complete_setup') }}</h1>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ __('auth/onboarding/pending-verification.complete_setup_desc') }}
                        </p>
                    </div>
                @endif

                {{-- Rejected documents list (shown whenever there are rejections) --}}
                @if($hasRejected)
                    <div class="mt-5 rounded-xl bg-red-50 border border-red-200 p-4 space-y-3">
                        <p class="text-xs font-semibold text-red-700">{{ __('auth/onboarding/pending-verification.require_reupload') }}</p>
                        @foreach($rejectedDocs as $doc)
                            <div class="flex items-start gap-2.5">
                                <svg class="w-3.5 h-3.5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800">{{ $doc->documentType->name ?? 'Document' }}</p>
                                    @if($doc->rejection_reason)
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $doc->rejection_reason }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Actions --}}
                <div class="mt-6 flex flex-col items-center gap-3">
                    @if($hasRejected || $status === 'rejected' || $status === 'pending')
                        <a href="{{ route('provider.onboarding.documents') }}"
                           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-accent-500 text-white text-sm font-semibold hover:bg-accent-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            {{ __('auth/onboarding/pending-verification.reupload_btn') }}
                        </a>
                    @endif

                    <p class="text-xs text-slate-400">
                        {{ __('auth/onboarding/pending-verification.questions') }} <a href="/contact" class="text-primary-600 hover:underline">{{ __('auth/onboarding/pending-verification.contact_support') }}</a>
                        · {{ __('auth/onboarding/pending-verification.account_id') }} <strong class="font-mono">{{ auth()->user()->user_code }}</strong>
                    </p>
                </div>
            </div>

            {{-- Document status list --}}
            @if($allDocs->isNotEmpty())
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">{{ __('auth/onboarding/pending-verification.document_status') }}</h3>
                    <div class="space-y-2.5">
                        @foreach($allDocs as $doc)
                            @php
                                $vs    = $doc->verification_status;
                                $color = match($vs) {
                                    'approved' => 'bg-green-50 text-green-700 border-green-200',
                                    'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                    default    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                };
                            @endphp
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-700">{{ $doc->documentType->name ?? 'Document' }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                                        {{ ucfirst($vs) }}
                                    </span>
                                </div>
                                @if($vs === 'rejected' && $doc->rejection_reason)
                                    <p class="text-xs text-red-500 mt-0.5 ps-0">{{ $doc->rejection_reason }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 underline underline-offset-2">{{ __('auth/onboarding/pending-verification.sign_out') }}</button>
                </form>
            </div>

        </div>
    </main>
</div>
</body>
</html>
