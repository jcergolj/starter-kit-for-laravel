<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\ValueObjects\HourlyRate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    /** Shows setup wizard if not complete, otherwise redirects to login. */
    public function create()
    {
        if ($this->isSetupComplete()) {
            return Redirect::route('login');
        }

        return view('auth.register');
    }

    /** Handles setup wizard form submit. */
    public function store(Request $request)
    {
        if ($this->isSetupComplete()) {
            return Redirect::route('login');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'default_currency' => ['required', 'string', 'size:3'],
            'default_hourly_rate' => ['required', 'numeric', 'min:0'],
        ]);

        $user = User::query()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        event(new Registered($user));

        $defaultHourlyRate = HourlyRate::make(
            (float) $request->input('default_hourly_rate'),
            strtoupper($request->input('default_currency'))
        );

        Setting::set('default_currency', strtoupper($request->input('default_currency')));
        Setting::set('default_hourly_rate', json_encode($defaultHourlyRate->toArray()));
        Setting::set('setup_complete', true);

        return Redirect::route('login')->with('success', 'Setup completed successfully! You can now login.');
    }

    private function isSetupComplete(): bool
    {
        return (bool) Setting::get('setup_complete', false) && User::query()->exists();
    }
}
