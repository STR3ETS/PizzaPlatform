@extends('mail.layout')

@section('mascotte', 'running-with-pizza')
@section('bubbel')Snel {{ explode(' ', $user->name)[0] }}, vandaag is de laatste dag van je proefperiode!@endsection

@section('inhoud')
    @php $zaak = $user->onboarding['name'] ?? 'jouw pizzeria'; @endphp
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#B04A3F;">Laatste dag</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Vandaag beslis je</h1>
    <p style="margin:0 0 12px;">
        Vandaag loopt de gratis proefperiode van <b>{{ $zaak }}</b> af. Zonder abonnement gaat je bestelpagina
        daarna op slot; met een abonnement van <b>24,95 euro per maand</b> blijft alles gewoon doorlopen,
        inclusief je menukaart, je klanten en hun spaarpunten. Opzeggen kan altijd per maand.
    </p>
    @include('mail.deel.knop', ['url' => route('dashboard'), 'label' => 'Start je abonnement'])
    <p style="margin:16px 0 0;">
        Nog niet klaar om te beslissen?
        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('feedback.formulier', ['user' => $user->id, 'context' => 'proef']) }}" style="color:#B04A3F; font-weight:600;">Vertel via dit korte formulier wat je tegenhoudt</a>, dan kijken we samen wat je nodig hebt.<br>
        <b>Het team van MijnPizzeria</b>
    </p>
@endsection
