@extends('layouts.gast')

@section('title', 'Wachtwoord vergeten 🍕')

@section('content')
    <div class="text-center mb-6">
        <img src="{{ asset('stickers/behind-laptop.png') }}" alt="" class="h-28 w-auto mx-auto mb-3 select-none pointer-events-none">
        <p class="step-kicker">Kan de beste overkomen 🤷</p>
        <h1 class="step-title">Wachtwoord vergeten</h1>
        <p class="step-sub !mb-0">Vul je e-mailadres in en we sturen je een link om een nieuw wachtwoord te kiezen.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border-4 border-basil/30 bg-basil/10 text-basil font-extrabold text-sm px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border-4 border-crema-dark shadow-[0_8px_0_0_var(--color-crema-dark)] p-6 sm:p-8">
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="lbl">E-mailadres</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    autocomplete="email" placeholder="mario@pizzeriamario.nl" class="inp !text-base">
                @error('email')<p class="err">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-primary w-full !px-4">Stuur herstel-link</button>
        </form>
    </div>

    <p class="text-center mt-5 text-sm font-extrabold text-cacao/50">
        Toch weer binnengeschoten? <a href="{{ route('login') }}" class="text-tomato hover:underline">Naar inloggen</a>
    </p>
@endsection
