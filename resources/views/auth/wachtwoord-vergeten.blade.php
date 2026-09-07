@extends('layouts.gast')

@section('title', 'Wachtwoord vergeten')

@section('content')
    <div class="text-center mb-6">
        <p class="step-kicker">Geen probleem</p>
        <h1 class="step-title">Wachtwoord vergeten</h1>
        <p class="step-sub !mb-0">Vul je e-mailadres in en we sturen je een link om een nieuw wachtwoord te kiezen.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-basil/30 bg-basil/10 text-basil font-semibold text-sm px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-crema-dark shadow-[0_1px_2px_rgb(36_23_18_/_.1)] p-6 sm:p-8">
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

    <p class="text-center mt-5 text-sm font-semibold text-cacao/50">
        Weet je je wachtwoord weer? <a href="{{ route('login') }}" class="text-tomato hover:underline">Naar inloggen</a>
    </p>
@endsection
