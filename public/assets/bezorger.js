/* ═══════════════ Teamscherm: bezorgers en keukenhulp ═══════════════ */

/* Dit scherm draait op de telefoon van een teamlid. Het toont alleen de
   bestellingen van de eigen zaak en ververst zichzelf, zodat een bezorger
   onderweg niet hoeft te verversen. */
(() => {
    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => [...document.querySelectorAll(sel)];
    const esc = (str) => String(str ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const ROL = window.PP_ROL || 'bezorger';
    const IS_BEZORGER = ROL === 'bezorger';
    let ORDERS = window.PP_ORDERS || [];
    let vandaag = 0;
    let bevestigId = null;      // bestelling die op een tweede tik wacht
    let bevestigTimer = null;

    const euro = (c) => '€ ' + ((c || 0) / 100).toFixed(2).replace('.', ',');

    const STATUS_LABEL = {
        nieuw: 'Nieuw', geaccepteerd: 'Bevestigd', bereiden: 'Wordt bereid',
        oven: 'In de oven', onderweg: 'Onderweg', bezorgd: 'Bezorgd',
    };

    /* ── Meldingen ────────────────────────────────────────────────── */

    function toast(msg, ms = 2600) {
        const el = $('#toast');
        if (!el) return;
        el.textContent = msg;
        el.classList.add('aan');
        clearTimeout(el._t);
        el._t = setTimeout(() => el.classList.remove('aan'), ms);
    }

    /* ── Hoe lang geleden binnengekomen ───────────────────────────── */

    function tijdGeleden(iso) {
        if (!iso) return '';
        const min = Math.floor((Date.now() - new Date(iso).getTime()) / 60000);
        if (min < 1) return 'net binnen';
        if (min < 60) return `${min} min geleden`;
        const uur = Math.floor(min / 60);
        return `${uur} uur geleden`;
    }

    /* ── Onderdelen van een kaart ─────────────────────────────────── */

    function chip(tekst, stijl = 'background:var(--color-crema-dark)') {
        return `<span class="text-[11px] font-body font-extrabold rounded-full px-2 py-0.5" style="${stijl}">${tekst}</span>`;
    }

    function itemRegels(o) {
        return (o.items || []).map((i) =>
            `<div class="flex items-start gap-2.5 text-sm font-extrabold">
                <span class="shrink-0 w-8 text-center rounded-lg py-0.5 text-xs" style="background:var(--color-crema-dark)">${i.aantal}×</span>
                <span class="flex-1 min-w-0">${esc(i.naam)}</span>
            </div>`
        ).join('');
    }

    /* Navigatie-link die op iedere telefoon de kaart-app opent */
    function navKnop(o) {
        if (o.type !== 'bezorgen' || !o.adres) return '';
        const doel = encodeURIComponent(o.adres);
        return `<a href="https://www.google.com/maps/dir/?api=1&destination=${doel}" target="_blank" rel="noopener"
            class="btn-primary btn-grey !flex items-center justify-center gap-2 !py-3 flex-1">
            <i class="fa-solid fa-diamond-turn-right" aria-hidden="true"></i> Navigeer
        </a>`;
    }

    function opmerkingBlok(o) {
        if (!o.opmerking) return '';
        return `<div class="mt-2.5 rounded-xl px-3 py-2 text-sm font-extrabold flex items-start gap-2" style="background:color-mix(in srgb, var(--color-gold) 16%, #fff)">
            <i class="fa-solid fa-comment-dots mt-0.5" style="color:var(--color-gold)" aria-hidden="true"></i>
            <span class="min-w-0">${esc(o.opmerking)}</span>
        </div>`;
    }

    function adresBlok(o) {
        if (o.type !== 'bezorgen') {
            return `<p class="text-sm font-extrabold mt-2.5 flex items-center gap-2 text-cacao/60">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i> Wordt afgehaald
            </p>`;
        }
        if (!o.adres) return '';
        return `<p class="text-sm font-extrabold mt-2.5 flex items-start gap-2">
            <i class="fa-solid fa-location-dot text-tomato mt-0.5" aria-hidden="true"></i>
            <span class="min-w-0">${esc(o.adres)}</span>
        </p>`;
    }

    /* ── De kaarten per lijst ─────────────────────────────────────── */

    function kaartMijn(o) {
        const bevestigt = bevestigId === o.id;
        return `<article class="rounded-2xl border border-crema-dark bg-white p-4 shadow-[0_1px_2px_rgb(36_23_18_/_.06)]" style="border-color:color-mix(in srgb, var(--color-tomato) 45%, transparent)">
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                <p class="font-display text-2xl">#${o.nummer}</p>
                <p class="font-extrabold flex-1 min-w-0 truncate">${esc(o.klant)}</p>
                <p class="font-display text-xl">${euro(o.totaal)}</p>
            </div>
            <div class="mt-2 space-y-1.5">${itemRegels(o)}</div>
            ${adresBlok(o)}
            ${opmerkingBlok(o)}
            <div class="mt-3.5 flex gap-2.5">
                ${navKnop(o)}
                <button type="button" data-actie="bezorgd" data-id="${o.id}"
                    class="btn-primary !py-3 flex-1 ${bevestigt ? '!bg-basil' : ''}">
                    ${bevestigt ? 'Tik nogmaals' : 'Bezorgd'}
                </button>
            </div>
            <div class="text-center">
                <button type="button" data-actie="vrijgeven" data-id="${o.id}" class="skip-link !mt-2.5 !text-xs">rit teruggeven</button>
            </div>
        </article>`;
    }

    function kaartKlaar(o) {
        return `<article class="rounded-2xl border border-crema-dark bg-white p-4 shadow-[0_1px_2px_rgb(36_23_18_/_.06)]">
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                <p class="font-display text-2xl">#${o.nummer}</p>
                <p class="font-extrabold flex-1 min-w-0 truncate">${esc(o.klant)}</p>
                ${chip(tijdGeleden(o.created_at))}
                <p class="font-display text-xl">${euro(o.totaal)}</p>
            </div>
            <div class="mt-2 space-y-1.5">${itemRegels(o)}</div>
            ${adresBlok(o)}
            ${opmerkingBlok(o)}
            <button type="button" data-actie="oppakken" data-id="${o.id}" class="btn-primary w-full !py-3.5 !text-lg mt-3.5">
                <i class="fa-solid fa-moped" aria-hidden="true"></i> Ik neem deze rit
            </button>
        </article>`;
    }

    /* In de keuken: voor een bezorger informatie, voor de keuken een knop */
    function kaartKeuken(o) {
        const volgende = { geaccepteerd: 'bereiden', bereiden: 'oven' }[o.status];
        const knop = ROL === 'keuken' && volgende
            ? `<button type="button" data-actie="keuken" data-id="${o.id}" class="btn-primary w-full !py-3 mt-3">
                ${volgende === 'bereiden' ? 'Wordt bereid' : 'De oven in'}
               </button>`
            : '';
        const eta = o.eta_minuten ? chip(`± ${o.eta_minuten} min`) : '';
        return `<article class="rounded-2xl border border-crema-dark bg-white p-4">
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                <p class="font-display text-xl">#${o.nummer}</p>
                <p class="font-extrabold flex-1 min-w-0 truncate">${esc(o.klant)}</p>
                ${eta}
                ${chip(STATUS_LABEL[o.status] || o.status)}
            </div>
            <div class="mt-2 space-y-1.5">${itemRegels(o)}</div>
            ${ROL === 'keuken' ? opmerkingBlok(o) : ''}
            ${knop}
        </article>`;
    }

    function kaartCollega(o) {
        return `<article class="rounded-2xl border border-crema-dark bg-white p-4 opacity-60">
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                <p class="font-display text-xl">#${o.nummer}</p>
                <p class="font-extrabold flex-1 min-w-0 truncate">${esc(o.klant)}</p>
                ${chip(esc(o.bezorgerNaam || 'collega'), 'background:var(--color-basil); color:#fff')}
            </div>
            ${adresBlok(o)}
        </article>`;
    }

    function kaartAf(o) {
        return `<div class="flex items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-sm font-extrabold" style="background:var(--color-crema-dark)">
            <i class="fa-solid fa-check text-basil" aria-hidden="true"></i>
            <span class="flex-1 min-w-0 truncate">#${o.nummer} ${esc(o.klant)}</span>
            <span class="text-cacao/50">${euro(o.totaal)}</span>
        </div>`;
    }

    /* ── Alles op het scherm zetten ───────────────────────────────── */

    function vulLijst(id, orders, kaart) {
        const sectie = $(id);
        if (!sectie) return;
        sectie.classList.toggle('hidden', orders.length === 0);
        const meervoud = sectie.querySelector('[data-meervoud]');
        if (meervoud) meervoud.textContent = orders.length === 1 ? '' : 'ten';
        if (orders.length) sectie.querySelector('[data-kaarten]').innerHTML = orders.map(kaart).join('');
    }

    function render() {
        const mijn = ORDERS.filter((o) => o.status === 'onderweg' && o.vanMij);
        const klaar = IS_BEZORGER
            ? ORDERS.filter((o) => o.type === 'bezorgen' && o.status === 'oven' && !o.bezorgerId)
            : [];
        const keuken = ORDERS.filter((o) => ['nieuw', 'geaccepteerd', 'bereiden'].includes(o.status)
            || (o.status === 'oven' && (o.type === 'afhalen' || !IS_BEZORGER)));
        const collega = ORDERS.filter((o) => o.status === 'onderweg' && !o.vanMij);
        const af = ORDERS.filter((o) => o.status === 'bezorgd' && o.vanMij);

        vulLijst('#lijstMijn', mijn, kaartMijn);
        vulLijst('#lijstKlaar', klaar, kaartKlaar);
        vulLijst('#lijstKeuken', keuken, kaartKeuken);
        vulLijst('#lijstCollega', collega, kaartCollega);
        vulLijst('#lijstKlaarVandaag', af, kaartAf);

        const leeg = mijn.length + klaar.length + keuken.length + collega.length === 0;
        $('#leegBlok').classList.toggle('hidden', !leeg);
        $('#vandaagTeller').textContent = vandaag || af.length;
    }

    /* ── Praten met de server ─────────────────────────────────────── */

    async function stuur(url, melding) {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            if (res.status === 401) { window.location.href = '/bezorger/login'; return false; }
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                toast(data.message || 'Dat lukte niet, probeer het zo nog eens.', 3600);
                await ververs();
                return false;
            }
            if (Array.isArray(data.orders)) { ORDERS = data.orders; render(); }
            if (melding) toast(melding);
            return true;
        } catch {
            toast('Geen verbinding. Check je internet.', 3600);
            return false;
        }
    }

    async function ververs() {
        try {
            const res = await fetch('/bezorger/data', { headers: { 'Accept': 'application/json' } });
            if (res.status === 401) { window.location.href = '/bezorger/login'; return; }
            if (!res.ok) return;
            const data = await res.json();
            ORDERS = data.orders || [];
            vandaag = data.vandaag || 0;
            render();
        } catch { /* even geen verbinding: bij de volgende ronde weer proberen */ }
    }

    /* ── Tikken ───────────────────────────────────────────────────── */

    function resetBevestiging() {
        clearTimeout(bevestigTimer);
        bevestigId = null;
    }

    document.addEventListener('click', async (e) => {
        const knop = e.target.closest('[data-actie]');
        if (!knop) {
            /* Ergens anders tikken annuleert een openstaande bevestiging */
            if (bevestigId !== null) { resetBevestiging(); render(); }
            return;
        }
        const id = Number(knop.dataset.id);

        if (knop.dataset.actie === 'oppakken') {
            knop.disabled = true;
            await stuur(`/bezorger/bestellingen/${id}/oppakken`, 'De rit staat op jouw naam');
            return;
        }
        if (knop.dataset.actie === 'vrijgeven') {
            await stuur(`/bezorger/bestellingen/${id}/vrijgeven`, 'Rit teruggegeven');
            return;
        }
        if (knop.dataset.actie === 'keuken') {
            knop.disabled = true;
            await stuur(`/bezorger/bestellingen/${id}/keuken`, 'Doorgezet');
            return;
        }
        if (knop.dataset.actie === 'bezorgd') {
            /* Twee tikken, zodat je hem niet per ongeluk in je broekzak afvinkt */
            if (bevestigId !== id) {
                resetBevestiging();
                bevestigId = id;
                bevestigTimer = setTimeout(() => { bevestigId = null; render(); }, 5000);
                render();
                return;
            }
            resetBevestiging();
            knop.disabled = true;
            await stuur(`/bezorger/bestellingen/${id}/bezorgd`, 'Netjes, bezorgd');
        }
    });

    /* ── Draaien ──────────────────────────────────────────────────── */

    render();
    ververs();
    setInterval(ververs, 12000);
    setInterval(render, 60000);     /* "x min geleden" actueel houden */
    document.addEventListener('visibilitychange', () => { if (!document.hidden) ververs(); });
})();
