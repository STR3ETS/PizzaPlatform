@extends('mail.layout')

@section('mascotte', 'sprinkling-cheese')
@section('bubbel')Psst {{ explode(' ', $user->name)[0] }}, je pizzeria staat hier nog steeds klaar.@endsection

@section('inhoud')
    @php $zaak = $user->onboarding['name'] ?? 'jouw pizzeria'; @endphp
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#B04A3F;">We missen je</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Alles staat er nog</h1>
    <p style="margin:0 0 12px;">
        Je proefperiode voor <b>{{ $zaak }}</b> is een paar dagen geleden afgelopen en je hebt nog geen abonnement gestart.
        Geen probleem, maar we zijn wel benieuwd: <b>wat hield je tegen?</b>
    </p>
    <p style="margin:0 0 12px;">
        Miste je een functie, was iets onduidelijk, of kwam het er gewoon nog niet van?
        Vertel het ons via het formulier hieronder; het kost een halve minuut en grote kans dat we het kunnen oplossen.
        Je pagina, menukaart en instellingen hebben we gewoon voor je bewaard.
    </p>
    @include('mail.deel.knop', ['url' => \Illuminate\Support\Facades\URL::signedRoute('feedback.formulier', ['user' => $user->id, 'context' => 'winback']), 'label' => 'Vertel wat er miste'])
    <p style="margin:8px 0 0; font-size:13px;">
        Of <a href="{{ route('dashboard') }}" style="color:#B04A3F; font-weight:600;">kijk gewoon nog eens rond</a> in je dashboard.
    </p>
    <p style="margin:16px 0 0;">
        We horen graag van je.<br>
        <b>Het team van MijnPizzeria</b>
    </p>
@endsection
