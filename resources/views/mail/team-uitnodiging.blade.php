@extends('mail.layout')

@section('inhoud')
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#2C7A4B;">Uitnodiging</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Je hoort bij het team van {{ $zaak }}</h1>
    <p style="margin:0 0 12px;">
        Hoi {{ explode(' ', $medewerker->naam)[0] }}, {{ $zaak }} heeft je toegevoegd als
        <b>{{ mb_strtolower($medewerker->rolLabel()) }}</b>. Kies hieronder een wachtwoord, dan kun je meteen aan de slag.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:1.9; font-weight:400;">
            <b style="font-weight:600;">Wat je straks kunt doen:</b><br>
            @if($medewerker->isBezorger())
                <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Zien welke bestellingen klaarstaan om te bezorgen<br>
                <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Een rit op je naam zetten, zodat collega's weten dat jij gaat<br>
                <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Onderweg zelf afvinken dat je bezorgd hebt
            @else
                <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Zien welke bestellingen er in de keuken liggen<br>
                <span style="display:inline-block; width:17px; height:17px; border-radius:999px; background-color:#2C7A4B; color:#ffffff; text-align:center; line-height:17px; font-size:10px; font-weight:600; margin-right:6px;">&#10003;</span>Bijhouden wat er bereid wordt en wat de oven in gaat
            @endif
        </td></tr>
    </table>

    @include('mail.deel.knop', ['url' => $link, 'label' => 'Wachtwoord kiezen'])

    <p style="margin:14px 0 0; font-size:12px; font-weight:400; color:#241712; opacity:.55;">
        Deze link is twee weken geldig. Werkt hij niet meer, vraag {{ $zaak }} dan om een nieuwe uitnodiging.
        Had je dit niet verwacht? Dan kun je deze mail gewoon negeren.
    </p>
@endsection
