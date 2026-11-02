<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\LoginChallenge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginOtpController extends Controller
{
    public function show(Request $request, LoginChallenge $challenge): View|RedirectResponse
    {
        $user = $challenge->pendingUser($request);
        if (! $user || $request->boolean('change')) {
            $request->session()->forget('otp');

            return redirect()->route('login');
        }

        return view('auth.login', ['loginMethod' => 'otp', 'otpEmail' => $user->email]);
    }

    public function store(Request $request, LoginChallenge $challenge): RedirectResponse
    {
        $user = $challenge->pendingUser($request);
        if (! $user) {
            $request->session()->forget('otp');

            return redirect()->route('login')->withErrors(['email' => 'Enter your email and password first.']);
        }

        return $challenge->send($request, $user);
    }

    public function verify(Request $request, LoginChallenge $challenge): RedirectResponse
    {
        $pendingUser = $challenge->pendingUser($request);
        if (! $pendingUser) {
            $request->session()->forget('otp');

            return redirect()->route('login')->withErrors(['email' => 'Your sign-in session expired. Enter your email and password again.']);
        }
        $data = $request->validate(['code' => ['required', 'string', 'regex:/\A[0-9]{6}\z/']]);
        $pendingHash = $request->session()->get('otp.challenge_hash');
        $fingerprint = $request->session()->get('otp.password_fingerprint');
        $user = DB::transaction(function () use ($pendingUser, $pendingHash, $fingerprint, $data): ?User {
            $user = User::whereKey($pendingUser->id)->lockForUpdate()->first();
            if (! $user || ! $user->canAccessAdmin() || ! $user->otp_hash || ! $user->otp_expires_at || $user->otp_expires_at->isPast() || $user->otp_attempts >= 5
                || ! is_string($pendingHash) || ! hash_equals($user->otp_hash, $pendingHash)
                || ! hash_equals(hash('sha256', $user->password), $fingerprint)) {
                return null;
            }
            if (! Hash::check($data['code'], $user->otp_hash)) {
                $user->otp_attempts++;
                if ($user->otp_attempts >= 5) {
                    $user->otp_hash = null;
                    $user->otp_expires_at = null;
                }
                $user->save();

                return null;
            }
            $user->forceFill(['otp_hash' => null, 'otp_expires_at' => null, 'otp_attempts' => 0, 'email_verified_at' => $user->email_verified_at ?? now()])->save();

            return $user;
        });
        if (! $user) {
            return back()->withErrors(['code' => 'The code is incorrect, expired or no longer valid. Request a new code if needed.']);
        }
        Auth::login($user);
        $request->session()->forget('otp');
        $request->session()->regenerate();
        $request->session()->put('admin.verified_user_id', $user->id);
        $request->session()->put('admin.password_fingerprint', hash('sha256', $user->password));

        return redirect()->intended(route('admin.dashboard'));
    }
}
