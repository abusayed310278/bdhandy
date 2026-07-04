<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProviderApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isProvider()) {
            return $next($request);
        }

        $profile = $user->providerProfile;

        if (!$profile || $profile->verification_status === 'pending') {
            return redirect()->route('provider.onboarding.profile');
        }

        if ($profile->verification_status === 'in_review') {
            return redirect()->route('provider.onboarding.pending');
        }

        if ($profile->verification_status === 'rejected') {
            return redirect()->route('provider.onboarding.documents')
                ->with('error', 'Your documents were rejected. Please review the reason and re-submit.');
        }

        return $next($request);
    }
}
