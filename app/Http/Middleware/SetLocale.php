<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supported = ['en', 'bn', 'ar', 'uz', 'ru'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);
        app()->setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Cookie (set by language switcher)
        if ($cookie = $request->cookie('app_locale')) {
            if (in_array($cookie, $this->supported)) {
                return $cookie;
            }
        }

        // 2. Authenticated user preference
        if (Auth::check()) {
            $pref = Auth::user()->preferred_language;
            if ($pref && in_array($pref, $this->supported)) {
                return $pref;
            }
        }

        // 3. Browser Accept-Language header
        $browser = substr($request->getPreferredLanguage($this->supported) ?? '', 0, 2);
        if ($browser && in_array($browser, $this->supported)) {
            return $browser;
        }

        return config('app.locale', 'en');
    }
}
