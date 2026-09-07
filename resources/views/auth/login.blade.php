@extends('layouts.gast')

@section('title', 'Inloggen')

@section('content')
    <div class="text-center mb-6">
        <p class="step-kicker">Welkom terug</p>
        <h1 class="step-title">Inloggen</h1>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-basil/30 bg-basil/10 text-basil font-semibold text-sm px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-crema-dark shadow-[0_1px_2px_rgb(36_23_18_/_.1)] p-6 sm:p-8">
        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="lbl">E-mailadres</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    autocomplete="email" placeholder="mario@pizzeriamario.nl" class="inp !text-base">
                @error('email')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="lbl">Wachtwoord</label>
                <input id="password" name="password" type="password" required
                    autocomplete="current-password" placeholder="••••••••" class="inp !text-base">
                @error('password')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center justify-between gap-3">
                <label class="flex items-center gap-2 text-sm font-semibold text-cacao/60 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 accent-[#B04A3F]">
                    Ingelogd blijven
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-tomato hover:underline">Wachtwoord vergeten?</a>
            </div>
            <button type="submit" class="btn-primary w-full !px-4">Inloggen</button>
        </form>
    </div>

    <p class="text-center mt-5 text-sm font-semibold text-cacao/50">
        Nog geen account? <a href="{{ route('onboarding') }}" class="text-tomato hover:underline">Start de onboarding, dan regel je 'm meteen</a>
    </p>
@endsection
