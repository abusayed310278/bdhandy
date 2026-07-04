<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" class="h-full bg-white text-slate-700">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title') — {{ config('app.name') }}</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:'#F0F8FF',100:'#E0F1FE',200:'#BAE0FD',300:'#7CC8FB',
                            400:'#38ADF7',500:'#0F94EA',600:'#0277C7',700:'#0561A1'
                        },
                        accent: {
                            50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',
                            400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter','system-ui','sans-serif'],
                    },
                    boxShadow: {
                        soft: '0 4px 20px -8px rgba(15, 148, 234, 0.15)',
                        warm: '0 4px 20px -8px rgba(249, 115, 22, 0.15)'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="h-full antialiased font-sans flex flex-col items-center justify-center p-6 text-center overflow-hidden">
    <!-- Decorative background -->
    <div class="absolute inset-0 opacity-10 pointer-events-none -z-10" aria-hidden="true">
        <svg class="absolute top-10 end-10 w-64 h-64 text-primary-200" viewBox="0 0 100 100" fill="none">
            <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1" fill="currentColor"/>
            </pattern>
            <rect width="100" height="100" fill="url(#dots)"/>
        </svg>
        <div class="absolute bottom-[-10%] start-[-5%] w-[40%] h-[60%] bg-gradient-to-tr from-primary-100 to-transparent rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-md w-full animate-in fade-in zoom-in duration-500">
        <!-- Logo -->
        <a href="/" class="inline-flex items-center gap-2 mb-10 transition hover:opacity-80" dir="ltr">
            <span class="w-10 h-10 rounded-xl bg-primary-500 flex items-center justify-center text-white shadow-soft">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            </span>
            <span class="font-bold text-2xl text-slate-900 tracking-tight">{{ config('app.name') }}</span>
        </a>

        <!-- Status Code -->
        <h1 class="text-8xl sm:text-9xl font-black text-slate-100 drop-shadow-sm select-none">@yield('code')</h1>
        
        <!-- Error Message -->
        <div class="relative -mt-10 sm:-mt-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 leading-tight">@yield('message')</h2>
            <p class="mt-4 text-slate-500 text-base sm:text-lg">@yield('description')</p>
        </div>

        <!-- Buttons -->
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                {{ __('errors/layout.return_home') }}
            </a>
            <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                {{ __('errors/layout.go_back') }}
            </button>
        </div>
    </div>

    <!-- Help text -->
    <p class="mt-16 text-sm text-slate-400">
        {!! __('errors/layout.mistake_help', ['link' => '<a href="' . route('contact') . '" class="text-primary-500 font-medium hover:underline">' . __('errors/layout.contact_support') . '</a>']) !!}
    </p>
</body>
</html>
