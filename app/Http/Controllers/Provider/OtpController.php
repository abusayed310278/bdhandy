<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    /**
     * Send OTP via Twilio to the given phone number.
     */
    public function send(Request $request): JsonResponse
    {
        if (!Setting::get('otp_verification_enabled', '0')) {
            return response()->json(['success' => false, 'message' => 'OTP verification is not enabled.'], 422);
        }

        $request->validate(['phone' => ['required', 'string', 'max:30']]);

        $user  = Auth::user();
        $phone = trim($request->phone);

        // Throttle: 1 OTP per 5 minutes
        $recent = OtpCode::where('user_id', $user->id)
            ->where('type', 'phone_verify')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->orderBy('created_at', 'desc')
            ->first();

        if ($recent) {
            $retryAfter = (int) now()->diffInSeconds($recent->created_at->addMinutes(5), false);
            if ($retryAfter > 0) {
                return response()->json([
                    'success'     => false,
                    'throttled'   => true,
                    'message'     => 'Please wait before requesting another OTP.',
                    'retry_after' => $retryAfter,
                ], 429);
            }
        }

        // Invalidate older pending codes
        OtpCode::where('user_id', $user->id)
            ->where('type', 'phone_verify')
            ->whereNull('verified_at')
            ->delete();

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id'    => $user->id,
            'phone'      => $phone,
            'code'       => $code,
            'type'       => 'phone_verify',
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send via Twilio
        try {
            $twilio = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            $twilio->messages->create($phone, [
                'from' => config('services.twilio.from'),
                'body' => 'Your ' . config('app.name') . ' verification code is: ' . $code . '. Valid for 10 minutes. Do not share this code.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS. Please check the phone number and try again.',
            ], 500);
        }

        return response()->json([
            'success'     => true,
            'message'     => 'OTP sent to ' . $phone,
            'retry_after' => 300,
        ]);
    }

    /**
     * Verify the OTP submitted by the user.
     */
    public function verify(Request $request): JsonResponse
    {
        if (!Setting::get('otp_verification_enabled', '0')) {
            return response()->json(['success' => false, 'message' => 'OTP verification is not enabled.'], 422);
        }

        $request->validate([
            'phone' => ['required', 'string'],
            'code'  => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        $otp = OtpCode::where('user_id', $user->id)
            ->where('phone', $request->phone)
            ->where('type', 'phone_verify')
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or not found. Please request a new code.',
            ], 422);
        }

        if ($otp->code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code. Please try again.',
            ], 422);
        }

        $otp->update(['verified_at' => now()]);

        $user->update([
            'phone'             => $request->phone,
            'phone_verified_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Phone number verified successfully!']);
    }
}
