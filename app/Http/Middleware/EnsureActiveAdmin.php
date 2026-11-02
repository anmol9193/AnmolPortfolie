<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $fingerprint = $request->session()->get('admin.password_fingerprint');
        if (! $user?->canAccessAdmin()
            || $request->session()->get('admin.verified_user_id') !== $user->id
            || ! is_string($fingerprint)
            || ! hash_equals(hash('sha256', $user->password), $fingerprint)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Please sign in with your password and complete email verification.']);
        }

        return $next($request);
    }
}
