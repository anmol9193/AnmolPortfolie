<?php

namespace App\Support;

use App\Mail\LoginOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class LoginChallenge
{
    public function pendingUser(Request $request): ?User
    {
        $pending = $request->session()->get('otp', []);
        if (! is_int($pending['user_id'] ?? null) || ($pending['expires_at'] ?? 0) <= now()->timestamp || ! is_string($pending['password_fingerprint'] ?? null)) {
            return null;
        }
        $user = User::find($pending['user_id']);

        return $user?->canAccessAdmin() && hash_equals($pending['password_fingerprint'], hash('sha256', $user->password)) ? $user : null;
    }

    public function send(Request $request, User $user): RedirectResponse
    {
        $identity = hash('sha256', mb_strtolower($user->email));
        $minuteKey = 'login-otp-minute:'.$identity;
        $hourKey = 'login-otp-hour:'.$identity;
        if (RateLimiter::tooManyAttempts($minuteKey, 1) || RateLimiter::tooManyAttempts($hourKey, 5)) {
            return redirect()->route('login.otp')->withErrors(['code' => 'Please wait before requesting another code. You can request one per minute and five per hour.']);
        }
        RateLimiter::hit($minuteKey, 60);
        RateLimiter::hit($hourKey, 3600);
        if (! app()->runningUnitTests() && in_array(config('mail.default'), ['log', 'array'], true)) {
            $request->session()->forget('otp');

            return redirect()->route('login')->withErrors(['email' => 'Email delivery is not configured. Please contact the site owner.']);
        }
        $code = (string) random_int(100000, 999999);
        $hash = Hash::make($code);
        $user->forceFill(['otp_hash' => $hash, 'otp_expires_at' => now()->addMinutes(5), 'otp_attempts' => 0])->save();
        try {
            Mail::to($user->email)->send(new LoginOtp($code));
        } catch (Throwable) {
            User::whereKey($user->id)->where('otp_hash', $hash)->update(['otp_hash' => null, 'otp_expires_at' => null, 'otp_attempts' => 0]);
            $request->session()->forget('otp');

            return redirect()->route('login')->withErrors(['email' => 'Could not send the verification code. Please try again later.']);
        }
        $request->session()->put('otp.challenge_hash', $hash);

        return redirect()->route('login.otp')->with('status', 'Password checked. Enter the 6-digit code sent to your email to finish signing in. The code expires in 5 minutes.');
    }
}
