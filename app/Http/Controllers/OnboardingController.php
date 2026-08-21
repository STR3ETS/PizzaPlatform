<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OnboardingController extends Controller
{
    /**
     * De onboarding is tegelijk de registratie: bij afronden maken we het
     * account aan (of werken we het bij voor wie al ingelogd is).
     */
    public function finish(Request $request)
    {
        $request->validate([
            'state' => ['required', 'array'],
            'state.name' => ['required', 'string', 'min:2', 'max:100'],
            'state.person' => ['required', 'string', 'min:2', 'max:100'],
            'state.email' => ['required', 'email', 'max:255'],
            'password' => [Auth::guest() ? 'required' : 'nullable', 'string', 'min:8'],
        ], [
            'state.name.required' => 'De naam van je pizzeria ontbreekt nog.',
            'state.person.required' => 'Je eigen naam ontbreekt nog.',
            'state.email.required' => 'Je e-mailadres ontbreekt nog.',
            'state.email.email' => 'Dit e-mailadres ziet er nog niet helemaal goed uit.',
            'password.required' => 'Kies nog even een wachtwoord.',
            'password.min' => 'Je wachtwoord moet minimaal 8 tekens zijn.',
        ]);

        $state = $request->input('state');
        unset($state['returnTo'], $state['submitted'], $state['step']);

        if (Auth::check()) {
            Auth::user()->forceFill(['onboarding' => $state])->save();

            return response()->json(['ok' => true]);
        }

        if (User::where('email', $state['email'])->exists()) {
            throw ValidationException::withMessages([
                'state.email' => 'Er bestaat al een account met dit e-mailadres. Log eerst in en rond de onboarding daarna af.',
            ]);
        }

        $user = User::create([
            'name' => $state['person'],
            'email' => $state['email'],
            'password' => Hash::make($request->input('password')),
            'onboarding' => $state,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['ok' => true]);
    }
}
