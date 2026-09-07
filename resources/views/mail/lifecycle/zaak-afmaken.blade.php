@extends('mail.layout')

@section('mascotte', 'kneading-dough')
@section('bubbel')Het deeg ligt klaar, {{ explode(' ', $user->name)[0] }}! Nog een paar stappen en je zaak staat online.@endsection

@section('inhoud')
    @php $zaak = $user->onboarding['name'] ?? 'jouw pizzeria'; @endphp
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#B04A3F;">Nog even doorpakken</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Je zaak wacht op je</h1>
    <p style="margin:0 0 12px;">
        Je hebt <b>{{ $zaak }}</b> een paar dagen geleden aangemeld, maar je bestelpagina staat nog niet live.
        Zonde, want het meeste werk is al gedaan en de rest is in een kwartiertje geregeld.
    </p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:1.9; font-weight:400;">
            <b style="font-weight:600;">Dit staat er nog open:</b><br>
            <b style="font-weight:600; color:#B04A3F;">1.</b> Openingstijden en menukaart invullen<br>
            <b style="font-weight:600; color:#B04A3F;">2.</b> Je huisstijl en bestel-adres kiezen<br>
            <b style="font-weight:600; color:#B04A3F;">3.</b> Op "Online gaan" klikken, klaar
        </td></tr>
    </table>
    <p style="margin:0 0 4px;">
        In je dashboard staat een banner "Verder met instellen" die je er stap voor stap doorheen loodst.
    </p>
    @include('mail.deel.knop', ['url' => route('dashboard'), 'label' => 'Maak je zaak af'])
    <p style="margin:16px 0 0;">
        Kom je ergens niet uit? Beantwoord deze mail en we helpen je er persoonlijk doorheen.<br>
        <b>Het team van MijnPizzeria</b>
    </p>
@endsection
