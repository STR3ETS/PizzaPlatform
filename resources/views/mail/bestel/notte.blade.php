{{-- Notte: één doorlopend menukaart-vel met dunne scheidingslijnen, serif
     koppen, kleine uppercase labels met brede letterafstand. Chique en rustig. --}}
@php $lijn = 'border-bottom:1px solid ' . $gedempt . '33;'; @endphp
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $zaakNaam }}</title>
    <style>@import url('https://fonts.googleapis.com/css2?family={{ $fontImport }}&display=swap');</style>
</head>
<body style="margin:0; padding:0; background-color:{{ $achtergrond }};">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:{{ $achtergrond }}; padding:34px 14px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; font-family:{{ $bodyFont }}; color:{{ $tekst }}; background-color:#ffffff; border-top:4px solid {{ $primair }}; border-radius:6px;">
                <tr>
                    <td style="padding:40px 38px 0;" align="center">
                        <span style="display:block; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.28em; color:{{ $gedempt }};">Orderbevestiging</span>
                        <span style="display:block; margin-top:12px; font-family:{{ $kopFont }}; font-size:26px; font-weight:600; color:{{ $tekst }};">{{ $zaakNaam }}</span>
                        <span style="display:block; margin:22px auto 0; width:44px; border-bottom:2px solid {{ $primair }};"></span>
                    </td>
                </tr>

                <tr>
                    <td style="padding:26px 38px 30px; {{ $lijn }}" align="center">
                        <span style="display:block; font-family:{{ $kopFont }}; font-size:24px; font-weight:600; color:{{ $tekst }};">Bedankt voor je bestelling.</span>
                        <span style="display:block; margin-top:8px; font-size:13px; font-weight:400; color:{{ $gedempt }};">Bestelling #{{ $order->nummer }} is in goede orde ontvangen. We gaan er met zorg mee aan de slag.</span>
                    </td>
                </tr>

                {{-- Stappen als verticale lijst, zoals een menukaart-register --}}
                <tr>
                    <td style="padding:26px 38px 28px; {{ $lijn }}">
                        <span style="display:block; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.28em; color:{{ $gedempt }}; text-align:center; margin-bottom:16px;">De status van je bestelling</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                            @foreach($stappen as [$stapIcoon, $label, $actief])
                                <tr>
                                    <td width="34" style="padding:6px 0;">
                                        <span style="display:inline-block; width:26px; height:26px; border-radius:999px; background-color:{{ $actief ? $primair : $achtergrond }}; text-align:center; line-height:26px;"><img src="{{ $icoon($stapIcoon, $actief) }}" width="13" height="13" alt="" style="display:inline-block; width:13px; height:13px; vertical-align:middle;"></span>
                                    </td>
                                    <td style="padding:6px 0 6px 12px; font-weight:{{ $actief ? '800' : '400' }}; text-transform:uppercase; letter-spacing:.12em; font-size:11px; color:{{ $actief ? $tekst : $gedempt }};">{{ $label }}</td>
                                    <td align="right" style="padding:6px 0; font-size:11px; font-weight:400; color:{{ $actief ? $primair : $gedempt }};">{{ $actief ? 'Nu bezig' : '' }}</td>
                                </tr>
                            @endforeach
                        </table>
                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:18px auto 0;">
                            <tr><td style="background-color:{{ $primair }}; border-radius:4px;">
                                <a href="{{ $statusLink }}" style="display:inline-block; padding:12px 28px; color:#ffffff; font-weight:800; font-size:12px; text-transform:uppercase; letter-spacing:.14em; text-decoration:none; font-family:{{ $bodyFont }};">Volg de bestelling</a>
                            </td></tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:26px 38px 26px; {{ $lijn }}">
                        <span style="display:block; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.28em; color:{{ $gedempt }}; text-align:center; margin-bottom:16px;">De gegevens</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; line-height:1.6;">
                            @foreach($bevestigingRegels as [$label, $waarde])
                                <tr>
                                    <td width="40%" style="padding:4px 12px 4px 0; font-weight:800; font-size:10px; text-transform:uppercase; letter-spacing:.14em; color:{{ $gedempt }}; vertical-align:top;">{{ $label }}</td>
                                    <td style="padding:4px 0; font-weight:400; vertical-align:top; color:{{ $tekst }};">{{ $waarde }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:26px 38px 30px;">
                        <span style="display:block; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.28em; color:{{ $gedempt }}; text-align:center; margin-bottom:16px;">De bestelling</span>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                            @foreach($gerechten as $item)
                                <tr>
                                    <td style="padding:7px 10px 2px 0; font-family:{{ $kopFont }}; font-weight:600; font-size:15px; color:{{ $tekst }};">{{ $item['aantal'] }}x {{ $item['naam'] }}</td>
                                    <td align="right" style="padding:7px 0 2px; font-weight:400; white-space:nowrap; color:{{ $tekst }};">{{ $euro($regelPrijs($item)) }}</td>
                                </tr>
                                @foreach($item['opties'] ?? [] as $optie)
                                    <tr><td colspan="2" style="padding:0 0 3px 14px; color:{{ $gedempt }}; font-weight:400; font-size:12px; font-style:italic;">{{ ($optie['type'] ?? '') === 'zonder' ? '' : 'met ' }}{{ strtolower($optie['naam']) }}{{ ($optie['prijs'] ?? 0) > 0 ? ', ' . $euro($optie['prijs']) : '' }}</td></tr>
                                @endforeach
                            @endforeach
                            <tr><td colspan="2" style="padding-top:12px; border-bottom:1px solid {{ $gedempt }}33; font-size:0; line-height:0;">&nbsp;</td></tr>
                            @foreach($kostenRegels as [$kostLabel, $kostBedrag, $isKorting])
                                <tr>
                                    <td style="padding:6px 10px 0 0; font-weight:400; font-size:12px; text-transform:uppercase; letter-spacing:.1em; font-size:10px; color:{{ $isKorting ? $primair : $gedempt }};">{{ $kostLabel }}</td>
                                    <td align="right" style="padding:6px 0 0; font-weight:400; font-size:12px; white-space:nowrap; color:{{ $isKorting ? $primair : $gedempt }};">{{ $kostBedrag }}</td>
                                </tr>
                            @endforeach
                            <tr><td colspan="2" style="padding-top:14px; border-bottom:2px solid {{ $tekst }}; font-size:0; line-height:0;">&nbsp;</td></tr>
                            <tr>
                                <td style="padding:12px 10px 0 0; font-family:{{ $kopFont }}; font-weight:600; font-size:17px; color:{{ $tekst }};">Totaal</td>
                                <td align="right" style="padding:12px 0 0; font-family:{{ $kopFont }}; font-weight:600; font-size:17px; white-space:nowrap; color:{{ $tekst }};">{{ $euro($order->totaal) }}</td>
                            </tr>
                        </table>
                        @if($order->punten > 0)
                            <p style="margin:18px 0 0; text-align:center; font-size:12px; font-weight:400; color:{{ $gedempt }};">U heeft <span style="color:{{ $primair }};">{{ $order->punten }} spaarpunt{{ $order->punten === 1 ? '' : 'en' }}</span> verdiend bij deze bestelling. Per 100 punten ontvangt u 5 euro korting.</p>
                        @endif
                        <p style="margin:14px 0 0; text-align:center; font-size:11px; font-weight:400; color:{{ $gedempt }};">Deze orderbevestiging is geen factuur.</p>
                    </td>
                </tr>
            </table>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; font-family:{{ $bodyFont }};">
                <tr>
                    <td style="padding:16px 10px 0; font-size:11px; font-weight:400; color:{{ $gedempt }}; line-height:1.6;" align="center">
                        {{ $voetTekst }}<br>
                        Deze e-mail is automatisch verstuurd namens {{ $zaakNaam }}.
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
