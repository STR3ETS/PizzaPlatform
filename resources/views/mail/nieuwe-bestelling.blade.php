@extends('mail.layout')

@section('mascotte', 'pizza-in-oven')
@section('bubbel')Oven aan! Er is werk aan de winkel bij {{ $pizzeria->onboarding['name'] ?? 'jouw zaak' }}.@endsection

@section('inhoud')
    <p style="margin:0 0 6px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#B04A3F;">Kassa</p>
    <h1 style="font-family:'Young Serif', Georgia, serif; font-weight:400; font-size:26px; margin:0 0 14px; color:#241712;">Nieuwe bestelling</h1>
    <p style="margin:0 0 12px;">
        Zojuist binnengekomen: <b>bestelling #{{ $order->nummer }}</b> van <b>{{ $order->klant }}</b>,
        {{ $order->type === 'bezorgen' ? 'om te bezorgen' : 'om af te halen' }}.
        Accepteer de bestelling in je dashboard, dan ziet de klant meteen dat je ermee bezig bent.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF8F4; border-bottom:2px solid #E9E2D8; border-radius:14px; margin:0 0 12px;">
        <tr><td style="padding:16px 20px; font-size:13px; line-height:1.9; font-weight:400;">
            <b style="font-weight:600;">{{ $order->type === 'bezorgen' ? 'Bezorgen op: ' . $order->adres : 'Afhalen aan de zaak' }}</b><br>
            @foreach($order->items as $item)
                {{ $item['aantal'] }}x {{ $item['naam'] }}@if(! empty($item['opties'])) <span style="color:#241712; opacity:.5;">({{ collect($item['opties'])->pluck('naam')->implode(', ') }})</span>@endif<br>
            @endforeach
            <b style="font-weight:600;">Totaal: &euro; {{ number_format($order->totaal / 100, 2, ',', '.') }}</b>
            @if($order->opmerking)
                <br><b style="font-weight:600;">Opmerking van de klant:</b> {{ $order->opmerking }}
            @endif
        </td></tr>
    </table>

    @include('mail.deel.knop', ['url' => route('dashboard', ['paneel' => 'bestellingen']), 'label' => 'Open je bestellingen'])

    <p style="margin:16px 0 0; font-size:12px; color:#241712; opacity:.55;">
        Tip: hou de statussen bij in je dashboard; je klant kijkt live mee en dat scheelt telefoontjes.
    </p>
@endsection
