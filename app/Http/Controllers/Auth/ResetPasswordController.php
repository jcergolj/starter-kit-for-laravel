<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ResetPasswordController extends Controller
{
    /** Shows reset password form. */
    public function edit()
    {
        return view('auth.reset-password');
    }

    /** Handles reset password form submit. */
    public function update(Request $request, string $token)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            array_merge($request->only('email', 'password', 'password_confirmation'), ['token' => $token]),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->input('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        throw_if($status != Password::PasswordReset, ValidationException::withMessages([
            'email' => __($status),
        ]));

        InAppNotification::success(__('Password reset successfully.'));

        return to_route('login');
    }
}
