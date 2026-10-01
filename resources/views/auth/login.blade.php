@extends('layouts.gast')

@section('title', 'Inloggen | Shop & Eat')
@section('foto', 'margherita.jpg')

@section('content')
    <div>
        <p class="label">Welkom terug</p>
        <h1 class="gast-kop">Inloggen</h1>
        <p class="lead" style="margin-top:8px">Je bestellingen, je menukaart en je cijfers, op één plek.</p>
    </div>

    @if (session('status'))
        <div class="kaart"><p class="status live"><i></i>{{ session('status') }}</p></div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" class="gast-velden">
        @csrf
        <div class="fld">
            <label for="email">E-mailadres</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                autocomplete="email" placeholder="jij@jouwzaak.nl">
            @error('email')<p class="fout">{{ $message }}</p>@enderror
        </div>
        <div class="fld">
            <label for="password">Wachtwoord</label>
            <input id="password" name="password" type="password" required
                autocomplete="current-password" placeholder="••••••••">
            @error('password')<p class="fout">{{ $message }}</p>@enderror
        </div>
        <div class="gast-rij">
            <label class="vink"><input type="checkbox" name="remember"> Ingelogd blijven</label>
            <a href="{{ route('password.request') }}" class="link-knop">Wachtwoord vergeten?</a>
        </div>
        <button type="submit" class="btn dark breed">Inloggen</button>
    </form>

    <p class="small">Nog geen account? <a href="{{ route('onboarding') }}" class="link-knop">Start de onboarding, dan regel je 'm meteen</a></p>
@endsection
