<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\OtpCode;
use App\Mail\ResetPasswordOtpMail;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We can\'t find a user with that email address.'],
            ]);
        }

        // Delete existing OTPs for this user
        OtpCode::where('user_id', $user->id)->where('type', 'password_reset')->delete();

        $otp = (string) rand(1000, 9999);
        OtpCode::create([
            'user_id' => $user->id,
            'phone' => $user->phone ?? '0000000000',
            'code' => $otp,
            'type' => 'password_reset',
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->queue(new ResetPasswordOtpMail($user, $otp));

        return response()->json([
            'success' => true,
            'message' => 'Password reset OTP sent to your email.',
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'], // Frontend sends OTP as token
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We can\'t find a user with that email address.'],
            ]);
        }

        $otpRecord = OtpCode::where('user_id', $user->id)
            ->where('code', $request->token)
            ->where('type', 'password_reset')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired OTP code.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        event(new PasswordReset($user));

        // Delete the OTP after successful reset
        $otpRecord->delete();

        return response()->json([
            'message' => 'Your password has been reset successfully.',
        ]);
    }
}
