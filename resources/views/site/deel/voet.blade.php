{{-- Afsluiter en voet in één: het donkere vlak met de laatste duw, de navigatie en de onderbalk --}}
<section class="vlak slide end" id="start" data-naam="Starten">
    <div class="top">
        <a href="/" aria-label="Shop &amp; Eat, naar boven">@include('deel.merk', ['licht' => true])</a>
        <nav aria-label="Voettekst">
            <a href="/">Home</a>
            <a href="/blog">Blog</a>
            <a href="/onboarding">Gratis starten</a>
        </nav>
        <a href="/login" class="btn line klein">Inloggen</a>
    </div>

    <div class="mid">
        <div>
            <span class="tag rv" style="background:transparent;color:rgba(255,255,255,.75);border-color:rgba(255,255,255,.25)">Voor elke keuken in Nederland</span>
            <h2 class="rv" style="--d:.08s;margin-top:14px">Vanavond nog <b>online staan?</b></h2>
            <p class="rv" style="--d:.14s">Binnen tien minuten heb je je eigen bestelpagina. De eerste 30 dagen zijn gratis, zonder betaalgegevens, en je zit nergens aan vast.</p>
        </div>
        <div class="rij rv" style="--d:.2s">
            <a href="/onboarding" class="btn wh">Gratis starten <i class="ar fa-solid fa-chevron-right" aria-hidden="true"></i></a>
        </div>
    </div>

    <div class="bot">
        <span>&copy; {{ date('Y') }} Shop &amp; Eat</span>
        <div>
            <span class="betaal" title="Klanten betalen veilig via Stripe">
                <i class="fa-brands fa-ideal" title="iDEAL" aria-hidden="true"></i>
                <i class="fa-brands fa-cc-apple-pay" title="Apple Pay" aria-hidden="true"></i>
                <i class="fa-brands fa-google-pay" title="Google Pay" aria-hidden="true"></i>
                <i class="fa-brands fa-cc-visa" title="Visa" aria-hidden="true"></i>
                <i class="fa-brands fa-cc-mastercard" title="Mastercard" aria-hidden="true"></i>
            </span>
            <span>Klanten betalen veilig via Stripe</span>
            <span>Gemaakt in Nederland</span>
        </div>
    </div>
</section>
