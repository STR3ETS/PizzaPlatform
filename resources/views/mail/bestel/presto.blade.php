{{-- Presto: losse ronde kaarten op de warme achtergrond, pill-knoppen, speels en vriendelijk --}}
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
                <tr>
                    <td align="center" style="padding:0 10px 16px;">
                        <span style="font-family:{{ $kopFont }}; font-size:22px; font-weight:800; color:{{ $tekst }};">{{ $zaakNaam }}</span>
                    </td>
                </tr>

                <tr>
                    <td style="background-color:{{ $primair }}; border-radius:22px; padding:38px 28px;" align="center">
                        <span style="display:block; font-family:{{ $kopFont }}; font-size:30px; line-height:1.15; font-weight:800; color:#ffffff;">Bedankt voor je bestelling!</span>
                        <span style="display:block; margin-top:10px; font-size:14px; font-weight:400; color:#ffffff; opacity:.85;">Bestelling #{{ $order->nummer }} is binnen, we gaan meteen voor je aan de slag.</span>
                    </td>
                </tr>
                <tr><td style="height:14px; line-height:14px; font-size:0;">&nbsp;</td></tr>

                <tr>
                    <td style="background-color:#ffffff; border-radius:22px; padding:26px 24px;" align="center">
                        <span style="display:block; font-family:{{ $kopFont }}; font-size:19px; font-weight:800; margin-bottom:16px; color:{{ $tekst }};">Volg je bestelling live</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                @foreach($stappen as [$stapIcoon, $label, $actief])
                                    <td width="25%" align="center" style="padding:12px 4px; background-color:{{ $actief ? $primair : $achtergrond }}; {{ $loop->first ? 'border-radius:14px 0 0 14px;' : ($loop->last ? 'border-radius:0 14px 14px 0;' : '') }}">
                                        <img src="{{ $icoon($stapIcoon, $actief) }}" width="20" height="20" alt="" style="display:inline-block; width:20px; height:20px;">
                                        <span style="display:block; margin-top:6px; font-size:10px; font-weight:800; color:{{ $actief ? '#ffffff' : $gedempt }};">{{ $label }}</span>
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:20px auto 4px;">
                            <tr><td style="background-color:{{ $primair }}; border-radius:999px;">
                                <a href="{{ $statusLink }}" style="display:inline-block; padding:13px 30px; color:#ffffff; font-weight:800; font-size:14px; text-decoration:none; font-family:{{ $bodyFont }};">Open de bestelling-tracker</a>
                            </td></tr>
                        </table>
                    </td>
                </tr>
                <tr><td style="height:14px; line-height:14px; font-size:0;">&nbsp;</td></tr>

                <tr>
                    <td style="background-color:#ffffff; border-radius:22px; padding:26px 24px;">
                        <span style="display:block; font-family:{{ $kopFont }}; font-size:19px; font-weight:800; margin-bottom:14px; text-align:center; color:{{ $tekst }};">Orderbevestiging</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; line-height:1.5;">
                            @foreach($bevestigingRegels as [$label, $waarde])
                                <tr>
                                    <td width="40%" style="padding:5px 10px 5px 0; font-weight:800; color:{{ $gedempt }}; text-transform:uppercase; font-size:10px; letter-spacing:.04em; vertical-align:top;">{{ $label }}</td>
                                    <td style="padding:5px 0; font-weight:400; vertical-align:top; color:{{ $tekst }};">{{ $waarde }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr><td style="height:14px; line-height:14px; font-size:0;">&nbsp;</td></tr>

                <tr>
                    <td style="background-color:#ffffff; border-radius:22px; padding:26px 24px;">
                        <span style="display:block; font-family:{{ $kopFont }}; font-size:19px; font-weight:800; margin-bottom:14px; text-align:center; color:{{ $tekst }};">Jouw bestelling</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                            @foreach($gerechten as $item)
                                <tr>
                                    <td style="padding:7px 10px 2px 0; font-weight:800; color:{{ $tekst }};">{{ $item['aantal'] }}x {{ $item['naam'] }}</td>
                                    <td align="right" style="padding:7px 0 2px; font-weight:800; white-space:nowrap; color:{{ $tekst }};">{{ $euro($regelPrijs($item)) }}</td>
                                </tr>
                                @foreach($item['opties'] ?? [] as $optie)
                                    <tr><td colspan="2" style="padding:0 0 2px 16px; color:{{ $gedempt }}; font-weight:400; font-size:12px;">{{ ($optie['type'] ?? '') === 'zonder' ? '' : '+ ' }}{{ $optie['naam'] }}{{ ($optie['prijs'] ?? 0) > 0 ? ' (' . $euro($optie['prijs']) . ')' : '' }}</td></tr>
                                @endforeach
                            @endforeach
                            <tr><td colspan="2" style="padding-top:10px; border-bottom:2px solid {{ $achtergrond }}; font-size:0; line-height:0;">&nbsp;</td></tr>
                            @foreach($kostenRegels as [$kostLabel, $kostBedrag, $isKorting])
                                <tr>
                                    <td style="padding:6px 10px 0 0; font-weight:400; color:{{ $isKorting ? $primair : $gedempt }};">{{ $kostLabel }}</td>
                                    <td align="right" style="padding:6px 0 0; font-weight:400; white-space:nowrap; color:{{ $isKorting ? $primair : $gedempt }};">{{ $kostBedrag }}</td>
                                </tr>
                            @endforeach
                            <tr><td colspan="2" style="padding-top:12px; border-bottom:2px solid {{ $achtergrond }}; font-size:0; line-height:0;">&nbsp;</td></tr>
                            <tr>
                                <td style="padding:12px 10px 0 0; font-weight:800; font-size:16px; color:{{ $tekst }};">Totaalbedrag</td>
                                <td align="right" style="padding:12px 0 0; font-weight:800; font-size:16px; white-space:nowrap; color:{{ $tekst }};">{{ $euro($order->totaal) }}</td>
                            </tr>
                        </table>
                        <p style="margin:14px 0 0; font-size:11px; font-weight:400; color:{{ $gedempt }};">Let op: deze orderbevestiging is geen factuur.</p>
                    </td>
                </tr>

                @if($order->punten > 0)
                    <tr><td style="height:14px; line-height:14px; font-size:0;">&nbsp;</td></tr>
                    <tr>
                        <td style="background-color:#ffffff; border:3px solid {{ $primair }}; border-radius:22px; padding:20px 24px;" align="center">
                            <span style="display:inline-block; background-color:{{ $primair }}; color:#ffffff; border-radius:999px; padding:5px 14px; font-size:13px; font-weight:800;">+{{ $order->punten }} punt{{ $order->punten === 1 ? '' : 'en' }}</span>
                            <span style="display:block; margin-top:10px; font-family:{{ $kopFont }}; font-size:16px; font-weight:800; color:{{ $tekst }};">Je hebt spaarpunten verdiend!</span>
                            <span style="display:block; margin-top:6px; font-size:12px; font-weight:400; color:{{ $gedempt }};">Sparen gaat vanzelf bij elke bestelling. Per 100 punten krijg je 5 euro korting bij het afrekenen.</span>
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
