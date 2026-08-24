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
    /** Snelle check bij de e-mailstap: bestaat er al een account met dit adres? */
    public function emailCheck(Request $request)
    {
        $email = strtolower(trim((string) $request->input('email')));
        $bestaat = $email !== ''
            && User::whereRaw('LOWER(email) = ?', [$email])
                ->when($request->user(), fn ($q) => $q->where('id', '!=', $request->user()->id))
                ->exists();

        return response()->json(['bestaat' => $bestaat]);
    }

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
            $this->syncMenukaart(Auth::user(), $state);

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
            // Eenmalig vastgelegd: deze slug komt straks op gedrukte QR-dozen en mag niet meer verschuiven
            'slug' => BestelController::uniekeSlug($state['name']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['ok' => true]);
    }

    /**
     * Bestaat er al een echte menukaart, dan is de menustap in de onboarding
     * daar een bewerking van: wijzigingen schrijven we terug. We syncen alleen
     * als de stap uit de echte kaart is opgebouwd (items dragen dan een id mee),
     * zodat verouderde onboarding-staat nooit een menukaart kan wissen.
     */
    private function syncMenukaart(User $user, array $state): void
    {
        $items = array_values($state['menu'] ?? []);
        if (! $user->menuItems()->exists() || ! collect($items)->contains(fn ($m) => ! empty($m['id']))) {
            return;
        }

        $cats = collect($state['categories'] ?? [])->keyBy('id');
        $bestaand = $user->menuItems()->get()->keyBy('id');
        $gezien = [];

        foreach ($items as $i => $m) {
            if (empty($m['name'])) {
                continue;
            }
            $velden = [
                'categorie' => $cats[$m['cat'] ?? '']['name'] ?? 'Menu',
                'naam' => $m['name'],
                'prijs' => (int) round(((float) str_replace(',', '.', (string) ($m['price'] ?? 0))) * 100),
                'volgorde' => $i,
            ];
            if (($m['icon']['t'] ?? '') === 'p' && ! empty($m['icon']['v'])) {
                $velden['foto'] = $m['icon']['v'];
            }

            if (! empty($m['id']) && $bestaand->has($m['id'])) {
                $bestaand[$m['id']]->update($velden);
                $gezien[] = (int) $m['id'];
            } else {
                $gezien[] = $user->menuItems()->create($velden)->id;
            }
        }

        $user->menuItems()->whereNotIn('id', $gezien)->delete();
    }
}
