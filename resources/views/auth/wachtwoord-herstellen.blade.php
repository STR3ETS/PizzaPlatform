@extends('layouts.gast')

@section('title', 'Nieuw wachtwoord | Shop & Eat')
@section('foto', 'oven.jpg')
@section('claim')Nieuw wachtwoord, <b>en door</b>.@endsection
@section('claimsub', 'Minimaal 8 tekens. Daarna log je meteen weer in.')

@section('content')
    <div>
        <p class="label">Account herstellen</p>
        <h1 class="gast-kop">Nieuw wachtwoord</h1>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="gast-velden">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="fld">
            <label for="email">E-mailadres</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="email">
            @error('email')<p class="fout">{{ $message }}</p>@enderror
        </div>
        <div class="fld">
            <label for="password">Nieuw wachtwoord</label>
            <input id="password" name="password" type="password" required autofocus
                autocomplete="new-password" placeholder="Minimaal 8 tekens">
            @error('password')<p class="fout">{{ $message }}</p>@enderror
        </div>
        <div class="fld">
            <label for="password_confirmation">Herhaal wachtwoord</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                autocomplete="new-password" placeholder="Zelfde als hierboven">
        </div>
        <button type="submit" class="btn dark breed">Wachtwoord opslaan</button>
    </form>
@endsection
