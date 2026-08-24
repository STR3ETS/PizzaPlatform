@extends('bestel.layout')

@section('titel', 'Contact | ' . $naam . ($plaats ? ' in ' . $plaats : ''))

@section('inhoud')
    <section class="b-hero">
        <div class="b-hero-blob"></div>
        <div class="max-w-5xl mx-auto px-4 pt-8 pb-6 relative text-center">
            <h1 class="b-kop text-4xl mb-2">Contact</h1>
            <p class="opacity-70">Langskomen, bellen of gewoon online bestellen: alles kan.</p>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-4">
        <div class="grid sm:grid-cols-2 gap-4 items-start">
            <div class="b-kaart p-6">
                <h2 class="b-kop text-xl mb-3">Hier vind je ons</h2>
                @if($adres)
                    <p class="opacity-80">📍 {{ $adres }}</p>
                    <p class="mt-2"><a class="underline underline-offset-2 font-extrabold" style="color: var(--accent)" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($naam . ', ' . $adres) }}">Route via Google Maps</a></p>
                @else
                    <p class="opacity-70">Adres volgt binnenkort.</p>
                @endif
                @if($telefoon)
                    <p class="mt-5 opacity-80">Liever telefonisch bestellen?</p>
                    <a href="tel:{{ $telefoon }}" class="b-knop mt-2">Bel {{ $telefoon }}</a>
                @endif
            </div>
            <div class="b-kaart p-6">
                <h2 class="b-kop text-xl mb-3">Openingstijden</h2>
                @foreach($dagen as $dag)
                    <div class="flex justify-between gap-4 py-0.5 {{ $dag['open'] ? '' : 'opacity-50' }}">
                        <span>{{ $dag['naam'] }}</span>
                        <span>{{ $dag['open'] ? $dag['van'] . ' - ' . $dag['tot'] : 'Gesloten' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
