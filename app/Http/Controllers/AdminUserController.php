<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users', ['admins' => User::orderBy('id')->paginate(20)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => is_string($request->input('email')) ? mb_strtolower(trim($request->input('email'))) : $request->input('email')]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);
        $user = new User(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']]);
        $user->role = 'admin';
        $user->is_active = true;
        $user->save();

        return redirect()->route('admin.users')->with('status', 'Admin added. Sign-in requires their password followed by email verification.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin() || $user->is($request->user()), 403);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $user->is_active = (bool) $data['is_active'];
        $user->remember_token = null;
        $user->otp_hash = null;
        $user->otp_expires_at = null;
        $user->otp_attempts = 0;
        $user->save();

        return redirect()->route('admin.users')->with('status', $user->is_active ? 'Admin activated.' : 'Admin deactivated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin() || $user->is($request->user()), 403);
        $user->delete();

        return redirect()->route('admin.users')->with('status', 'Admin removed.');
    }
}
