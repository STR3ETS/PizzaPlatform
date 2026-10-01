{{-- Stap: spaarpunten. Gedeeld door /onboarding en het venster "je zaak afmaken". --}}
<section data-step="punten" class="step hidden w-full max-w-xl text-center">
    <p class="label">Vaste klanten</p>
    <h2 class="stap-titel">Klanten laten sparen?</h2>
    <p class="lead stap-sub">Klanten sparen automatisch punten met elke bestelling. Die punten leveren straks extra korting op bij het afrekenen.</p>
    <div class="flex flex-col gap-2 text-left">
        <button type="button" data-punten="aan" class="keuze cursor-pointer">
            <span class="rol-icoon"><i class="fa-solid fa-star" aria-hidden="true"></i></span>
            <span class="min-w-0 flex-1"><b>Ja, spaarpunten aan</b><span class="block">Punten bij elke bestelling, korting bij het afrekenen</span></span>
            <span class="tag klein shrink-0">Aanbevolen</span>
        </button>
        <button type="button" data-punten="uit" class="keuze cursor-pointer">
            <span class="rol-icoon"><i class="fa-solid fa-ban" aria-hidden="true"></i></span>
            <span class="min-w-0 flex-1"><b>Nee, liever niet</b><span class="block">Je kunt dit later altijd nog aanzetten</span></span>
        </button>
    </div>
    <div class="stap-knoppen">
        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
        <button data-next type="button" class="btn dark cursor-pointer">Volgende</button>
    </div>
</section>
