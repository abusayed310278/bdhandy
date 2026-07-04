<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar','he','fa','ur']) ? 'rtl' : 'ltr' }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

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
                    bn:   ['"Hind Siliguri"','"Noto Sans Bengali"','sans-serif'],
                    ar:   ['Cairo','Tajawal','"Noto Naskh Arabic"','sans-serif']
                  },
                  boxShadow: {
                    soft: '0 4px 20px -8px rgba(15, 148, 234, 0.15)',
                    warm: '0 4px 20px -8px rgba(249, 115, 22, 0.15)'
                  }
                }
              }
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600;700&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            html[lang="bn"] body { font-family: 'Hind Siliguri', system-ui, sans-serif; line-height: 1.75; }
            html[lang="ar"] body { font-family: 'Cairo', system-ui, sans-serif; }
            html[lang="en"] body { font-family: 'Inter', system-ui, sans-serif; }
            [dir="rtl"] .rtl-flip { transform: scaleX(-1); }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-700">
        <div class="min-h-screen bg-slate-50">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-slate-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>

