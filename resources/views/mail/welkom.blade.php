@extends('mail.layout')

@section('mascotte', 'waving-hello')
@section('bubbel')Ciao {{ explode(' ', $user->name)[0] }}! Wat leuk dat je er bent. Ik loop even met je mee tot je zaak online staat.@endsection

@section('inhoud')
    @php $zaak = $user->onboarding['name'] ?? 'jouw pizzeria'; @endphp
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#2C7A4B;">Welkom bij MijnPizzeria</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Je proefmaand is gestart</h1>
    <p style="margin:0 0 12px;">
        Het account voor <b>{{ $zaak }}</b> staat klaar en je proefperiode van <b>30 dagen gratis</b> is ingegaan.
        Je kunt dus alles rustig uitproberen{{ $user->proef_tot ? ', tot en met ' . $user->proef_tot->locale('nl')->isoFormat('D MMMM') : '' }}.
        Daarna kost het abonnement 24,95 euro per maand, opzegbaar per maand. Geen kleine lettertjes, geen commissie per bestelling.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:2; font-weight:400;">
            <b style="font-weight:600;">Dit krijg je allemaal:</b><br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Je eigen bestelpagina op {{ $user->slug }}.{{ config('app.centraal_domein') }}<br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Onbeperkt bestellingen ontvangen, zonder commissie<br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Een dashboard met je bestellingen, menukaart en live status<br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Een spaarprogramma dat klanten laat terugkomen
        </td></tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:1.9; font-weight:400;">
            <b style="font-weight:600;">Zo ga je live, in een kwartiertje:</b><br>
            <b style="font-weight:600; color:#B04A3F;">1.</b> Log in op je dashboard<br>
            <b style="font-weight:600; color:#B04A3F;">2.</b> Vul stap voor stap je openingstijden, menukaart, spaarpunten, bestel-adres en huisstijl aan<br>
            <b style="font-weight:600; color:#B04A3F;">3.</b> Klik op "Online gaan" en ontvang je eerste bestelling
        </td></tr>
    </table>

    @include('mail.deel.knop', ['url' => route('dashboard'), 'label' => 'Naar je dashboard'])

    <p style="margin:16px 0 0;">
        Loop je ergens tegenaan of wil je gewoon even sparren over je menukaart?
        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('feedback.formulier', ['user' => $user->id, 'context' => 'hulp']) }}" style="color:#B04A3F; font-weight:600;">Stel je vraag via dit korte formulier</a> en we denken met je mee.
    </p>
    <p style="margin:14px 0 0;">
        Veel succes met je zaak, wij rijden met je mee!<br>
        <b>Het team van MijnPizzeria</b>
    </p>
@endsection
