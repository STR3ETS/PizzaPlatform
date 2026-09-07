@extends('mail.layout')

@section('mascotte', 'writing-chalkboard')
@section('bubbel')Er is feedback binnen van {{ $zaak->onboarding['name'] ?? $zaak->name }}.@endsection

@section('inhoud')
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#B97F10;">Beheer</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Nieuwe feedback</h1>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:1.9; font-weight:400;">
            <b style="font-weight:600;">Zaak:</b> {{ $zaak->onboarding['name'] ?? $zaak->name }}<br>
            <b style="font-weight:600;">Contact:</b> {{ $zaak->email }}{{ ($zaak->onboarding['phone'] ?? '') !== '' ? ', ' . $zaak->onboarding['phone'] : '' }}<br>
            <b style="font-weight:600;">Formulier:</b> {{ $feedback->context }}<br>
            @if($feedback->antwoord)
                <b style="font-weight:600;">Antwoord:</b> {{ $feedback->antwoord }}<br>
            @endif
            @if($feedback->toelichting)
                <b style="font-weight:600;">Toelichting:</b> {{ $feedback->toelichting }}
            @endif
        </td></tr>
    </table>
    @if($zaak->id)
        @include('mail.deel.knop', ['url' => route('admin.pizzeria', $zaak), 'label' => 'Open het dossier'])
    @endif
@endsection
