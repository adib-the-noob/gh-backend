<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register user with phone number and generate OTP
     */
    public function register(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
        ]);

        // Find or create user
        $user = User::firstOrCreate(
            ['phone_number' => $request->phone_number],
            [
                'full_name' => 'User',
                'password' => Hash::make(uniqid()),
            ]
        );

        // Delete any existing OTPs for this user
        Otp::where('user_id', $user->id)->delete();

        // Generate hardcoded OTP (123456)
        $otp = Otp::create([
            'user_id' => $user->id,
            'otp' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'message' => 'OTP sent successfully',
            'phone_number' => $request->phone_number,
            // Remove this in production
            'otp' => '123456',
        ]);
    }

    /**
     * Verify OTP and return JWT token
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $otp = Otp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('has_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 401);
        }

        // Mark OTP as used
        $otp->update(['has_used' => true]);

        // Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'OTP verified successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
}
