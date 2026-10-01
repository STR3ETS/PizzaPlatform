@extends('bezorger.layout')

@section('titel', $medewerker->isBezorger() ? 'Bezorgen' : 'Keuken')

@section('inhoud')
    {{-- Kop: blijft staan bij het scrollen, zodat je altijd ziet waar je bent --}}
    <header class="sticky top-0 z-30 border-b border-crema-dark" style="background:color-mix(in srgb, var(--color-crema) 94%, transparent); backdrop-filter:blur(8px)">
        <div class="max-w-2xl mx-auto px-4 py-3 flex items-center gap-3">
            <span class="inline-grid w-10 h-10 shrink-0 rounded-xl bg-tomato text-white place-items-center text-lg">
                <i class="fa-solid {{ $medewerker->isBezorger() ? 'fa-moped' : 'fa-fire-burner' }}" aria-hidden="true"></i>
            </span>
            <div class="min-w-0 flex-1">
                <p class="font-display text-lg leading-tight truncate">{{ $medewerker->naam }}</p>
                <p class="text-xs font-semibold text-cacao/50 truncate">{{ $medewerker->rolLabel() }} bij {{ $zaak }}</p>
            </div>
            <div class="text-right shrink-0">
                <p class="font-display text-xl leading-none"><span id="vandaagTeller">0</span></p>
                <p class="text-[11px] font-semibold text-cacao/45">vandaag</p>
            </div>
            <form method="POST" action="{{ route('bezorger.logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="w-10 h-10 rounded-xl border border-crema-dark grid place-items-center text-cacao/50 hover:text-tomato hover:border-tomato/40 transition-colors cursor-pointer" aria-label="Uitloggen">
                    <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 pt-5 pb-16 space-y-7">
        {{-- Alle lijsten worden gevuld door assets/bezorger.js --}}
        <section id="lijstMijn" class="hidden">
            <h2 class="font-display text-xl mb-2.5"><i class="fa-solid fa-moped text-tomato" aria-hidden="true"></i> Mijn rit<span data-meervoud>ten</span></h2>
            <div class="space-y-3" data-kaarten></div>
        </section>

        <section id="lijstKlaar" class="hidden">
            <h2 class="font-display text-xl mb-2.5"><i class="fa-solid fa-box-open text-basil" aria-hidden="true"></i> Klaar om mee te nemen</h2>
            <div class="space-y-3" data-kaarten></div>
        </section>

        <section id="lijstKeuken" class="hidden">
            <h2 class="font-display text-xl mb-2.5"><i class="fa-solid fa-fire text-gold" aria-hidden="true"></i> In de keuken</h2>
            <div class="space-y-3" data-kaarten></div>
        </section>

        <section id="lijstCollega" class="hidden">
            <h2 class="font-display text-xl mb-2.5"><i class="fa-solid fa-user-group text-cacao/40" aria-hidden="true"></i> Onderweg bij een collega</h2>
            <div class="space-y-3" data-kaarten></div>
        </section>

        <section id="lijstKlaarVandaag" class="hidden">
            <h2 class="font-display text-xl mb-2.5"><i class="fa-solid fa-check text-basil" aria-hidden="true"></i> Vandaag afgerond</h2>
            <div class="space-y-2" data-kaarten></div>
        </section>

        {{-- Niets te doen --}}
        <div id="leegBlok" class="hidden text-center py-16">
            <span class="inline-grid w-16 h-16 rounded-2xl bg-white border border-crema-dark place-items-center text-2xl text-cacao/30 mb-4"><i class="fa-solid fa-mug-hot" aria-hidden="true"></i></span>
            <p class="font-display text-xl">Even rustig</p>
            <p class="text-sm font-semibold text-cacao/50 mt-1.5">Er staat nu niets klaar. Zodra er een bestelling binnenkomt, zie je hem hier vanzelf.</p>
        </div>

        <p class="text-center text-xs font-semibold text-cacao/35">
            <i class="fa-solid fa-rotate" aria-hidden="true"></i> Dit scherm ververst zichzelf
        </p>
    </main>
@endsection

@section('scripts')
    <script>
        window.PP_ORDERS = @json($orders);
        window.PP_ROL = @json($medewerker->rol);
        window.PP_MIJ = @json($medewerker->id);
    </script>
    <script src="{{ asset('assets/bezorger.js') }}?v={{ filemtime(public_path('assets/bezorger.js')) }}"></script>
@endsection
