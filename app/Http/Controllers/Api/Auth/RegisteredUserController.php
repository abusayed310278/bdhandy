<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtpMail;
use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;

class RegisteredUserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $role = $request->input('role', 'customer');

        if (!in_array($role, ['customer', 'freelancer', 'business'])) {
            return response()->json(['message' => 'Invalid role'], 422);
        }

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
            'referred_by'        => $request->input('referral_code'),
        ]);

        $user->assignRole($role);

        event(new Registered($user));

        $otp = (string) rand(1000, 9999);
        OtpCode::create([
            'user_id' => $user->id,
            'phone' => $user->phone,
            'code' => $otp,
            'type' => 'registration',
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->queue(new RegistrationOtpMail($user, $otp));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Account created! Please verify your email.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }
}
