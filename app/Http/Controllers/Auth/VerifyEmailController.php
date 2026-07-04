<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // Route to role-specific onboarding after first verification
        if ($user->isProvider()) {
            return redirect()->route('provider.onboarding.profile')
                ->with('status', 'Email verified! Please complete your provider profile.');
        }

        if ($user->isCustomer()) {
            return redirect()->route('customer.onboarding.profile')
                ->with('status', 'Email verified! Please complete your profile.');
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
