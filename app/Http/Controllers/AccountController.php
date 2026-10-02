<?php

namespace App\Http\Controllers;

use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Update the admin's name, login email and profile picture.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [], ['avatar' => 'profile picture']);

        unset($data['avatar']);

        if ($request->hasFile('avatar')) {
            Uploads::delete($user->avatar);
            $data['avatar'] = Uploads::store($request->file('avatar'), 'avatar');
        } elseif ($request->boolean('remove_avatar')) {
            Uploads::delete($user->avatar);
            $data['avatar'] = null;
        }

        $user->update($data);

        return redirect()->to(route('admin.account').'#profile')->with('status', 'Profile saved.');
    }

    /**
     * Change the admin's password (the current one is required).
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8), 'different:current_password'],
        ], [
            'current_password.current_password' => 'The current password is not correct.',
            'password.different' => 'The new password must be different from the current one.',
        ], ['password' => 'new password']);

        $request->user()->update(['password' => $data['password']]);

        return redirect()->to(route('admin.account').'#password')->with('status', 'Password changed.');
    }
}
