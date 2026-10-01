{{-- Stap: openingstijden. Gedeeld door /onboarding en het venster "je zaak afmaken".
     $terug: of er een Terug-knop staat (in het venster is dit de eerste stap). --}}
<section data-step="hours" class="step hidden w-full max-w-xl text-center">
    <p class="label">Openingstijden</p>
    <h2 class="stap-titel">Wanneer kunnen mensen bestellen?</h2>
    <p class="lead stap-sub">Tik de dagen aan dat je open bent.</p>
    <div id="dayChips" class="flex flex-wrap justify-center gap-2 mb-6"></div>

    <div class="flex justify-center mb-6">
        <div class="mode-switch">
            <button type="button" data-hoursmode="same" class="mode-opt cursor-pointer">Elke dag dezelfde tijden</button>
            <button type="button" data-hoursmode="perday" class="mode-opt cursor-pointer">Per dag apart</button>
        </div>
    </div>

    <div id="sameTimes" class="flex items-center justify-center gap-3">
        <span class="small">van</span>
        <input id="inpOpen" type="time" value="16:00" class="inp-time">
        <span class="small">tot</span>
        <input id="inpClose" type="time" value="21:30" class="inp-time">
    </div>
    <div id="perDayTimes" class="hidden space-y-2"></div>
    <p class="err" data-err="hours"></p>
    <div class="stap-knoppen">
        @if($terug ?? true)
            <button data-back type="button" class="btn line cursor-pointer">Terug</button>
        @endif
        <button data-next type="button" class="btn dark cursor-pointer">Volgende</button>
    </div>
</section>
