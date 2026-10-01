@extends('layouts.gast')

@section('title', 'Wachtwoord vergeten | Shop & Eat')
@section('foto', 'koken.jpg')
@section('claim')Even kwijt? <b>Zo geregeld</b>.@endsection
@section('claimsub', 'Je krijgt een link per mail om een nieuw wachtwoord te kiezen.')

@section('content')
    <div>
        <p class="label">Geen probleem</p>
        <h1 class="gast-kop">Wachtwoord vergeten</h1>
        <p class="lead" style="margin-top:8px">Vul je e-mailadres in en we sturen je een link om een nieuw wachtwoord te kiezen.</p>
    </div>

    @if (session('status'))
        <div class="kaart"><p class="status live"><i></i>{{ session('status') }}</p></div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="gast-velden">
        @csrf
        <div class="fld">
            <label for="email">E-mailadres</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                autocomplete="email" placeholder="jij@jouwzaak.nl">
            @error('email')<p class="fout">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn dark breed">Stuur herstel-link</button>
    </form>

    <p class="small">Weet je je wachtwoord weer? <a href="{{ route('login') }}" class="link-knop">Naar inloggen</a></p>
@endsection
