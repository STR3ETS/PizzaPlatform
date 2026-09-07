{{-- Giro: witte pagina met zachte getinte kaarten, losse ronde stap-cirkels,
     veel lucht. Fris en modern, als een app. --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $zaakNaam }}</title>
    <style>@import url('https://fonts.googleapis.com/css2?family={{ $fontImport }}&display=swap');</style>
</head>
<body style="margin:0; padding:0; background-color:#ffffff;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#ffffff; padding:32px 14px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; font-family:{{ $bodyFont }}; color:{{ $tekst }};">

                {{-- App-achtige kop: naam links, bestelnummer als pill rechts --}}
                <tr>
                    <td style="padding:0 6px 20px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-size:19px; font-weight:800; color:{{ $tekst }};">{{ $zaakNaam }}</td>
                                <td align="right"><span style="display:inline-block; background-color:{{ $achtergrond }}; color:{{ $primair }}; border-radius:999px; padding:6px 14px; font-size:12px; font-weight:800;">#{{ $order->nummer }}</span></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Luchtige hero: veel wit, gekleurde begroeting --}}
                <tr>
                    <td style="padding:8px 6px 26px;" align="center">
                        <span style="display:inline-block; background-color:{{ $primair }}; color:#ffffff; border-radius:999px; padding:7px 18px; font-size:12px; font-weight:800;">Bestelling ontvangen</span>
                        <span style="display:block; margin-top:16px; font-size:28px; line-height:1.2; font-weight:800; color:{{ $tekst }};">Bedankt, {{ explode(' ', $order->klant)[0] }}!</span>
                        <span style="display:block; margin-top:8px; font-size:14px; font-weight:400; color:{{ $gedempt }};">We gaan meteen voor je aan de slag.</span>
                    </td>
                </tr>

                {{-- Losse ronde stap-cirkels met verbindingsgevoel --}}
                <tr>
                    <td style="background-color:{{ $achtergrond }}; border-radius:24px; padding:24px 18px;" align="center">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                @foreach($stappen as [$stapIcoon, $label, $actief])
                                    <td width="25%" align="center" style="padding:4px 2px;">
                                        <span style="display:inline-block; width:44px; height:44px; border-radius:999px; background-color:{{ $actief ? $primair : '#ffffff' }}; text-align:center; line-height:44px;"><img src="{{ $icoon($stapIcoon, $actief) }}" width="18" height="18" alt="" style="display:inline-block; width:18px; height:18px; vertical-align:middle;"></span>
                                        <span style="display:block; margin-top:7px; font-size:10px; font-weight:800; color:{{ $actief ? $tekst : $gedempt }};">{{ $label }}</span>
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:18px auto 2px;">
                            <tr><td style="background-color:{{ $primair }}; border-radius:999px;">
                                <a href="{{ $statusLink }}" style="display:inline-block; padding:13px 30px; color:#ffffff; font-weight:800; font-size:14px; text-decoration:none; font-family:{{ $bodyFont }};">Volg je bestelling</a>
                            </td></tr>
                        </table>
                    </td>
                </tr>
                <tr><td style="height:16px; line-height:16px; font-size:0;">&nbsp;</td></tr>

                {{-- Gegevens en bestelling als zachte kaarten --}}
                <tr>
                    <td style="background-color:{{ $achtergrond }}; border-radius:24px; padding:26px 24px;">
                        <span style="display:block; font-size:16px; font-weight:800; margin-bottom:12px; color:{{ $tekst }};">Orderbevestiging</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; line-height:1.5;">
                            @foreach($bevestigingRegels as [$label, $waarde])
                                <tr>
                                    <td width="40%" style="padding:5px 10px 5px 0; font-weight:800; color:{{ $gedempt }}; font-size:11px; vertical-align:top;">{{ $label }}</td>
                                    <td style="padding:5px 0; font-weight:400; vertical-align:top; color:{{ $tekst }};">{{ $waarde }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr><td style="height:16px; line-height:16px; font-size:0;">&nbsp;</td></tr>

                <tr>
                    <td style="background-color:{{ $achtergrond }}; border-radius:24px; padding:26px 24px;">
                        <span style="display:block; font-size:16px; font-weight:800; margin-bottom:12px; color:{{ $tekst }};">Jouw bestelling</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                            @foreach($gerechten as $item)
                                <tr>
                                    <td style="padding:8px 10px 2px 0; font-weight:800; color:{{ $tekst }};">{{ $item['aantal'] }}x {{ $item['naam'] }}</td>
                                    <td align="right" style="padding:8px 0 2px; font-weight:800; white-space:nowrap; color:{{ $tekst }};">{{ $euro($regelPrijs($item)) }}</td>
                                </tr>
                                @foreach($item['opties'] ?? [] as $optie)
                                    <tr><td colspan="2" style="padding:0 0 2px 16px; color:{{ $gedempt }}; font-weight:400; font-size:12px;">{{ ($optie['type'] ?? '') === 'zonder' ? '' : '+ ' }}{{ $optie['naam'] }}{{ ($optie['prijs'] ?? 0) > 0 ? ' (' . $euro($optie['prijs']) . ')' : '' }}</td></tr>
                                @endforeach
                            @endforeach
                            <tr><td colspan="2" style="padding-top:10px; border-bottom:2px solid #ffffff; font-size:0; line-height:0;">&nbsp;</td></tr>
                            @foreach($kostenRegels as [$kostLabel, $kostBedrag, $isKorting])
                                <tr>
                                    <td style="padding:6px 10px 0 0; font-weight:400; color:{{ $isKorting ? $primair : $gedempt }};">{{ $kostLabel }}</td>
                                    <td align="right" style="padding:6px 0 0; font-weight:400; white-space:nowrap; color:{{ $isKorting ? $primair : $gedempt }};">{{ $kostBedrag }}</td>
                                </tr>
                            @endforeach
                        </table>
                        {{-- Totaal als witte kaart in de kaart --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:16px; background-color:#ffffff; border-radius:16px;">
                            <tr>
                                <td style="padding:14px 18px; font-weight:800; font-size:16px; color:{{ $tekst }};">Totaal</td>
                                <td align="right" style="padding:14px 18px; font-weight:800; font-size:16px; white-space:nowrap; color:{{ $primair }};">{{ $euro($order->totaal) }}</td>
                            </tr>
                        </table>
                        <p style="margin:12px 0 0; font-size:11px; font-weight:400; color:{{ $gedempt }};">Let op: deze orderbevestiging is geen factuur.</p>
                    </td>
                </tr>

                @if($order->punten > 0)
                    <tr><td style="height:16px; line-height:16px; font-size:0;">&nbsp;</td></tr>
                    <tr>
                        <td style="background-color:{{ $primair }}; border-radius:24px; padding:22px 24px;" align="center">
                            <span style="display:block; font-size:16px; font-weight:800; color:#ffffff;">+{{ $order->punten }} spaarpunt{{ $order->punten === 1 ? '' : 'en' }} verdiend</span>
                            <span style="display:block; margin-top:6px; font-size:12px; font-weight:400; color:#ffffff; opacity:.85;">Per 100 punten krijg je 5 euro korting bij het afrekenen.</span>
                        </td>
                    </tr>
                @endif

                <tr>
                    <td style="padding:20px 10px 0; font-size:11px; font-weight:400; color:{{ $gedempt }}; line-height:1.6;" align="center">
                        {{ $voetTekst }}<br>
                        Deze e-mail is automatisch verstuurd namens {{ $zaakNaam }}.
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
