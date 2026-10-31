<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\LoginChallenge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        $request->session()->forget('otp');

        return view('auth.login');
    }

    public function login(Request $request, LoginChallenge $challenge): RedirectResponse
    {
        $request->session()->forget('otp');
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);
        $email = mb_strtolower(trim($credentials['email']));
        $user = User::where('email', $email)->first();
        if (! $user?->canAccessAdmin() || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'The email address or password is incorrect.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        $request->session()->put('otp', [
            'user_id' => $user->id,
            'email' => $user->email,
            'password_fingerprint' => hash('sha256', $user->password),
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        return $challenge->send($request, $user);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
