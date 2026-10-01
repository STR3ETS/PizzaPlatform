@extends('bezorger.layout')

@section('titel', 'Wachtwoord kiezen')

@section('inhoud')
    <main class="min-h-screen grid place-items-center px-5 py-10">
        <div class="w-full max-w-sm">
            @if($medewerker === null)
                {{-- Verlopen of onbekende uitnodiging --}}
                <div class="bg-white rounded-2xl border border-crema-dark shadow-[0_1px_2px_rgb(36_23_18_/_.06)] p-7 text-center">
                    <span class="inline-grid w-14 h-14 rounded-2xl bg-crema border border-crema-dark place-items-center text-2xl text-tomato mb-4"><i class="fa-solid fa-hourglass-end" aria-hidden="true"></i></span>
                    <h1 class="font-display text-2xl">Deze uitnodiging werkt niet meer</h1>
                    <p class="text-sm font-semibold text-cacao/55 mt-2">Hij is verlopen of al gebruikt. Vraag je pizzeria om een nieuwe uitnodiging, dan kun je alsnog aan de slag.</p>
                    <a href="{{ route('bezorger.login') }}" class="btn-primary btn-grey inline-block mt-6 !px-6 !py-2.5">Naar inloggen</a>
                </div>
            @else
                <div class="text-center mb-7">
                    <span class="inline-grid w-14 h-14 rounded-2xl bg-tomato text-white place-items-center text-2xl mb-4"><i class="fa-solid {{ $medewerker->isBezorger() ? 'fa-moped' : 'fa-fire-burner' }}" aria-hidden="true"></i></span>
                    <h1 class="font-display text-3xl">Welkom, {{ explode(' ', $medewerker->naam)[0] }}</h1>
                    <p class="text-sm font-semibold text-cacao/55 mt-1.5">
                        Je hoort bij het team van <b>{{ $medewerker->user->onboarding['name'] ?? 'de pizzeria' }}</b> als {{ mb_strtolower($medewerker->rolLabel()) }}.
                        Kies een wachtwoord, dan kun je meteen beginnen.
                    </p>
                </div>

                <form method="POST" action="{{ route('bezorger.activeren.opslaan') }}" class="bg-white rounded-2xl border border-crema-dark shadow-[0_1px_2px_rgb(36_23_18_/_.06)] p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div>
                        <label for="wachtwoord" class="lbl">Kies een wachtwoord</label>
                        <input id="wachtwoord" name="wachtwoord" type="password" autocomplete="new-password" required minlength="8"
                            placeholder="Minimaal 8 tekens" class="inp !text-base">
                        @error('wachtwoord')<p class="err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="wachtwoord_herhaal" class="lbl">Nog een keer</label>
                        <input id="wachtwoord_herhaal" name="wachtwoord_herhaal" type="password" autocomplete="new-password" required minlength="8"
                            placeholder="Zelfde als hierboven" class="inp !text-base">
                        @error('wachtwoord_herhaal')<p class="err">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary w-full !py-3.5 !text-lg">Aan de slag</button>
                </form>

                <p class="text-center text-xs font-semibold text-cacao/40 mt-5">
                    Je komt hiermee alleen bij de bestellingen van deze zaak.
                </p>
            @endif
        </div>
    </main>
@endsection
