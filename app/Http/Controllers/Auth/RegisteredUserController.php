<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'customer');

        abort_unless(in_array($role, ['customer', 'freelancer', 'business']), 422);

        $request->validate([
            'role'               => ['required', 'in:customer,freelancer,business'],
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone_country_code' => ['required', 'string', 'max:10'],
            'phone'              => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password'           => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'phone_country_code' => $request->phone_country_code,
            'password'           => Hash::make($request->password),
            'referred_by'        => session('referral_code'),
        ]);

        session()->forget('referral_code');

        $user->assignRole($role);

        event(new Registered($user));

        Auth::login($user);

        Mail::to($user->email)->queue(new WelcomeMail($user));

        if ($role === 'customer') {
            return redirect()->route('verification.notice')
                ->with('status', 'Account created! Please verify your email.');
        }

        return redirect()->route('verification.notice')
            ->with('status', 'Account created! Please verify your email to continue.');
    }
}
