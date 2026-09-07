@extends('mail.layout')

@section('mascotte', 'scanning-qr')
@section('bubbel')Mooi moment, {{ explode(' ', $user->name)[0] }}: vanaf nu staat alles voor je open.@endsection

@section('inhoud')
    @php $zaak = $user->onboarding['name'] ?? 'jouw pizzeria'; @endphp
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#2C7A4B;">Abonnement actief</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Welkom aan boord</h1>
    <p style="margin:0 0 12px;">
        Bedankt voor je vertrouwen. Het abonnement voor <b>{{ $zaak }}</b> is vanaf nu actief.
        Daarmee is je bestelpagina definitief van jou en kun je zonder beperkingen bestellingen blijven ontvangen.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:1.9; font-weight:400;">
            <b style="font-weight:600;">Abonnement:</b> MijnPizzeria, 24,95 euro per maand<br>
            <b style="font-weight:600;">Volgende facturatie:</b> {{ now()->addMonthNoOverflow()->locale('nl')->isoFormat('D MMMM YYYY') }}<br>
            <b style="font-weight:600;">Opzeggen:</b> kan per maand, zonder gedoe
        </td></tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:2; font-weight:400;">
            <b style="font-weight:600;">Dit staat nu allemaal voor je open:</b><br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Onbeperkt bestellingen, ook na je proefmaand<br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Bestellen via je eigen domein, wij helpen met koppelen<br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Het volledige spaarprogramma voor je vaste klanten
        </td></tr>
    </table>

    @include('mail.deel.knop', ['url' => route('dashboard'), 'label' => 'Naar je dashboard'])

    <p style="margin:16px 0 0;">
        Vragen over je abonnement of de facturatie?
        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('feedback.formulier', ['user' => $user->id, 'context' => 'hulp']) }}" style="color:#B04A3F; font-weight:600;">Stel ze via dit korte formulier</a>, dan regelen we het samen.
    </p>
    <p style="margin:14px 0 0;">
        Veel succes met je zaak, wij rijden met je mee!<br>
        <b>Het team van MijnPizzeria</b>
    </p>
@endsection
