<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->query('ref');

        if ($code && !auth()->check() && !session()->has('referral_code')) {
            session(['referral_code' => strtoupper(trim($code))]);
        }

        return $next($request);
    }
}
