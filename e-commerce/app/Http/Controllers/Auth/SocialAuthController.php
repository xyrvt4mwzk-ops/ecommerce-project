<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->with('error', 'Google sign-in is not configured.');
        }

        $intent = $request->string('intent')->value();
        if (! in_array($intent, ['login', 'register'], true)) {
            $intent = 'login';
        }

        $request->session()->put('google_auth_intent', $intent);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->with('error', 'Google sign-in is not configured.');
        }

        try {
            // Keep Socialite stateful on both sides of the OAuth exchange. Using
            // stateless() only on the callback discards the state created above.
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('login')->with(
                'error',
                'Google sign-in could not be completed. Ensure the Google Console redirect URI exactly matches this site URL.'
            );
        }
        $email = $googleUser->getEmail();
        $name = $googleUser->getName() ?? $googleUser->getNickname() ?? $email;

        if (! $email) {
            return redirect()->route('login')->with('error', 'Google did not return an email address for this account.');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = new User([
                'email' => $email,
            ]);
            $user->password = Hash::make(Str::random(24));
        }

        $user->name = $name;
        $user->email_verified_at = now();
        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        return $user->roles()->exists()
            ? redirect()->intended(route('dashboard', absolute: false))
            : redirect()->route('role.select');
    }
}
