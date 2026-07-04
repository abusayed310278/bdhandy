<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Provider who hasn't completed onboarding
        if ($user->isProvider() && !$user->providerProfile) {
            return redirect()->route('provider.onboarding.profile');
        }

        if ($user->isProvider() && $user->providerProfile?->verification_status === 'pending') {
            return redirect()->route('provider.onboarding.documents');
        }

        if ($user->isProvider() && $user->providerProfile?->verification_status === 'in_review') {
            return redirect()->route('provider.onboarding.pending');
        }

        return redirect()->intended(route($user->getDashboardRoute(), absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
