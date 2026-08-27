<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inloggen bij {{ $naam }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme static {
            --font-sans: "Archivo", sans-serif;
            --color-primair: {{ $palet['primair'] }};
            --color-primair-donker: {{ $palet['primairDonker'] }};
            --color-secundair: {{ $palet['secundair'] }};
            --color-secundair-licht: {{ $palet['secundairLicht'] }};
            --color-wit-warm: {{ $palet['witWarm'] }};
        }
        body { font-family: var(--font-sans); } h1, h2 { font-family: "Anton", "Arial Narrow", sans-serif; text-transform: uppercase; letter-spacing: .015em; }
    </style>
</head>
<body class="bg-wit-warm min-h-screen">
    {{-- Vlakke witte balk met dunne lijn: titel in Anton, terug als tekstlink --}}
    <header class="sticky top-0 z-40 bg-white border-b border-secundair/15">
        <div class="max-w-5xl mx-auto px-6 py-4 grid grid-cols-[1fr_auto_1fr] items-center gap-4">
            <div>
                <a href="{{ $basis }}" class="inline-flex items-center gap-2.5 text-sm font-bold uppercase tracking-wide text-secundair/60 hover:text-secundair transition-colors">
                    <i class="fa-solid fa-arrow-left-long" aria-hidden="true"></i> Menu
                </a>
            </div>
            <h2 class="text-2xl text-secundair">Bijna klaar</h2>
            <p class="text-right text-sm font-bold uppercase tracking-wide text-secundair/40 truncate hidden sm:block">{{ $naam }}</p>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-10">
        <div class="text-center">
            <div class="mx-auto w-14 h-14 rounded-none overflow-hidden border border-black/10 bg-white grid place-items-center">
                @if($logo)
                    <img src="{{ $logo }}" alt="" class="w-full h-full object-cover">
                @else
                    <i class="fa-solid fa-pizza-slice text-xl text-primair" aria-hidden="true"></i>
                @endif
            </div>
            <h1 class="mt-4 text-2xl font-extrabold text-secundair">Log in of maak een account</h1>
            <p class="mt-1 text-sm font-semibold text-secundair/55">Dan staan je gegevens de volgende keer al klaar{{ $spaarpunten ? ' en spaar je automatisch mee' : '' }}.</p>
        </div>

        <div class="mt-8 grid lg:grid-cols-2 gap-6 items-start">
            {{-- Inloggen --}}
            <section class="bg-white rounded-none border border-black/10 p-6">
                <h2 class="text-lg font-extrabold text-secundair">Welkom terug</h2>
                <p class="mt-1 text-sm font-semibold text-secundair/50">Log in met het account van {{ $naam }}.</p>
                <form method="POST" action="{{ $basis }}/inloggen" class="mt-5 space-y-3">
                    @csrf
                    <input type="hidden" name="verder" value="{{ request('verder') }}">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="E-mailadres" autocomplete="email"
                        class="w-full rounded-none border border-black/10 px-3.5 py-3 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors">
                    <input type="password" name="wachtwoord" placeholder="Wachtwoord" autocomplete="current-password"
                        class="w-full rounded-none border border-black/10 px-3.5 py-3 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors">
                    @if($errors->login->any())
                        <p class="text-xs font-bold text-primair-donker">{{ $errors->login->first() }}</p>
                    @endif
                    <button type="submit" class="w-full py-3.5 rounded-none bg-primair font-bold text-white hover:bg-primair-donker transition-colors cursor-pointer">Inloggen</button>
                </form>
            </section>

            {{-- Account maken --}}
            <section class="bg-white rounded-none border border-black/10 p-6">
                <h2 class="text-lg font-extrabold text-secundair">Nieuw bij {{ $naam }}?</h2>
                <p class="mt-1 text-sm font-semibold text-secundair/50">Maak in een paar tellen een account aan.</p>
                <form method="POST" action="{{ $basis }}/registreren" class="mt-5 space-y-3">
                    @csrf
                    <input type="hidden" name="verder" value="{{ request('verder') }}">
                    <input type="text" name="naam" value="{{ old('naam') }}" placeholder="Je naam" autocomplete="name"
                        class="w-full rounded-none border border-black/10 px-3.5 py-3 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="E-mailadres" autocomplete="email"
                        class="w-full rounded-none border border-black/10 px-3.5 py-3 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors">
                    <input type="tel" name="telefoon" value="{{ old('telefoon') }}" placeholder="Telefoonnummer (optioneel)" autocomplete="tel"
                        class="w-full rounded-none border border-black/10 px-3.5 py-3 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors">
                    <input type="password" name="wachtwoord" placeholder="Kies een wachtwoord (minimaal 8 tekens)" autocomplete="new-password"
                        class="w-full rounded-none border border-black/10 px-3.5 py-3 text-sm font-semibold text-secundair placeholder:text-secundair/40 outline-none focus:border-primair/50 transition-colors">
                    @if($errors->registratie->any())
                        <p class="text-xs font-bold text-primair-donker">{{ $errors->registratie->first() }}</p>
                    @endif
                    <button type="submit" class="w-full py-3.5 rounded-none bg-secundair font-bold text-white hover:bg-primair transition-colors cursor-pointer">Account maken</button>
                </form>
            </section>
        </div>

        <p class="mt-8 text-center">
            <a id="gastLink" href="{{ $basis }}/afrekenen?gast=1" class="text-sm font-bold text-secundair/50 underline underline-offset-4 decoration-secundair/30 hover:text-secundair transition-colors">Doorgaan zonder account</a>
        </p>
    </main>
</body>
</html>
