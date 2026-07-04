<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception) {
            return redirect()->route('login')->withErrors(['social' => 'Social login failed. Please try again.']);
        }

        $existing = User::where('provider', $provider)
            ->where('provider_user_id', $socialUser->getId())
            ->first();

        if ($existing) {
            Auth::login($existing, true);
            $existing->update([
                'access_token'  => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return redirect()->route($existing->getDashboardRoute());
        }

        // Check if email already registered
        $byEmail = User::where('email', $socialUser->getEmail())->first();
        if ($byEmail) {
            $byEmail->update([
                'provider'         => $provider,
                'provider_user_id' => $socialUser->getId(),
                'access_token'     => $socialUser->token,
                'refresh_token'    => $socialUser->refreshToken,
                'email_verified_at' => $byEmail->email_verified_at ?? now(),
            ]);
            Auth::login($byEmail, true);
            return redirect()->route($byEmail->getDashboardRoute());
        }

        // New user — store socialite data in session and ask for role
        $request->session()->put('social_auth', [
            'provider'         => $provider,
            'provider_user_id' => $socialUser->getId(),
            'name'             => $socialUser->getName(),
            'email'            => $socialUser->getEmail(),
            'photo'            => $socialUser->getAvatar(),
            'access_token'     => $socialUser->token,
            'refresh_token'    => $socialUser->refreshToken,
        ]);

        return redirect()->route('social.role-select');
    }

    public function roleSelectForm(Request $request)
    {
        if (!$request->session()->has('social_auth')) {
            return redirect()->route('login');
        }

        $social = $request->session()->get('social_auth');
        return view('auth.social-role-select', compact('social'));
    }

    public function roleSelectStore(Request $request): RedirectResponse
    {
        if (!$request->session()->has('social_auth')) {
            return redirect()->route('login');
        }

        $request->validate([
            'role'               => ['required', 'in:customer,freelancer,business'],
            'phone_country_code' => ['required', 'string', 'max:10'],
            'phone'              => ['required', 'string', 'max:20', 'unique:users,phone'],
        ]);

        $social = $request->session()->get('social_auth');

        $user = User::create([
            'name'               => $social['name'],
            'email'              => $social['email'],
            'photo'              => $social['photo'],
            'provider'           => $social['provider'],
            'provider_user_id'   => $social['provider_user_id'],
            'access_token'       => $social['access_token'],
            'refresh_token'      => $social['refresh_token'],
            'email_verified_at'  => now(),
            'phone'              => $request->phone,
            'phone_country_code' => $request->phone_country_code,
            'password'           => bcrypt(str()->random(32)),
            'referred_by'        => session('referral_code'),
        ]);

        $user->assignRole($request->role);

        $request->session()->forget('social_auth');
        $request->session()->forget('referral_code');

        Auth::login($user, true);

        Mail::to($user->email)->queue(new WelcomeMail($user));

        if ($request->role === 'customer') {
            return redirect()->route('customer.dashboard')
                ->with('status', 'Welcome to ServiceHub!');
        }

        return redirect()->route('provider.onboarding.profile');
    }
}
