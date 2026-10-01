{{-- Gedeelde layout voor platformmails, in de MijnPizzeria-huisstijl 2.0:
     ivoor-achtergrond, witte kaart met dunne rand, Young Serif-koppen en
     Inter Tight bodytekst. Iconen zijn ingesloten PNG's, geen emoji's. --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MijnPizzeria</title>
    <style>@import url('https://fonts.googleapis.com/css2?family=Young+Serif&family=Inter+Tight:wght@400;500;600;700&display=swap');</style>
</head>
<body style="margin:0; padding:0; background-color:#FAF8F4;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; padding:30px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; font-family:'Inter Tight', 'Segoe UI', Arial, sans-serif; color:#241712;">
                    <tr>
                        <td align="center" style="padding:0 10px 18px;">
                            <img src="{{ $message->embed(public_path('assets/mail/pizza-slice-tomaat.png')) }}" width="20" height="20" alt="" style="display:inline-block; width:20px; height:20px; vertical-align:middle; margin-right:8px;">
                            <span style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:22px; color:#241712; vertical-align:middle;">MijnPizzeria</span>
                        </td>
                    </tr>
                    @hasSection('mascotte')
                    {{-- De mascotte met spraakwolkje --}}
                    <tr>
                        <td style="padding:0 4px 14px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="100" valign="bottom" style="padding-right:4px;">
                                        <img src="{{ $message->embed(public_path('assets/mail/mascotte-' . trim($__env->yieldContent('mascotte')) . '.png')) }}" width="92" alt="" style="display:block; width:92px; height:auto;">
                                    </td>
                                    <td valign="middle">
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border:1px solid #E9E2D8; border-radius:12px;">
                                            <tr><td style="padding:13px 18px; font-size:13px; font-weight:600; color:#241712; line-height:1.5;">@yield('bubbel')</td></tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="background-color:#ffffff; border:1px solid #E9E2D8; border-bottom:3px solid #E9E2D8; border-radius:14px; padding:32px 30px; font-size:14px; line-height:1.65; font-weight:400;">
                            @yield('inhoud')
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:18px 10px 0; font-size:11px; font-weight:600; color:#241712; opacity:.45;">
                            Shop &amp; Eat, jouw eigen bestelpagina zonder commissie<br>
                            {{ config('app.centraal_domein') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
