@extends('layouts.gast')

@section('title', 'Nieuw wachtwoord 🍕')

@section('content')
    <div class="text-center mb-6">
        <img src="{{ asset('stickers/kneading-dough.png') }}" alt="" class="h-28 w-auto mx-auto mb-3 select-none pointer-events-none">
        <p class="step-kicker">Vers deeg, verse start 💪</p>
        <h1 class="step-title">Nieuw wachtwoord</h1>
    </div>

    <div class="bg-white rounded-3xl border-4 border-crema-dark shadow-[0_8px_0_0_var(--color-crema-dark)] p-6 sm:p-8">
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label for="email" class="lbl">E-mailadres</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required
                    autocomplete="email" class="inp !text-base">
                @error('email')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="lbl">Nieuw wachtwoord</label>
                <input id="password" name="password" type="password" required autofocus
                    autocomplete="new-password" placeholder="Minimaal 8 tekens" class="inp !text-base">
                @error('password')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="lbl">Nog een keer</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    autocomplete="new-password" placeholder="Zelfde als hierboven" class="inp !text-base">
            </div>
            <button type="submit" class="btn-primary w-full !px-4">Wachtwoord opslaan</button>
        </form>
    </div>
@endsection
