{{-- Weet-je-het-zeker-modal in huisstijl, zelfde patroon als de online/offline-modal
     op het dashboard. Elk formulier met data-bevestig gaat automatisch via deze modal:
     data-bevestig="tekst" data-bevestig-titel="..." data-bevestig-icoon="fa-rocket"
     data-bevestig-knop="Ja, doorgaan" en optioneel data-bevestig-bezig="..." voor
     acties die even duren (de knop blokkeert dan tegen dubbelklikken). --}}
<div id="bevestigModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-cacao/60 backdrop-blur-sm">
    <div class="min-h-full flex items-center justify-center p-4 py-10">
        <div class="relative w-full max-w-sm bg-white rounded-2xl border border-crema-dark shadow-2xl p-6 animate-pop text-center">
            <span id="bevestigModalIcoon" class="block text-4xl text-tomato" aria-hidden="true"></span>
            <p id="bevestigModalTitel" class="font-display text-2xl mt-2 mb-1">Weet je het zeker?</p>
            <p id="bevestigModalTekst" class="text-sm font-semibold text-cacao/55 mb-5"></p>
            <div class="flex flex-col gap-2">
                <button type="button" id="bevestigModalJa" class="btn-primary w-full !py-3 cursor-pointer">Ja, doorgaan</button>
                <button type="button" id="bevestigModalNee" class="btn-primary btn-grey w-full !py-2.5 cursor-pointer">Annuleren</button>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('bevestigModal');
        const icoon = document.getElementById('bevestigModalIcoon');
        const titel = document.getElementById('bevestigModalTitel');
        const tekst = document.getElementById('bevestigModalTekst');
        const ja = document.getElementById('bevestigModalJa');
        const nee = document.getElementById('bevestigModalNee');
        let actiefFormulier = null;

        const sluit = () => {
            modal.classList.add('hidden');
            actiefFormulier = null;
        };

        document.querySelectorAll('form[data-bevestig]').forEach((formulier) => {
            formulier.addEventListener('submit', (e) => {
                e.preventDefault();
                actiefFormulier = formulier;
                /* FontAwesome-klasse (fa-...) of anders letterlijke tekst */
                const icoonWaarde = formulier.dataset.bevestigIcoon || 'fa-circle-question';
                if (icoonWaarde.startsWith('fa-')) {
                    icoon.innerHTML = '<i class="fa-solid ' + icoonWaarde + '" aria-hidden="true"></i>';
                } else {
                    icoon.textContent = icoonWaarde;
                }
                titel.textContent = formulier.dataset.bevestigTitel || 'Weet je het zeker?';
                tekst.textContent = formulier.dataset.bevestig || '';
                ja.textContent = formulier.dataset.bevestigKnop || 'Ja, doorgaan';
                ja.disabled = false;
                nee.disabled = false;
                modal.classList.remove('hidden');
            });
        });

        /* form.submit() slaat de submit-listener over, dus geen lus */
        ja.addEventListener('click', () => {
            if (!actiefFormulier) return;
            const bezig = actiefFormulier.dataset.bevestigBezig;
            if (bezig) {
                ja.textContent = bezig;
                ja.disabled = true;
                nee.disabled = true;
            }
            actiefFormulier.submit();
            if (!bezig) sluit();
        });
        nee.addEventListener('click', sluit);
        modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) sluit(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) sluit(); });
    })();
</script>
