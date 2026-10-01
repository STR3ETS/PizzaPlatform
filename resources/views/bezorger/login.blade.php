@extends('bezorger.layout')

@section('titel', 'Inloggen')

@section('inhoud')
    <main class="min-h-screen grid place-items-center px-5 py-10">
        <div class="w-full max-w-sm">
            <div class="text-center mb-7">
                <span class="inline-grid w-14 h-14 rounded-2xl bg-tomato text-white place-items-center text-2xl mb-4"><i class="fa-solid fa-moped" aria-hidden="true"></i></span>
                <h1 class="font-display text-3xl">Teamscherm</h1>
                <p class="text-sm font-semibold text-cacao/55 mt-1.5">Log in met het e-mailadres waarop je de uitnodiging kreeg.</p>
            </div>

            <form method="POST" action="{{ route('bezorger.login.attempt') }}" class="bg-white rounded-2xl border border-crema-dark shadow-[0_1px_2px_rgb(36_23_18_/_.06)] p-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="lbl">E-mailadres</label>
                    <input id="email" name="email" type="email" inputmode="email" autocomplete="email" required autofocus
                        value="{{ old('email') }}" placeholder="jij@voorbeeld.nl" class="inp !text-base">
                    @error('email')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="wachtwoord" class="lbl">Wachtwoord</label>
                    <input id="wachtwoord" name="wachtwoord" type="password" autocomplete="current-password" required
                        placeholder="Je wachtwoord" class="inp !text-base">
                    @error('wachtwoord')<p class="err">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary w-full !py-3.5 !text-lg">Inloggen</button>
            </form>

            <p class="text-center text-xs font-semibold text-cacao/40 mt-5">
                Nog geen wachtwoord? Gebruik de link uit je uitnodigingsmail.<br>
                Kwijt? Vraag je pizzeria om een nieuwe uitnodiging.
            </p>
        </div>
    </main>
@endsection
