@extends('mail.layout')

@section('mascotte', 'writing-chalkboard')
@section('bubbel')Goedemorgen! Dit vraagt vandaag om je aandacht.@endsection

@section('inhoud')
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#B97F10;">Beheer</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Je dag in het kort</h1>

    @if(count($digest['aanmeldingen']))
        <p style="margin:0 0 6px; font-weight:600;">Nieuwe aanmeldingen (laatste 24 uur):</p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
            <tr><td style="padding:14px 20px; font-size:13px; line-height:1.9; font-weight:400;">
                @foreach($digest['aanmeldingen'] as $regel){{ $regel }}<br>@endforeach
            </td></tr>
        </table>
    @endif

    @if(count($digest['proefLooptAf']))
        <p style="margin:0 0 6px; font-weight:600;">Proef loopt binnen 7 dagen af:</p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
            <tr><td style="padding:14px 20px; font-size:13px; line-height:1.9; font-weight:400;">
                @foreach($digest['proefLooptAf'] as $regel){{ $regel }}<br>@endforeach
            </td></tr>
        </table>
    @endif

    @if(count($digest['netVerlopen']))
        <p style="margin:0 0 6px; font-weight:600;">Gisteren verlopen zonder abonnement:</p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
            <tr><td style="padding:14px 20px; font-size:13px; line-height:1.9; font-weight:400;">
                @foreach($digest['netVerlopen'] as $regel){{ $regel }}<br>@endforeach
            </td></tr>
        </table>
    @endif

    @if(count($digest['stil']))
        <p style="margin:0 0 6px; font-weight:600;">Stille zaken (abonnee, maar 14 dagen zonder bestellingen):</p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
            <tr><td style="padding:14px 20px; font-size:13px; line-height:1.9; font-weight:400;">
                @foreach($digest['stil'] as $regel){{ $regel }}<br>@endforeach
            </td></tr>
        </table>
    @endif

    @include('mail.deel.knop', ['url' => route('admin.index'), 'label' => 'Open het beheer'])
@endsection
