{{-- Stap: bestel-adres. Gedeeld door /onboarding en het venster "je zaak afmaken". --}}
<section data-step="domain" class="step hidden w-full max-w-xl text-center">
    <p class="label">Jouw plek op het internet</p>
    <h2 class="stap-titel">Kies je bestel-adres</h2>
    <p class="lead stap-sub">Hier komen je klanten straks bestellen.</p>
    <div class="flex flex-col gap-2 text-left">
        <button type="button" data-domain="sub" class="keuze cursor-pointer">
            <span class="rol-icoon"><i class="fa-solid fa-bolt" aria-hidden="true"></i></span>
            <span class="min-w-0 flex-1"><b class="break-all"><span data-slug>jouwzaak</span>.{{ config('app.centraal_domein') }}</b><span class="block">Direct live, je hebt niks nodig</span></span>
        </button>
        <button type="button" data-domain="own" class="keuze cursor-pointer">
            <span class="rol-icoon"><i class="fa-solid fa-house" aria-hidden="true"></i></span>
            <span class="min-w-0 flex-1"><b>Op mijn eigen website</b><span class="block">Bijv. bestellen.jouwdomein.nl, wij koppelen 'm gratis</span></span>
            @auth
                @if(! auth()->user()->abonnement_actief)
                    <span class="tag klein shrink-0">Met abonnement</span>
                @endif
            @endauth
        </button>
    </div>
    <div id="ownDomainWrap" class="hidden mt-5 text-left">
        <div class="fld">
            <label for="inpOwnDomain">Jouw huidige website</label>
            <input id="inpOwnDomain" type="text" placeholder="jouwzaak.nl">
        </div>
        <p id="ownDomainPreview" class="mt-2 status live hidden"><i></i>Wordt: <span class="underline underline-offset-2"></span></p>
    </div>
    <p id="domainCheck" class="mt-4 h-6 small"></p>
    <p class="err" data-err="domain"></p>
    <div class="stap-knoppen">
        <button data-back type="button" class="btn line cursor-pointer">Terug</button>
        <button data-next type="button" class="btn dark cursor-pointer">Volgende</button>
    </div>
</section>
