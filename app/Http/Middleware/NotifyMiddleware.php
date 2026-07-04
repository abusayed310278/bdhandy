<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotifyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('success')) {
            notify()->success()->message(session('success'))->send();
        }

        if (session()->has('status')) {
            notify()->success()->message(session('status'))->send();
        }

        if (session()->has('error')) {
            notify()->error()->message(session('error'))->send();
        }

        if (session()->has('info')) {
            notify()->info()->message(session('info'))->send();
        }

        if (session()->has('warning')) {
            notify()->warning()->message(session('warning'))->send();
        }

        return $next($request);
    }
}
