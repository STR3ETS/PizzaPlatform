@extends('mail.layout')

@section('mascotte', 'holding-3-pizzas')
@section('bubbel')Nog {{ max(1, (int) ceil(now()->diffInDays($user->proef_tot, false))) }} dagen gratis, {{ explode(' ', $user->name)[0] }}. Daarna is het aan jou!@endsection

@section('inhoud')
    @php
        $zaak = $user->onboarding['name'] ?? 'jouw pizzeria';
        $dagen = max(1, (int) ceil(now()->diffInDays($user->proef_tot, false)));
    @endphp
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#B97F10;">Nog {{ $dagen }} {{ $dagen === 1 ? 'dag' : 'dagen' }} in je proefperiode</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Tijd om te beslissen</h1>
    <p style="margin:0 0 12px;">
        Je proefperiode voor <b>{{ $zaak }}</b> loopt {{ $user->proef_tot?->locale('nl')->isoFormat('D MMMM') ? 'op ' . $user->proef_tot->locale('nl')->isoFormat('D MMMM') : 'binnenkort' }} af.
        Met een abonnement van <b>24,95 euro per maand</b> (opzegbaar per maand) houd je alles wat je nu hebt opgebouwd:
    </p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:2; font-weight:400;">
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Je bestelpagina op {{ $user->slug }}.mijnpizzeria.nl blijft live<br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Onbeperkt bestellingen, zonder commissie<br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Je menukaart, klanten en spaarpunten blijven bewaard<br>
            <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Vanaf dan kan ook bestellen via je eigen domein
        </td></tr>
    </table>
    <p style="margin:0 0 4px;">Starten kan met twee klikken vanuit je dashboard:</p>
    @include('mail.deel.knop', ['url' => route('dashboard'), 'label' => 'Start je abonnement'])
    <p style="margin:16px 0 0;">
        Twijfel je nog ergens over?
        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('feedback.formulier', ['user' => $user->id, 'context' => 'proef']) }}" style="color:#B04A3F; font-weight:600;">Vertel het ons via dit korte formulier</a>, we denken graag met je mee.<br>
        <b>Het team van MijnPizzeria</b>
    </p>
@endsection
