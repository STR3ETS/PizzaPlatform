<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Dit e-mailadres ziet er nog niet helemaal goed uit.',
            'password.required' => 'Vul je wachtwoord in.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Deze combinatie kennen we niet. Probeer het nog eens.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showForgot()
    {
        return view('auth.wachtwoord-vergeten');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Dit e-mailadres ziet er nog niet helemaal goed uit.',
        ]);

        Password::sendResetLink($request->only('email'));

        // Altijd hetzelfde bericht, zodat je niet kunt raden welke e-mailadressen bestaan
        return back()->with('status', 'Als dit e-mailadres bij ons bekend is, staat er nu een herstel-link in je inbox.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('auth.wachtwoord-herstellen', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Dit e-mailadres ziet er nog niet helemaal goed uit.',
            'password.required' => 'Kies een nieuw wachtwoord.',
            'password.min' => 'Minimaal 8 tekens, dan zit je goed.',
            'password.confirmed' => 'De wachtwoorden zijn niet hetzelfde.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages([
                'email' => 'Deze herstel-link is verlopen of ongeldig. Vraag een nieuwe aan.',
            ]);
        }

        return redirect()->route('login')->with('status', 'Je wachtwoord is aangepast. Log maar lekker in!');
    }
}
