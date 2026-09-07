@extends('mail.layout')

@section('mascotte', 'waving-hello')
@section('bubbel')Laatste zwaai, {{ explode(' ', $user->name)[0] }}! Zullen we er twee weken extra proeftijd opzetten?@endsection

@section('inhoud')
    @php $zaak = $user->onboarding['name'] ?? 'jouw pizzeria'; @endphp
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#2C7A4B;">Een extra kans</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Twee weken extra, van ons</h1>
    <p style="margin:0 0 12px;">
        Twee weken geleden liep de proefperiode van <b>{{ $zaak }}</b> af. Misschien kwam het er gewoon niet van,
        en dat snappen we; een zaak runnen is druk genoeg.
    </p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:1.8; font-weight:400;">
            <b style="font-weight:600;">Daarom dit aanbod:</b> klik op de knop hieronder en je krijgt direct
            <b>twee weken extra gratis proeftijd</b>. Geen voorwaarden, gewoon opnieuw rustig proberen
            met alles wat je al had ingesteld.
        </td></tr>
    </table>
    @include('mail.deel.knop', ['url' => \Illuminate\Support\Facades\URL::signedRoute('feedback.verlenging', ['user' => $user->id]), 'label' => 'Ja, geef mij twee weken extra'])
    <p style="margin:16px 0 0;">
        Dit is de laatste mail die we hierover sturen; we willen je niet lastigvallen.
        Wil je dat we je gegevens verwijderen, dan is één berichtje ook genoeg.<br>
        <b>Het team van MijnPizzeria</b>
    </p>
@endsection
