{{-- Forza: aaneengesloten volle banden zonder afronding of tussenruimte,
     mega uppercase koppen, dikke randen. Bold en vol energie. --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $zaakNaam }}</title>
    <style>@import url('https://fonts.googleapis.com/css2?family={{ $fontImport }}&display=swap');</style>
</head>
<body style="margin:0; padding:0; background-color:{{ $achtergrond }};">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:{{ $achtergrond }}; padding:28px 14px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; font-family:{{ $bodyFont }}; color:{{ $tekst }};">

                {{-- Zwarte kopband met de zaaknaam --}}
                <tr>
                    <td style="background-color:{{ $tekst }}; padding:16px 28px;" align="center">
                        <span style="font-family:{{ $kopFont }}; font-size:20px; font-weight:400; text-transform:uppercase; letter-spacing:.08em; color:#ffffff;">{{ $zaakNaam }}</span>
                    </td>
                </tr>

                {{-- Hero-band direct eronder --}}
                <tr>
                    <td style="background-color:{{ $primair }}; padding:44px 28px;" align="center">
                        <span style="display:block; font-family:{{ $kopFont }}; font-size:38px; line-height:1.05; font-weight:400; text-transform:uppercase; letter-spacing:.02em; color:#ffffff;">Bedankt voor<br>je bestelling</span>
                        <span style="display:block; margin-top:12px; font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.1em; color:#ffffff; opacity:.85;">Bestelling #{{ $order->nummer }} is binnen</span>
                    </td>
                </tr>

                {{-- Stappenband --}}
                <tr>
                    <td style="padding:0;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                @foreach($stappen as [$stapIcoon, $label, $actief])
                                    <td width="25%" align="center" style="padding:16px 4px; background-color:{{ $actief ? '#ffffff' : $tekst }}; border-right:{{ $loop->last ? 'none' : '1px solid ' . ($actief ? $achtergrond : '#ffffff22') }};">
                                        <img src="{{ $icoon($stapIcoon, ! $actief) }}" width="20" height="20" alt="" style="display:inline-block; width:20px; height:20px;">
                                        <span style="display:block; margin-top:6px; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:{{ $actief ? $tekst : '#ffffff' }};">{{ $label }}</span>
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Tracker-knop over de volle breedte --}}
                <tr>
                    <td style="padding:0;">
                        <a href="{{ $statusLink }}" style="display:block; background-color:{{ $primair }}; border-top:2px solid #ffffff; padding:16px 10px; color:#ffffff; font-family:{{ $kopFont }}; font-weight:400; font-size:16px; text-transform:uppercase; letter-spacing:.1em; text-align:center; text-decoration:none;">Volg je bestelling live</a>
                    </td>
                </tr>

                {{-- Orderbevestiging in een omkaderd blok --}}
                <tr>
                    <td style="background-color:#ffffff; border:2px solid {{ $tekst }}; border-top:none; padding:28px;">
                        <span style="display:block; font-family:{{ $kopFont }}; font-size:20px; font-weight:400; text-transform:uppercase; letter-spacing:.04em; margin-bottom:16px; color:{{ $tekst }};">Orderbevestiging</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; line-height:1.5;">
                            @foreach($bevestigingRegels as [$label, $waarde])
                                <tr>
                                    <td width="40%" style="padding:5px 10px 5px 0; font-weight:800; color:{{ $gedempt }}; text-transform:uppercase; font-size:10px; letter-spacing:.08em; vertical-align:top;">{{ $label }}</td>
                                    <td style="padding:5px 0; font-weight:400; vertical-align:top; color:{{ $tekst }};">{{ $waarde }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                {{-- De bestelling, afgesloten met een totaalband --}}
                <tr>
                    <td style="background-color:#ffffff; border:2px solid {{ $tekst }}; border-top:none; padding:28px 28px 0;">
                        <span style="display:block; font-family:{{ $kopFont }}; font-size:20px; font-weight:400; text-transform:uppercase; letter-spacing:.04em; margin-bottom:12px; color:{{ $tekst }};">Jouw bestelling</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                            @foreach($gerechten as $item)
                                <tr>
                                    <td style="padding:7px 10px 2px 0; font-weight:800; text-transform:uppercase; color:{{ $tekst }};">{{ $item['aantal'] }}x {{ $item['naam'] }}</td>
                                    <td align="right" style="padding:7px 0 2px; font-weight:800; white-space:nowrap; color:{{ $tekst }};">{{ $euro($regelPrijs($item)) }}</td>
                                </tr>
                                @foreach($item['opties'] ?? [] as $optie)
                                    <tr><td colspan="2" style="padding:0 0 2px 16px; color:{{ $gedempt }}; font-weight:400; font-size:12px;">{{ ($optie['type'] ?? '') === 'zonder' ? '' : '+ ' }}{{ $optie['naam'] }}{{ ($optie['prijs'] ?? 0) > 0 ? ' (' . $euro($optie['prijs']) . ')' : '' }}</td></tr>
                                @endforeach
                            @endforeach
                            <tr><td colspan="2" style="padding-top:10px; border-bottom:2px solid {{ $tekst }}; font-size:0; line-height:0;">&nbsp;</td></tr>
                            @foreach($kostenRegels as [$kostLabel, $kostBedrag, $isKorting])
                                <tr>
                                    <td style="padding:6px 10px 0 0; font-weight:800; text-transform:uppercase; font-size:11px; letter-spacing:.06em; color:{{ $isKorting ? $primair : $gedempt }};">{{ $kostLabel }}</td>
                                    <td align="right" style="padding:6px 0 0; font-weight:800; white-space:nowrap; color:{{ $isKorting ? $primair : $gedempt }};">{{ $kostBedrag }}</td>
                                </tr>
                            @endforeach
                        </table>
                        <p style="margin:14px 0 16px; font-size:11px; font-weight:400; color:{{ $gedempt }};">Let op: deze orderbevestiging is geen factuur.</p>
                    </td>
                </tr>
                <tr>
                    <td style="background-color:{{ $tekst }}; padding:16px 28px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-family:{{ $kopFont }}; font-size:18px; font-weight:400; text-transform:uppercase; letter-spacing:.06em; color:#ffffff;">Totaal</td>
                                <td align="right" style="font-family:{{ $kopFont }}; font-size:18px; font-weight:400; color:#ffffff; white-space:nowrap;">{{ $euro($order->totaal) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                @if($order->punten > 0)
                    <tr>
                        <td style="background-color:#ffffff; border:2px solid {{ $primair }}; border-top:none; padding:18px 28px;" align="center">
                            <span style="display:block; font-family:{{ $kopFont }}; font-size:16px; font-weight:400; text-transform:uppercase; letter-spacing:.06em; color:{{ $primair }};">+{{ $order->punten }} spaarpunt{{ $order->punten === 1 ? '' : 'en' }} verdiend</span>
                            <span style="display:block; margin-top:5px; font-size:11px; font-weight:400; color:{{ $gedempt }};">Per 100 punten krijg je 5 euro korting bij het afrekenen.</span>
                        </td>
                    </tr>
                @endif

                <tr>
                    <td style="padding:18px 10px 0; font-size:11px; font-weight:400; color:{{ $gedempt }}; line-height:1.6;">
                        {{ $voetTekst }}<br>
                        Deze e-mail is automatisch verstuurd namens {{ $zaakNaam }}.
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
