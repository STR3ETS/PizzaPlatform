@extends('mail.layout')

@section('mascotte', 'writing-chalkboard')
@section('bubbel')Nieuwe zaak in de familie! {{ $user->onboarding['name'] ?? $user->name }} heeft zich net aangemeld.@endsection

@section('inhoud')
    @php $ob = $user->onboarding ?? []; @endphp
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#B97F10;">Beheer</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Nieuwe pizzeria aangemeld</h1>
    <p style="margin:0 0 12px;">
        De proefmaand loopt vanaf nu; een mooi moment om even kennis te maken en te vragen of ze hulp nodig hebben bij het instellen.
    </p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:1.9; font-weight:400;">
            <b style="font-weight:600;">Zaak:</b> {{ $ob['name'] ?? '-' }}<br>
            <b style="font-weight:600;">Contactpersoon:</b> {{ $user->name }}<br>
            <b style="font-weight:600;">E-mail:</b> {{ $user->email }}<br>
            <b style="font-weight:600;">Telefoon:</b> {{ ($ob['phone'] ?? '') !== '' ? $ob['phone'] : 'Niet ingevuld' }}<br>
            <b style="font-weight:600;">Plaats:</b> {{ ($ob['city'] ?? '') !== '' ? $ob['city'] : 'Niet ingevuld' }}<br>
            <b style="font-weight:600;">KvK:</b> {{ ($ob['kvk'] ?? '') !== '' ? $ob['kvk'] : 'Nog niet ingevuld' }}<br>
            <b style="font-weight:600;">Subdomein:</b> {{ $user->slug }}.mijnpizzeria.nl<br>
            <b style="font-weight:600;">Proefperiode tot:</b> {{ $user->proef_tot?->locale('nl')->isoFormat('D MMMM YYYY') ?? '-' }}
        </td></tr>
    </table>
    @include('mail.deel.knop', ['url' => route('admin.index'), 'label' => 'Naar het beheer'])
@endsection
