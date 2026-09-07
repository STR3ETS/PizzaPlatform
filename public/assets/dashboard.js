/* ═══════════════ Dashboard: app-gevoel ═══════════════ */

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => [...document.querySelectorAll(sel)];
const DATA = window.PP_DATA || {};
const esc = (str) => String(str).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* ── Begroeting op basis van het moment van de dag ────────────── */

(function dagregel() {
    const datum = new Date().toLocaleDateString('nl-NL', { weekday: 'long', day: 'numeric', month: 'long' });
    $('#dagregel').textContent = `Het is vandaag ${datum}.`;
})();

/* ── Panelen wisselen (client-side, als een app) ──────────────── */

$$('[data-nav]').forEach((btn) => btn.addEventListener('click', () => {
    const doel = btn.dataset.nav;
    $$('[data-nav]').forEach((b) => b.classList.toggle('actief', b.dataset.nav === doel));
    $$('[data-panel]').forEach((p) => {
        const actief = p.dataset.panel === doel;
        p.classList.remove('actief');
        if (actief) requestAnimationFrame(() => p.classList.add('actief'));
    });
    /* Paneel in de url bewaren (/dashboard/bestellingen), zodat een refresh op hetzelfde scherm uitkomt */
    history.replaceState(null, '', doel === 'overzicht' ? '/dashboard' : '/dashboard/' + doel);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}));

/* Toast midden onderin het scherm, in de stijl van de onboarding */
function dashToast(msg, ms = 3200) {
    const el = $('#dashToast');
    if (!el) return;
    el.textContent = msg;
    el.classList.add('aan');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('aan'), ms);
}

/* Zaak nog niet af: panelen zichtbaar op slot, en elke klik op een knop of link
   geeft een toast. Alleen de weg naar het afmaken zelf blijft open. */
if (window.PP_SETUP === false) {
    $$('[data-nav]').forEach((b) => { if (b.dataset.nav !== 'overzicht') b.classList.add('opacity-40'); });
    $$('.qa-btn').forEach((b) => b.classList.add('opacity-40'));
    const online = $('#onlineToggle');
    if (online) {
        online.classList.add('opacity-40', 'cursor-not-allowed');
        online.title = 'Maak eerst je zaak af, daarna kun je online';
    }

    document.addEventListener('click', (e) => {
        const doel = e.target.closest('a, button');
        if (!doel) return;
        /* Wat wel mag: de setup-banner, onboarding-links en checklist-stappen,
           de uitlegvideo, de abonnement-overlay, abonneren en uitloggen */
        if (doel.closest('#setupBanner') || doel.closest('#introModal') || doel.closest('#abonnementOverlay')) return;
        /* De snelknoppen onder "Snel regelen" zijn allemaal op slot, ook de
           twee die naar de wizard linken: afmaken gaat via de banner */
        if (!doel.classList.contains('qa-btn')) {
            if (doel.dataset.nav === 'overzicht' || doel.dataset.stap) return;
            if ((doel.getAttribute('href') || '').includes('/onboarding')) return;
            const formActie = doel.closest('form')?.action || '';
            if (formActie.includes('logout') || formActie.includes('abonnement')) return;
        }
        e.preventDefault();
        e.stopPropagation();
        dashToast('Maak eerst je zaak af via "Verder met instellen".');
    }, true);
}

/* ── Tellers en weekgrafiek ───────────────────────────────────── */

$$('[data-teller]').forEach((el) => {
    const doel = Number(el.dataset.teller) || 0;
    if (!doel) return;
    let t = 0;
    const stap = Math.max(1, Math.round(doel / 30));
    const timer = setInterval(() => {
        t = Math.min(doel, t + stap);
        el.textContent = t;
        if (t >= doel) clearInterval(timer);
    }, 30);
});

/* ── Schatting drukte per uur ─────────────────────────────────────
   Zodra er echte bestellingen zijn, vullen we `echteDrukte` met het
   gemiddelde aantal bestellingen per uur over alle dagen. Tot die
   tijd tonen we het typische pizzeria-patroon (piek rond etenstijd),
   binnen de openingstijden van vandaag. */

(function renderDrukte() {
    const chart = $('#drukteChart');
    if (!chart) return;

    const echteDrukte = null; // straks: { 16: 2.1, 17: 5.4, ... } uit de database

    const DAGKEYS = ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'];
    const dagKey = DAGKEYS[new Date().getDay()];
    const openVandaag = DATA.days ? !!DATA.days[dagKey] : true;
    const tijd = (DATA.hoursMode === 'perday' && DATA.dayTimes && DATA.dayTimes[dagKey])
        ? DATA.dayTimes[dagKey]
        : { open: DATA.open || '16:00', close: DATA.close || '21:30' };

    /* Slots per half uur binnen de openingstijden */
    const parseT = (s, fb) => {
        const [h, m] = String(s).split(':').map(Number);
        return (isNaN(h) ? fb : h) + ((m || 0) >= 30 ? 0.5 : 0);
    };
    let van = parseT(tijd.open, 16);
    let tot = parseT(tijd.close, 21.5);
    if (tot <= van) { van = 16; tot = 21.5; }

    const slots = [];
    for (let t = van; t < tot; t += 0.5) slots.push(t);

    /* Typisch patroon: dinerpiek rond 18:00, kleine lunchpiek rond 12:30 */
    const schatting = (t) => {
        const diner = Math.exp(-Math.pow(t - 18, 2) / 5);
        const lunch = 0.45 * Math.exp(-Math.pow(t - 12.5, 2) / 2);
        return Math.max(12, Math.round(100 * Math.max(diner, lunch)));
    };

    const fmt = (t) => `${Math.floor(t)}:${t % 1 ? '30' : '00'}`;
    const waarden = slots.map((t) => ({ uur: t, pct: echteDrukte ? Math.round(echteDrukte[t] || 0) : schatting(t) }));
    const max = Math.max(...waarden.map((w) => w.pct), 1);
    const nu = new Date();
    const nuSlot = nu.getHours() + (nu.getMinutes() >= 30 ? 0.5 : 0);

    chart.innerHTML = waarden.map((w) => {
        const rel = w.pct / max;
        const klasse = rel > 0.75 ? 'hoog' : rel > 0.4 ? 'middel' : 'laag';
        const label = rel > 0.75 ? 'druk' : rel > 0.4 ? 'gemiddeld' : 'rustig';
        const isNu = openVandaag && w.uur === nuSlot;
        /* Tijdlijn toont alleen hele uren; het "nu"-pilletje krijgt wel de exacte tijd */
        const uurLabel = w.uur % 1 ? (isNu ? fmt(w.uur) : '') : String(Math.floor(w.uur));
        return `<div class="drukte-kolom ${isNu ? 'nu' : ''}" title="${fmt(w.uur)} wordt ${label}">
            <div class="d-balkvak"><div class="drukte-bar ${klasse}" data-hoogte="${Math.round(rel * 100)}"></div></div>
            <span class="d-uur">${uurLabel}</span>
        </div>`;
    }).join('');

    requestAnimationFrame(() => requestAnimationFrame(() => {
        $$('#drukteChart .drukte-bar').forEach((bar) => { bar.style.height = Math.max(6, bar.dataset.hoogte) + '%'; });
    }));

    const piek = waarden.reduce((a, b) => (b.pct > a.pct ? b : a));
    $('#drukteBadge').textContent = openVandaag ? 'Vandaag' : 'Gemiddeld';
    $('#drukteSub').textContent = openVandaag
        ? `Verwachte piek rond ${fmt(piek.uur)}`
        : `Vandaag ben je gesloten, dit is je gemiddelde dag`;
})();

/* ── Checklist "maak je zaak compleet" op basis van echte data ── */

const CHECKS = [
    { label: 'Menukaart gevuld (3+ gerechten)', emoji: '<i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>', af: (window.PP_MENU || []).length >= 3, paneel: 'menukaart' },
    { label: 'Logo geüpload', emoji: '<i class="fa-solid fa-image" aria-hidden="true"></i>', af: !!DATA.logo, stap: 'style' },
    { label: 'Bedrijfsgegevens compleet', emoji: '<i class="fa-solid fa-address-card" aria-hidden="true"></i>', af: !!DATA.kvk && !!DATA.street, inst: 'bedrijf' },
    { label: 'Telefoonnummer toegevoegd', emoji: '<i class="fa-solid fa-phone" aria-hidden="true"></i>', af: !!DATA.phone, inst: 'zaak' },
];

(function renderChecklist() {
    const lijst = $('#checklist');
    if (!lijst) return;
    lijst.innerHTML = CHECKS.map((c) =>
        `<button type="button" class="check-item ${c.af ? 'af' : ''}" data-stap="${c.stap || ''}" data-paneel="${c.paneel || ''}" data-inst="${c.inst || ''}">
            <span class="c-dot"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
            <span>${c.emoji}</span>
            <span class="c-lbl">${c.label}</span>
        </button>`
    ).join('');
    lijst.addEventListener('click', (e) => {
        const item = e.target.closest('.check-item');
        if (!item) return;
        if (item.dataset.paneel) document.querySelector(`.rail-item[data-nav="${item.dataset.paneel}"]`)?.click();
        else if (item.dataset.inst) return; /* de algemene [data-inst] handler opent de popup */
        else if (item.dataset.stap) window.location.href = `/onboarding?stap=${item.dataset.stap}`;
    });

    const klaar = CHECKS.filter((c) => c.af).length;
    const pct = Math.round((klaar / CHECKS.length) * 100);
    const OMTREK = 163.4;
    setTimeout(() => { $('#ringBar').style.strokeDashoffset = OMTREK - (OMTREK * pct) / 100; }, 350);

    let t = 0;
    const timer = setInterval(() => {
        t = Math.min(pct, t + 3);
        $('#ringPct').textContent = t + '%';
        if (t >= pct) clearInterval(timer);
    }, 32);
})();

/* ── Samenvattingen uit de onboarding-data ────────────────────── */

(function samenvattingen() {
    if (DATA.days) {
        const namen = { ma: 'ma', di: 'di', wo: 'wo', do: 'do', vr: 'vr', za: 'za', zo: 'zo' };
        const open = Object.keys(namen).filter((d) => DATA.days[d]);
        if (open.length) {
            const label = open.length === 7 ? 'elke dag' : open.map((d) => namen[d]).join(', ');
            $('#tijdenSamenvatting').textContent = DATA.hoursMode === 'perday'
                ? `Open op ${label}, met eigen tijden per dag.`
                : `Open op ${label}, van ${DATA.open || '16:00'} tot ${DATA.close || '21:30'}.`;
        }

        /* Per dag uitgeschreven, voor het instellingen-paneel */
        const DAGNAMEN = { ma: 'Maandag', di: 'Dinsdag', wo: 'Woensdag', do: 'Donderdag', vr: 'Vrijdag', za: 'Zaterdag', zo: 'Zondag' };
        const lijst = $('#tijdenLijst');
        if (lijst) {
            lijst.innerHTML = Object.keys(DAGNAMEN).map((d) => {
                const tijd = (DATA.hoursMode === 'perday' && DATA.dayTimes && DATA.dayTimes[d])
                    ? DATA.dayTimes[d]
                    : { open: DATA.open || '16:00', close: DATA.close || '21:30' };
                return `<div class="flex justify-between gap-3">
                    <span class="text-cacao/45">${DAGNAMEN[d]}</span>
                    <span>${DATA.days[d] ? `${tijd.open} - ${tijd.close}` : 'Gesloten'}</span>
                </div>`;
            }).join('');
        }
    }

    const THEMA_NAMEN = { template1: 'Presto', template2: 'Notte', template3: 'Forza', template4: 'Giro', fresco: 'Fresco', nero: 'Nero', napoli: 'Napoli', puro: 'Puro', blocco: 'Blocco', retro: 'Retro' };
    /* Bij een vast palet tonen we de echte hoofdkleur (bruin wordt bijvoorbeeld karamelbruin) */
    if (DATA.theme) {
        const kleur = (window.PP_KLEUREN || []).find((k) => k.hex === DATA.color)?.palet?.primair || DATA.color || '#B04A3F';
        $('#paginaSamenvatting').innerHTML = `Template: <b>${THEMA_NAMEN[DATA.theme] || 'Presto'}</b>${DATA.logo ? ', met logo' : ''}. Jouw kleur: <span class="inline-block w-3.5 h-3.5 rounded-full align-middle" style="background:${esc(kleur)}"></span>`;
    }
})();

/* ── Bestellingen (echt uit de database, touch-first) ─────────── */

let ORDERS = (window.PP_ORDERS || []).map((o) => ({ ...o }));
let orderFilter = 'alles';

const STATUSSEN = ['nieuw', 'geaccepteerd', 'bereiden', 'oven', 'onderweg', 'bezorgd'];

/* Font Awesome solid-iconen voor de status-tijdlijn */
const ICONEN = {
    nieuw: '<i class="fa-solid fa-euro-sign" aria-hidden="true"></i>',
    geaccepteerd: '<i class="fa-solid fa-thumbs-up" aria-hidden="true"></i>',
    bereiden: '<i class="fa-solid fa-utensils" aria-hidden="true"></i>',
    oven: '<i class="fa-solid fa-fire" aria-hidden="true"></i>',
    onderweg: '<i class="fa-solid fa-bicycle" aria-hidden="true"></i>',
    bezorgd: '<i class="fa-solid fa-flag" aria-hidden="true"></i>',
};

/* De interne statuswaarden blijven gelijk, alleen de labels verschillen:
   Betaald -> Bevestigd (met tijdsindicatie) -> Wordt bereid -> In de oven -> Onderweg -> Bezorgd */
const STATUS_INFO = {
    nieuw:        { label: 'Betaald',      icoon: ICONEN.nieuw,        chip: 'background:var(--color-gold); color:var(--color-cacao)', actie: 'Bevestigen',   volgende: 'geaccepteerd' },
    geaccepteerd: { label: 'Bevestigd',    icoon: ICONEN.geaccepteerd, chip: 'background:#2563EB; color:#fff',                         actie: 'Wordt bereid', volgende: 'bereiden' },
    bereiden:     { label: 'Wordt bereid', icoon: ICONEN.bereiden,     chip: 'background:#F97316; color:#fff',                         actie: 'In de oven',   volgende: 'oven' },
    oven:         { label: 'In de oven',   icoon: ICONEN.oven,         chip: 'background:var(--color-tomato); color:#fff',             actie: 'Onderweg',     volgende: 'onderweg' },
    onderweg:     { label: 'Onderweg',     icoon: ICONEN.onderweg,     chip: 'background:var(--color-basil); color:#fff',              actie: 'Bezorgd',      volgende: 'bezorgd' },
    bezorgd:      { label: 'Bezorgd',      icoon: ICONEN.bezorgd,      chip: 'background:var(--color-cacao); color:#fff',              actie: null,           volgende: null },
};

/* Afhalen kent geen bezorgtraject: de laatste stappen heten en ogen anders */
const ICONEN_AFHALEN = {
    ...ICONEN,
    onderweg: '<i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>',
};
const STATUS_INFO_AFHALEN = {
    ...STATUS_INFO,
    oven:     { ...STATUS_INFO.oven, actie: 'Af te halen' },
    onderweg: { label: 'Af te halen', icoon: ICONEN_AFHALEN.onderweg, chip: 'background:var(--color-basil); color:#fff', actie: 'Afgehaald', volgende: 'bezorgd' },
    bezorgd:  { label: 'Afgehaald', icoon: ICONEN.bezorgd, chip: 'background:var(--color-cacao); color:#fff', actie: null, volgende: null },
};

const iconenVoor = (o) => (o.type === 'afhalen' ? ICONEN_AFHALEN : ICONEN);
const infoVoor = (o, status = o.status) => (o.type === 'afhalen' ? STATUS_INFO_AFHALEN : STATUS_INFO)[status] || STATUS_INFO.nieuw;

/* Tijdsschatting bij accepteren */
let etaOpen = null;      // order-id waarvoor de kiezer open staat
let etaWaarde = 75;      // eigen invoer, in stappen van 15 minuten
const euro = (c) => '€ ' + (c / 100).toFixed(2).replace('.', ',');

function postJson(url, body, method = 'POST') {
    return fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: body ? JSON.stringify(body) : undefined,
    });
}

function tijdGeleden(iso) {
    const min = Math.max(0, Math.round((Date.now() - new Date(iso).getTime()) / 60000));
    if (min < 1) return 'zojuist';
    if (min < 60) return `${min} min geleden`;
    return `${Math.floor(min / 60)} uur geleden`;
}

function typeChip(o) {
    const label = o.type === 'afhalen'
        ? '<i class="fa-solid fa-box-open" aria-hidden="true"></i> Afhalen'
        : '<i class="fa-solid fa-moped" aria-hidden="true"></i> Bezorgen';
    return `<span class="text-[11px] font-body font-extrabold rounded-full px-2 py-0.5" style="background:var(--color-crema-dark)">${label}</span>`;
}

/* Compacte rij voor het overzicht */
function orderRij(o) {
    const info = infoVoor(o);
    if (!info || !info.actie) return '';
    return `<div class="order-card flex flex-col sm:flex-row sm:items-center gap-2 rounded-2xl px-3.5 py-2.5" data-id="${o.id}" style="background:var(--color-crema)">
        <div class="flex-1 min-w-0">
            <p class="font-display text-base">#${o.nummer} voor ${esc(o.klant)}
                <span class="text-[11px] font-body font-extrabold rounded-full px-2 py-0.5 ml-1 align-middle" style="${info.chip}">${info.label}</span>
                <span class="ml-1 align-middle inline-block">${typeChip(o)}</span></p>
            <p class="text-xs font-extrabold text-cacao/50 truncate">${(o.items || []).map((i) => i.aantal + '× ' + esc(i.naam)).join(', ')}</p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <span class="font-display">${euro(o.totaal)}</span>
            <button type="button" class="order-actie btn-primary !text-sm !px-4 !py-2" data-order="${o.id}" data-volgende="${info.volgende}">${info.actie}</button>
        </div>
    </div>`;
}

/* Grote touch-kaart voor het bestellingen-paneel */
function orderKaart(o) {
    const info = infoVoor(o);
    const idx = STATUSSEN.indexOf(o.status);
    const klaar = o.status === 'bezorgd';
    /* De huidige status is behaald (vinkje); de stap erna is waar de pizzeria mee bezig is (laad-rondje) */
    const tijdlijn = STATUSSEN.map((s, i) => {
        const si = infoVoor(o, s);
        let st = '';
        let inhoud = si.icoon;   // toekomstige stap: grijs status-icoon
        if (i <= idx || klaar) {
            st = 'geweest';      // behaald: groen met vinkje
            inhoud = '<i class="fa-solid fa-check" aria-hidden="true"></i>';
        } else if (i === idx + 1) {
            st = 'nu';           // hiermee bezig: laad-rondje
            inhoud = '<i class="fa-solid fa-circle-notch fa-spin" aria-hidden="true"></i>';
        }
        return `<div class="stl-stap ${st}">
            <div class="stl-dot">${inhoud}</div>
            <span class="stl-lbl">${si.label}</span>
        </div>`;
    }).join('');
    const etaChip = o.eta_minuten && o.status !== 'bezorgd'
        ? `<span class="text-[11px] font-body font-extrabold rounded-full px-2 py-0.5" style="background:var(--color-crema-dark)"><i class="fa-solid fa-clock" aria-hidden="true"></i> ± ${o.eta_minuten} min</span>`
        : '';
    /* Spaarpunten: wat deze klant spaart of inwisselt, zichtbaar voor de pizzeria */
    const puntenDelen = [];
    if (o.punten > 0) puntenDelen.push(`+${o.punten} gespaard`);
    if (o.punten_gebruikt > 0) puntenDelen.push(`${o.punten_gebruikt} ingewisseld`);
    const puntenChip = puntenDelen.length
        ? `<span class="text-[11px] font-body font-extrabold rounded-full px-2 py-0.5" style="background:var(--color-crema-dark)"><i class="fa-solid fa-star" style="color:var(--color-gold)" aria-hidden="true"></i> ${puntenDelen.join(', ')}</span>`
        : '';

    /* Bij accepteren eerst een tijdsschatting kiezen */
    const kiezer = etaOpen === o.id && o.status === 'nieuw' ? `
        <div class="eta-kiezer">
            <p class="font-extrabold text-sm text-cacao/60 mb-2.5 text-center">${o.type === 'afhalen' ? 'Over hoe lang kan het ongeveer afgehaald worden?' : 'Hoe lang gaat dit ongeveer duren?'}</p>
            <div class="grid grid-cols-4 gap-2 mb-3">
                ${[15, 30, 45, 60].map((m) => `<button type="button" class="eta-snel" data-eta-snel="${m}" data-order="${o.id}">${m} min</button>`).join('')}
            </div>
            <div class="flex items-center justify-center gap-4 mb-3">
                <button type="button" class="eta-stap" data-eta-min aria-label="15 minuten minder">−</button>
                <span class="font-display text-2xl w-28 text-center">${etaWaarde} min</span>
                <button type="button" class="eta-stap" data-eta-plus aria-label="15 minuten meer">+</button>
            </div>
            <button type="button" class="btn-primary w-full !text-lg !py-3" data-eta-bevestig data-order="${o.id}">Bevestigen, ± ${etaWaarde} min</button>
            <div class="text-center"><button type="button" class="skip-link !mt-2" data-eta-sluit>annuleren</button></div>
        </div>` : '';

    /* Wijzigingen per gerecht: extra's in groen, weglatingen in rood */
    const optieRegel = (op) => {
        const zonder = op.type === 'zonder';
        return `<p class="order-optie text-xs font-extrabold ${zonder ? 'text-tomato' : 'text-basil'}">
            <i class="fa-solid ${zonder ? 'fa-minus' : 'fa-plus'} !text-[10px] w-3" aria-hidden="true"></i>${esc(op.naam)}${op.prijs ? ` <span class="text-cacao/40">${euro(op.prijs)}</span>` : ''}
        </p>`;
    };
    const itemRegels = (o.items || []).map((i) => {
        const regelTotaal = (i.prijs + (i.opties || []).reduce((som, op) => som + (op.prijs || 0), 0)) * (i.aantal || 1);
        return `<div class="order-item flex items-start gap-2.5 text-sm font-extrabold">
            <span class="shrink-0 w-8 text-center rounded-lg py-0.5 text-xs" style="background:var(--color-crema-dark)">${i.aantal || 1}×</span>
            <div class="flex-1 min-w-0">
                <p class="truncate">${esc(i.naam)}</p>
                ${(i.opties || []).map(optieRegel).join('')}
            </div>
            <span class="shrink-0 text-cacao/55">${euro(regelTotaal)}</span>
        </div>`;
    }).join('');
    const adres = o.type === 'bezorgen' && o.adres
        ? `<p class="order-adres text-sm font-extrabold mt-2.5 flex items-center gap-2"><i class="fa-solid fa-location-dot text-tomato" aria-hidden="true"></i>${esc(o.adres)}</p>`
        : '';
    const opmerking = o.opmerking
        ? `<div class="order-opmerking mt-2.5 rounded-xl px-3 py-2 text-sm font-extrabold flex items-start gap-2" style="background:color-mix(in srgb, var(--color-gold) 16%, #fff)">
            <i class="fa-solid fa-comment-dots mt-0.5" style="color:var(--color-gold)" aria-hidden="true"></i>
            <span class="min-w-0">${esc(o.opmerking)}</span>
        </div>`
        : '';

    return `<div class="order-kaart dash-card !p-4 sm:!p-5 order-card ${o.status === 'bezorgd' ? 'opacity-60' : ''}" data-id="${o.id}">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
            <p class="font-display text-2xl">#${o.nummer}</p>
            <p class="font-extrabold text-lg flex-1 min-w-0 truncate">${esc(o.klant)}</p>
            ${etaChip}
            ${puntenChip}
            ${typeChip(o)}
            <p class="font-display text-2xl">${euro(o.totaal)}</p>
        </div>
        <div class="mt-2.5 space-y-1.5">${itemRegels}</div>
        ${adres}
        ${opmerking}
        <p class="text-xs font-extrabold text-cacao/40 mt-2 mb-3">${tijdGeleden(o.created_at)}</p>
        <div class="status-tijdlijn">${tijdlijn}</div>
        ${kiezer}
        ${info.volgende && !kiezer ? `<button type="button" class="order-actie btn-primary w-full !text-lg !py-3.5 !flex items-center justify-center gap-2.5" data-order="${o.id}" data-volgende="${info.volgende}">
            ${info.label}
            <i class="fa-solid fa-arrow-right !text-base" aria-hidden="true"></i>
            ${infoVoor(o, info.volgende).label}
        </button>` : ''}
    </div>`;
}

const TAB_KLEUREN = {
    alles: ['var(--color-cacao)', '#fff'],
    nieuw: ['var(--color-gold)', 'var(--color-cacao)'],
    geaccepteerd: ['#2563EB', '#fff'],
    bereiden: ['#F97316', '#fff'],
    oven: ['var(--color-tomato)', '#fff'],
    onderweg: ['var(--color-basil)', '#fff'],
    bezorgd: ['var(--color-cacao)', '#fff'],
};

function orderTabs() {
    const el = $('#orderTabs');
    if (!el) return;
    /* In de lijst staan bezorgingen en afhalers door elkaar, dus de laatste twee tabs heten neutraal */
    const labels = { alles: 'Alles', nieuw: 'Betaald', geaccepteerd: 'Bevestigd', bereiden: 'Wordt bereid', oven: 'In de oven', onderweg: 'Onderweg / klaar', bezorgd: 'Afgerond' };
    el.innerHTML = Object.keys(labels).map((f) => {
        const actief = orderFilter === f;
        const aantal = f === 'alles' ? ORDERS.length : ORDERS.filter((o) => o.status === f).length;
        const [kleur, tekst] = TAB_KLEUREN[f];
        return `<button type="button" class="order-tab" data-filter="${f}"
            style="${actief ? `background:${kleur}; border-color:${kleur}; color:${tekst};` : ''}">${labels[f]} <span class="opacity-60">${aantal}</span></button>`;
    }).join('');
}

function werkTellersBij() {
    const open = ORDERS.filter((o) => o.status !== 'bezorgd');
    $('#openLeeg')?.classList.toggle('hidden', open.length > 0);
    const badge = $('#railOrdersBadge');
    if (badge) {
        badge.textContent = open.length;
        badge.style.display = open.length ? '' : 'none';
    }
    const topbalkOrders = $('#topbalkOrders');
    if (topbalkOrders) topbalkOrders.textContent = open.length + ' openstaand';
    orderTabs();
    const zichtbaar = document.querySelectorAll('#orderLijst .order-kaart').length;
    $('#ordersLeeg')?.classList.toggle('hidden', zichtbaar > 0);
    const leegTekst = $('#ordersLeegTekst');
    if (leegTekst) {
        leegTekst.textContent = ORDERS.length === 0
            ? 'Zodra je live staat, komen je bestellingen hier realtime binnen. Probeer het alvast met een voorbeeld.'
            : 'Geen bestellingen met deze status.';
    }
}

/* Volledige render: alleen bij laden, filterwissel en de tijd-verversing */
function renderOrders() {
    const open = ORDERS.filter((o) => o.status !== 'bezorgd');
    if ($('#openOrders')) $('#openOrders').innerHTML = open.map(orderRij).join('');
    if ($('#orderLijst')) {
        const lijst = orderFilter === 'alles' ? [...open, ...ORDERS.filter((o) => o.status === 'bezorgd')] : ORDERS.filter((o) => o.status === orderFilter);
        $('#orderLijst').innerHTML = lijst.map(orderKaart).join('');
    }
    werkTellersBij();
}

/* Bij een actie alleen de betreffende bestelling bijwerken, de rest blijft staan */
function werkOrderBij(order, opts = {}) {
    const rijHtml = orderRij(order);
    const rij = document.querySelector(`#openOrders [data-id="${order.id}"]`);
    if (rij) {
        if (rijHtml) rij.outerHTML = rijHtml;
        else rij.remove();
    } else if (rijHtml && $('#openOrders')) {
        $('#openOrders').insertAdjacentHTML('afterbegin', rijHtml);
    }

    if ($('#orderLijst')) {
        const hoortErbij = orderFilter === 'alles' || order.status === orderFilter;
        const kaartHtml = hoortErbij ? orderKaart(order) : '';
        const kaart = document.querySelector(`#orderLijst [data-id="${order.id}"]`);
        if (kaart) {
            if (kaartHtml) kaart.outerHTML = kaartHtml;
            else kaart.remove();
        } else if (kaartHtml) {
            $('#orderLijst').insertAdjacentHTML('afterbegin', kaartHtml);
        }
    }

    if (opts.pop) document.querySelectorAll(`[data-id="${order.id}"]`).forEach((el) => el.classList.add('pop'));

    /* Statuswissel: het balkje netjes laten vollopen in plaats van verspringen */
    if (opts.wissel) {
        const kaart = document.querySelector(`#orderLijst [data-id="${order.id}"]`);
        if (kaart) {
            const stappen = [...kaart.querySelectorAll('.stl-stap')];
            const nuIdx = stappen.findIndex((s) => s.classList.contains('nu'));
            if (nuIdx > 0) {
                stappen[nuIdx - 1].classList.add('vul');
                stappen[nuIdx].classList.add('vul-in');
                /* Oude grijze icoon laten staan tot het balkje aankomt */
                stappen[nuIdx].querySelector('.stl-dot').insertAdjacentHTML('afterbegin', `<span class="stl-was">${iconenVoor(order)[STATUSSEN[nuIdx]]}</span>`);
            } else if (nuIdx === -1 && stappen.length > 1) {
                /* Laatste stap: de lijn was al groen (laad-rondje), alleen het rondje wisselt naar een vinkje */
                const laatste = stappen[stappen.length - 1];
                laatste.classList.add('vul-eind');
                laatste.querySelector('.stl-dot').insertAdjacentHTML('afterbegin', '<span class="stl-was"><i class="fa-solid fa-circle-notch fa-spin" aria-hidden="true"></i></span>');
                /* Doorzichtig worden wacht tot het vinkje er staat; de animatie wint van opacity-60 */
                kaart.classList.add('klaar-vervaag');
            }
            /* Na afloop de animatieklassen opruimen, anders blokkeren ze de fa-spin van het laad-icoon */
            setTimeout(() => {
                stappen.forEach((s) => {
                    s.classList.remove('vul', 'vul-in', 'vul-eind');
                    s.querySelectorAll('.stl-was').forEach((w) => w.remove());
                });
            }, 950);
        }
    }
    werkTellersBij();
}

/* Plus/min in de tijdkiezer: alleen de twee tekstjes aanpassen */
function werkEtaTekstBij() {
    const kaart = document.querySelector(`#orderLijst [data-id="${etaOpen}"]`);
    if (!kaart) return;
    const span = kaart.querySelector('.eta-kiezer .font-display');
    if (span) span.textContent = `${etaWaarde} min`;
    const knop = kaart.querySelector('[data-eta-bevestig]');
    if (knop) knop.textContent = `Bevestigen, ± ${etaWaarde} min`;
}

function accepteer(order, eta) {
    order.status = 'geaccepteerd';
    order.eta_minuten = eta;
    etaOpen = null;
    werkOrderBij(order, { wissel: true });
    postJson(`/bestellingen/${order.id}/status`, { status: 'geaccepteerd', eta }).catch(() => {});
}

document.addEventListener('click', (e) => {
    const orderVan = (el) => ORDERS.find((o) => o.id === Number(el.dataset.order));

    /* Tijdsschatting-kiezer */
    const snel = e.target.closest('[data-eta-snel]');
    if (snel) { const o = orderVan(snel); if (o) accepteer(o, Number(snel.dataset.etaSnel)); return; }
    if (e.target.closest('[data-eta-min]')) { etaWaarde = Math.max(15, etaWaarde - 15); werkEtaTekstBij(); return; }
    if (e.target.closest('[data-eta-plus]')) { etaWaarde = Math.min(240, etaWaarde + 15); werkEtaTekstBij(); return; }
    const bevestig = e.target.closest('[data-eta-bevestig]');
    if (bevestig) { const o = orderVan(bevestig); if (o) accepteer(o, etaWaarde); return; }
    if (e.target.closest('[data-eta-sluit]')) {
        const o = ORDERS.find((x) => x.id === etaOpen);
        etaOpen = null;
        if (o) werkOrderBij(o);
        return;
    }

    /* Grote actieknop: door naar de volgende status (alleen vooruit) */
    const btn = e.target.closest('.order-actie');
    if (!btn) return;
    const order = orderVan(btn);
    if (!order) return;
    if (order.status === 'nieuw') {
        /* Eerst een tijdsschatting kiezen; vanaf het overzicht springen we naar het bestellingen-paneel */
        etaOpen = order.id;
        etaWaarde = 75;
        if (!btn.closest('#orderLijst')) document.querySelector('[data-nav="bestellingen"].rail-item')?.click();
        werkOrderBij(order);
        return;
    }
    order.status = btn.dataset.volgende;
    werkOrderBij(order, { wissel: true });
    postJson(`/bestellingen/${order.id}/status`, { status: order.status }).catch(() => {});
});

$('#orderTabs')?.addEventListener('click', (e) => {
    const tab = e.target.closest('[data-filter]');
    if (!tab) return;
    orderFilter = tab.dataset.filter;
    renderOrders();
});

$('#demoOrderBtn')?.addEventListener('click', async () => {
    try {
        const res = await postJson('/dashboard/demo-bestelling');
        if (!res.ok) return;
        const order = await res.json();
        ORDERS.unshift(order);
        werkOrderBij(order, { pop: true });

        /* Statistieken van vandaag live bijwerken */
        const aantal = $('#statAantal');
        if (aantal) aantal.textContent = Number(aantal.textContent || 0) + 1;
        const omzet = $('#statOmzet');
        if (omzet) {
            omzet.dataset.cents = Number(omzet.dataset.cents || 0) + order.totaal;
            omzet.textContent = euro(Number(omzet.dataset.cents));
        }
    } catch { /* geen verbinding */ }
});

renderOrders();
setInterval(renderOrders, 30000);   // "x min geleden" actueel houden

/* ── Tip-mascotte ─────────────────────────────────────────────── */

/* ── Online gaan + openingstijden van vandaag ─────────────────── */

const STATUS = window.PP_STATUS || { online: false, mode: 'bezorgen_afhalen' };

function renderStatus() {
    $('#statusDot').classList.toggle('aan', STATUS.online);
    $('#statusTitel').textContent = STATUS.online
        ? (STATUS.mode === 'alleen_afhalen' ? 'Je staat online, klanten kunnen afhalen' : 'Je staat online, klanten kunnen bestellen')
        : 'Je bent offline';
    const knop = $('#onlineToggle');
    knop.textContent = STATUS.online ? 'Ga offline' : 'Online gaan';
    knop.classList.toggle('btn-grey', STATUS.online);
    $('#modeSwitch').classList.toggle('hidden', !STATUS.online);
    $$('#modeSwitch [data-mode]').forEach((b) => b.classList.toggle('on', b.dataset.mode === STATUS.mode));

    /* Altijd zichtbaar in de topbalk en de navigatie (rail + mobiele tabbalk) */
    $('#topbalkDot')?.classList.toggle('bg-basil', STATUS.online);
    $('#topbalkDot')?.classList.toggle('bg-tomato', !STATUS.online);
    const topbalkStatus = $('#topbalkStatus');
    if (topbalkStatus) topbalkStatus.textContent = STATUS.online ? 'Online' : 'Offline';
    $('#railDot')?.classList.toggle('aan', STATUS.online);
    $('#tabStatus')?.classList.toggle('online', STATUS.online);
    const tabStatusTekst = $('#tabStatusTekst');
    if (tabStatusTekst) tabStatusTekst.textContent = STATUS.online ? 'Online' : 'Offline';
    const railTitel = $('#railStatusTitel');
    const railSub = $('#railStatusSub');
    if (railTitel) railTitel.textContent = STATUS.online ? 'Online' : 'Offline';
    if (railSub) railSub.textContent = STATUS.online
        ? (STATUS.mode === 'alleen_afhalen' ? 'neemt bestellingen aan (alleen afhalen)' : 'neemt bestellingen aan')
        : 'neemt geen bestellingen aan';

    updateWaarschuwing();
}

/* Waarschuwing wanneer online-status en openingstijden niet kloppen */

function isNuBinnenTijden() {
    const DAGKEYS = ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'];
    const key = DAGKEYS[new Date().getDay()];
    if (!DATA.days || !DATA.days[key]) return false;
    const tijd = (DATA.hoursMode === 'perday' && DATA.dayTimes && DATA.dayTimes[key])
        ? DATA.dayTimes[key]
        : { open: DATA.open || '16:00', close: DATA.close || '21:30' };
    const naarDecimaal = (s, fb) => {
        const [h, m] = String(s).split(':').map(Number);
        return isNaN(h) ? fb : h + (m || 0) / 60;
    };
    const nu = new Date();
    const nuD = nu.getHours() + nu.getMinutes() / 60;
    return nuD >= naarDecimaal(tijd.open, 16) && nuD < naarDecimaal(tijd.close, 21.5);
}

function updateWaarschuwing() {
    const banner = $('#statusWaarschuwing');
    if (!banner) return;
    const binnen = isNuBinnenTijden();
    let tekst = '';
    let actie = '';
    if (binnen && !STATUS.online) {
        tekst = 'Volgens je openingstijden ben je nu open, maar je neemt geen bestellingen aan.';
        actie = 'Online gaan';
    } else if (!binnen && STATUS.online) {
        tekst = 'Je staat online buiten je openingstijden. Is dat niet de bedoeling, ga dan offline.';
        actie = 'Ga offline';
    }
    banner.style.display = tekst ? 'flex' : 'none';
    banner.classList.toggle('hidden', !tekst);
    if (tekst) {
        $('#waarschuwingTekst').textContent = tekst;
        $('#waarschuwingActie').textContent = actie;
    }
}

$('#waarschuwingActie')?.addEventListener('click', () => {
    STATUS.online = !STATUS.online;
    renderStatus();
    bewaarStatus();
});

function bewaarStatus() {
    fetch('/dashboard/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ online: STATUS.online, mode: STATUS.mode }),
    }).catch(() => {});
}

/* Online of offline gaan: eerst even zeker weten */
const statusModal = $('#statusModal');
$('#onlineToggle')?.addEventListener('click', () => {
    const naarOnline = !STATUS.online;
    $('#statusModalIcoon').innerHTML = naarOnline
        ? '<i class="fa-solid fa-circle-check" style="color:var(--color-basil)"></i>'
        : '<i class="fa-solid fa-circle-pause" style="color:var(--color-tomato)"></i>';
    $('#statusModalTitel').textContent = naarOnline ? 'Online gaan?' : 'Offline gaan?';
    $('#statusModalTekst').textContent = naarOnline
        ? 'Je bestelpagina gaat open en klanten kunnen direct bestellingen plaatsen.'
        : 'Je bestelpagina gaat dicht en klanten kunnen niet meer bestellen. Openstaande bestellingen blijven gewoon staan.';
    $('#statusModalBevestig').textContent = naarOnline ? 'Ja, ga online' : 'Ja, ga offline';
    statusModal.classList.remove('hidden');
});
$('#statusModalBevestig')?.addEventListener('click', () => {
    statusModal.classList.add('hidden');
    STATUS.online = !STATUS.online;
    renderStatus();
    bewaarStatus();
});
$('#statusModalAnnuleer')?.addEventListener('click', () => statusModal.classList.add('hidden'));
statusModal?.addEventListener('click', (e) => {
    if (!e.target.closest('.animate-pop')) statusModal.classList.add('hidden');
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') statusModal?.classList.add('hidden');
});
$('#modeSwitch')?.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-mode]');
    if (!btn) return;
    STATUS.mode = btn.dataset.mode;
    renderStatus();
    bewaarStatus();
});

(function vandaagTijden() {
    const el = $('#vandaagTijden');
    if (!el) return;
    const DAGKEYS = ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'];
    const key = DAGKEYS[new Date().getDay()];
    if (!DATA.days) { el.textContent = 'Stel eerst je openingstijden in'; return; }
    if (!DATA.days[key]) { el.textContent = 'Vandaag ben je gesloten'; return; }
    const tijd = (DATA.hoursMode === 'perday' && DATA.dayTimes && DATA.dayTimes[key])
        ? DATA.dayTimes[key]
        : { open: DATA.open || '16:00', close: DATA.close || '21:30' };
    el.textContent = `Vandaag open van ${tijd.open} tot ${tijd.close}`;
})();

renderStatus();

/* ── Deel je bestelpagina: link + QR ──────────────────────────── */

(function deelBestelpagina() {
    const linkEl = $('#shareLink');
    if (!linkEl) return;

    const slug = window.PP_SLUG || String(DATA.name || 'jouwpizzeria')
        .normalize('NFD').replace(/[̀-ͯ]/g, '')
        .toLowerCase().replace(/[^a-z0-9]+/g, '').slice(0, 30) || 'jouwpizzeria';
    const link = window.PP_BESTEL_URL || `${location.origin}/bestellen/${slug}`;
    linkEl.innerHTML = `<a href="${link}" target="_blank" rel="noopener" class="hover:underline">${link}</a>`;

    if (window.QRCode) {
        new QRCode($('#qrBox'), { text: link, width: 90, height: 90, correctLevel: QRCode.CorrectLevel.M });
    }

    $('#copyLink')?.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(link);
        } catch {
            const ta = document.createElement('textarea');
            ta.value = link;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            ta.remove();
        }
        const btn = $('#copyLink');
        btn.textContent = 'Gekopieerd';
        setTimeout(() => { btn.textContent = 'Kopieer link'; }, 1600);
    });
})();

/* ── Toppers deze week (voorbeeld tot er echte data is) ───────── */

(function renderToppers() {
    const el = $('#topGerechten');
    if (!el) return;
    const toppers = (window.PP_STATS?.toppers || []);
    if (!toppers.length) {
        el.innerHTML = '<p class="text-sm font-extrabold text-cacao/45">Nog geen bestellingen deze week. Je toppers verschijnen hier vanzelf.</p>';
        return;
    }
    el.innerHTML = toppers.map((t, i) =>
        `<div class="flex items-center gap-2.5 text-sm font-extrabold">
            <span class="w-5 text-center font-display text-cacao/45">${i + 1}</span>
            <span class="flex-1 truncate">${esc(t.naam)}</span>
            <span class="text-cacao/45">${t.aantal}×</span>
        </div>`
    ).join('');
})();

/* ── Gemiddelde doorlooptijd: van betaald tot bezorgd ─────────── */

(function renderDoorloop() {
    const el = $('#doorloopTijd');
    if (!el) return;
    const stats = window.PP_STATS || {};
    if (stats.doorloop === null || stats.doorloop === undefined) {
        $('#doorloopSub').textContent = 'Zodra je eerste bestelling is bezorgd, zie je hier je gemiddelde.';
        return;
    }
    el.textContent = `${stats.doorloop} min`;
    $('#doorloopSub').textContent = `Van betaald tot bezorgd of afgehaald, over ${stats.klaarAantal} bestelling${stats.klaarAantal === 1 ? '' : 'en'} in de afgelopen 7 dagen.`;
})();

/* ── Omzet afgelopen 7 dagen (voorbeeld tot er echte data is) ── */

(function renderOmzetWeek() {
    const chart = $('#omzetWeek');
    if (!chart) return;
    const omzet = (window.PP_STATS?.omzet || []);
    if (!omzet.length) return;
    const max = Math.max(1, ...omzet.map((d) => d.bedrag));
    chart.innerHTML = omzet.map((dag, i) => {
        const rel = dag.bedrag / max;
        const klasse = rel > 0.75 ? 'hoog' : rel > 0.45 ? 'middel' : 'laag';
        return `<div class="drukte-kolom ${i === omzet.length - 1 ? 'nu' : ''}" title="${euro(dag.bedrag)}">
            <div class="d-balkvak"><div class="drukte-bar ${klasse}" data-hoogte="${Math.round(rel * 100)}"></div></div>
            <span class="d-uur">${esc(dag.label)}</span>
        </div>`;
    }).join('');
    requestAnimationFrame(() => requestAnimationFrame(() => {
        $$('#omzetWeek .drukte-bar').forEach((bar) => { bar.style.height = Math.max(4, bar.dataset.hoogte) + '%'; });
    }));
})();

/* ── Introvideo bij het eerste bezoek ─────────────────────────── */

const introModal = $('#introModal');

function introTonen() {
    introModal.classList.remove('hidden');
    introModal.classList.add('flex');
}

function introSluiten(markeren) {
    introModal.classList.add('hidden');
    introModal.classList.remove('flex');
    if (markeren) {
        fetch('/dashboard/intro-gezien', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        }).catch(() => {});
    }
}

if (window.PP_INTRO === true) introTonen();
$('#introSluiten')?.addEventListener('click', () => introSluiten(true));
$('#introOpnieuw')?.addEventListener('click', introTonen);
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !introModal.classList.contains('hidden')) introSluiten(true);
});

/* ── Menukaart-beheer ─────────────────────────────────────────── */

const MENU = (window.PP_MENU || []).map((m) => ({ ...m }));
const ALLERGENEN = ['gluten', 'schaaldieren', 'ei', 'vis', 'pinda', 'soja', 'melk', 'noten', 'selderij', 'mosterd', 'sesam', 'sulfiet', 'lupine', 'weekdieren'];
const cap = (s) => s.charAt(0).toUpperCase() + s.slice(1);

/* Veelvoorkomende ingrediënten → allergenen. Alleen een hulpje: de pizzeria controleert zelf. */
const ALLERGEEN_HINTS = {
    melk: ['mozzarella', 'kaas', 'gorgonzola', 'parmezaan', 'mascarpone', 'ricotta', 'burrata', 'room', 'boter'],
    gluten: ['bloem', 'deeg', 'bodem', 'paneermeel'],
    vis: ['ansjovis', 'tonijn', 'zalm', 'vis'],
    schaaldieren: ['garnaal', 'garnalen', 'gamba', 'krab', 'kreeft'],
    weekdieren: ['mossel', 'mosselen', 'inktvis', 'calamaris', 'octopus'],
    ei: ['ei', 'eieren', 'mayonaise', 'aioli'],
    noten: ['pesto', 'walnoot', 'walnoten', 'hazelnoot', 'noten', 'pistache'],
    pinda: ['pinda', 'satesaus', 'sate', 'saté'],
    soja: ['soja'],
    sesam: ['sesam'],
    mosterd: ['mosterd'],
    selderij: ['selderij', 'bleekselderij'],
};

function allergenenBij(ingredient) {
    const woorden = ingredient.toLowerCase().split(/[\s-]+/);
    return Object.keys(ALLERGEEN_HINTS).filter((a) =>
        ALLERGEEN_HINTS[a].some((kw) => woorden.some((w) => w === kw || (kw.length >= 4 && w.includes(kw)))));
}

const naarCenten = (str) => {
    const n = parseFloat(String(str).replace(/[^\d,.]/g, '').replace(',', '.'));
    return isNaN(n) ? null : Math.round(n * 100);
};
const uitCenten = (c) => (c / 100).toFixed(2).replace('.', ',');

let menuCatFilter = 'alles';
const menuCategorieen = () => [...new Set(MENU.map((m) => m.categorie))];

function renderMenuCats() {
    const el = $('#menuCats');
    if (!el) return;
    const cats = menuCategorieen();
    el.innerHTML = cats.length < 2 ? '' : ['alles', ...cats].map((c) =>
        `<button type="button" class="order-tab" data-menucat="${esc(c)}"
            style="${menuCatFilter === c ? 'background:var(--color-cacao); border-color:var(--color-cacao); color:#fff;' : ''}">${c === 'alles' ? 'Alles' : esc(c)}</button>`).join('');
}

function menuKaart(m) {
    const ing = m.ingredienten || [];
    const allerg = m.allergenen;
    const allergHtml = (allerg === null || allerg === undefined)
        ? '<span class="warn-chip leeg"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Allergenen nog niet ingevuld</span>'
        : (allerg.length
            ? allerg.map((a) => `<span class="warn-chip"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> ${cap(a)}</span>`).join('')
            : '<span class="warn-chip ok"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Geen allergenen</span>');
    const groepen = m.opties || [];
    return `<div class="menu-card dash-card !p-4 ${m.actief ? '' : 'uit'}" data-menu-id="${m.id}">
        <div class="flex items-start gap-3">
            ${m.foto ? `<img src="${m.foto}" alt="" class="w-12 h-12 rounded-xl object-cover border-2 border-white shadow shrink-0">` : ''}
            <div class="flex-1 min-w-0">
                <p class="font-display text-xl truncate">${!m.foto && m.icoon ? esc(m.icoon) + ' ' : ''}${esc(m.naam)}</p>
                <p class="text-xs font-extrabold text-cacao/45">${esc(m.categorie)}</p>
            </div>
            <p class="font-display text-xl shrink-0">${euro(m.prijs)}</p>
            <button type="button" class="keuze-chip shrink-0 ${m.actief ? 'aan' : ''}" data-menu-actief="${m.id}" title="${m.actief ? 'Klanten zien dit gerecht' : 'Verborgen voor klanten'}">${m.actief ? 'Actief' : 'Uit'}</button>
        </div>
        ${m.beschrijving ? `<p class="text-sm font-extrabold text-cacao/55 mt-1.5">${esc(m.beschrijving)}</p>` : ''}
        ${ing.length ? `<p class="text-xs font-extrabold text-cacao/50 mt-2">${ing.map(esc).join(', ')}</p>` : ''}
        <div class="flex flex-wrap gap-1.5 mt-2.5">${allergHtml}</div>
        ${groepen.length ? `<p class="text-xs font-extrabold text-cacao/45 mt-2"><i class="fa-solid fa-sliders" aria-hidden="true"></i> Opties: ${groepen.map((g) => esc(g.naam)).join(', ')}</p>` : ''}
    </div>`;
}

function menuSamenvattingBijwerken() {
    const el = $('#menuSamenvatting');
    if (!el) return;
    if (!MENU.length) { el.textContent = 'Je hebt nog geen gerechten toegevoegd.'; return; }
    const cats = menuCategorieen().length;
    el.textContent = `Je hebt ${MENU.length} gerecht${MENU.length === 1 ? '' : 'en'} in ${cats} categorie${cats === 1 ? '' : 'ën'} staan.`;
}

function renderMenu(popId = null) {
    const lijst = $('#menuLijst');
    if (!lijst) return;
    if (menuCatFilter !== 'alles' && !menuCategorieen().includes(menuCatFilter)) menuCatFilter = 'alles';
    renderMenuCats();
    const items = menuCatFilter === 'alles' ? MENU : MENU.filter((m) => m.categorie === menuCatFilter);
    lijst.innerHTML = items.map(menuKaart).join('');
    if (popId) lijst.querySelector(`[data-menu-id="${popId}"]`)?.classList.add('pop');
    $('#menuLeeg')?.classList.toggle('hidden', MENU.length > 0);
    menuSamenvattingBijwerken();
}

function vervangMenuKaart(item) {
    const el = document.querySelector(`#menuLijst [data-menu-id="${item.id}"]`);
    if (el) el.outerHTML = menuKaart(item);
}

/* ── Editor: gerecht toevoegen of bewerken ── */

const OPTIE_TEMPLATES = [
    { naam: 'Formaat', type: 'een', keuzes: [{ naam: '25 cm', prijs: '0,00' }, { naam: '30 cm', prijs: '2,50' }, { naam: '35 cm', prijs: '4,50' }] },
    { naam: 'Saus erbij', type: 'meerdere', keuzes: [{ naam: 'Knoflooksaus', prijs: '1,00' }, { naam: 'Sambal', prijs: '0,50' }, { naam: 'Chilisaus', prijs: '0,75' }] },
    { naam: 'Extra ingrediënten', type: 'meerdere', keuzes: [{ naam: 'Extra kaas', prijs: '1,50' }, { naam: 'Champignons', prijs: '1,00' }, { naam: 'Salami', prijs: '1,50' }] },
    { naam: 'Drankje erbij', type: 'meerdere', keuzes: [{ naam: 'Cola', prijs: '2,50' }, { naam: 'Fanta', prijs: '2,50' }, { naam: 'Spa blauw', prijs: '2,00' }] },
];

let bewerkt = null;   // werkkopie van het gerecht dat in de editor staat

function openMenuEditor(item = null) {
    bewerkt = item ? {
        id: item.id,
        naam: item.naam,
        prijs: uitCenten(item.prijs),
        categorie: item.categorie,
        beschrijving: item.beschrijving || '',
        ingredienten: [...(item.ingredienten || [])],
        allergenen: [...(item.allergenen || [])],
        geenAllergenen: Array.isArray(item.allergenen) && item.allergenen.length === 0,
        opties: (item.opties || []).map((g) => ({ ...g, keuzes: g.keuzes.map((k) => ({ ...k, prijs: uitCenten(k.prijs) })) })),
        foto: item.foto || null,
        actief: item.actief,
        nieuweCat: false,
    } : {
        id: null, naam: '', prijs: '',
        categorie: menuCatFilter !== 'alles' ? menuCatFilter : (menuCategorieen()[0] || "Pizza's"),
        beschrijving: '', foto: null, ingredienten: [], allergenen: [], geenAllergenen: false, opties: [], actief: true, nieuweCat: false,
    };
    const verwijder = $('#menuVerwijder');
    verwijder.textContent = 'Dit gerecht verwijderen';
    delete verwijder.dataset.zeker;
    $('#mNaam').value = bewerkt.naam;
    $('#mPrijs').value = bewerkt.prijs;
    $('#mBeschrijving').value = bewerkt.beschrijving;
    $('#mIngInput').value = '';
    $('#mNaamErr').textContent = '';
    $('#mPrijsErr').textContent = '';
    $('#mFotoErr').textContent = '';
    $('#mAllergHint').textContent = '';
    renderCatChips(); renderIngChips(); renderAllergChips(); renderOptieGroepen(); renderMFoto();
    mVanOverzicht = false;
    toonMStap(item ? 'overzicht' : 'naam');
    $('#menuModal').classList.remove('hidden');
}

function sluitMenuEditor() {
    $('#menuModal').classList.add('hidden');
    bewerkt = null;
}

/* ── De wizard: één vraag per stap, met het mannetje ernaast ── */

const M_STAPPEN = ['naam', 'prijs', 'categorie', 'beschrijving', 'foto', 'ingredienten', 'allergenen', 'opties', 'overzicht'];
let mStap = 'naam';
let mVanOverzicht = false;   // via "wijzig" op het overzicht een stap ingedoken?

const naamNu = () => $('#mNaam').value.trim() || 'dit gerecht';

const M_INFO = {
    naam:         { kicker: () => (bewerkt.id ? 'Even bijwerken' : 'Nieuw gerecht'), titel: 'Hoe heet je gerecht?', sub: 'De naam die klanten straks op je kaart zien.', mascot: 'tossing-dough', bubble: 'Kies een naam die klanten herkennen.', kant: 'right', enter: true },
    prijs:        { kicker: 'De prijs', titel: () => `Wat kost ${naamNu()}?`, sub: 'De normale prijs. Extra opties komen zo nog.', mascot: 'holding-3-pizzas', bubble: 'Een eerlijke prijs werkt het best.', kant: 'left', enter: true },
    categorie:    { kicker: 'Op de kaart', titel: 'In welke categorie hoort dit?', sub: 'Zo vinden klanten het sneller terug.', mascot: 'writing-chalkboard', bubble: 'Zo blijft je kaart overzichtelijk.', kant: 'right' },
    beschrijving: { kicker: 'Beschrijving', titel: 'Wil je er iets bij vertellen?', sub: 'Een korte omschrijving maakt het extra smakelijk. Mag ook leeg blijven.', mascot: 'showing-pizza-order', bubble: 'Verse basilicum? Benoem het gerust.', kant: 'left' },
    foto:         { kicker: 'De foto', titel: () => `Heb je een foto van ${naamNu()}?`, sub: 'Gerechten met een foto worden vaker besteld. Mag ook zonder.', mascot: 'pizza-in-oven', bubble: 'Mensen eten met hun ogen.', kant: 'right' },
    ingredienten: { kicker: 'Ingrediënten', titel: 'Welke ingrediënten zitten erop?', sub: 'Klanten kunnen deze weglaten bij het bestellen, en gasten met een allergie zien precies wat erin zit.', mascot: 'sprinkling-cheese', bubble: 'Klanten zien precies wat erop zit.', kant: 'right' },
    allergenen:   { kicker: 'Allergenen', titel: 'Welke allergenen zitten erin?', sub: 'Tik aan wat erin zit en controleer het altijd zelf. Een pizzabodem bevat gluten.', mascot: 'kneading-dough', bubble: 'Dit moet kloppen voor je gasten.', kant: 'left' },
    opties:       { kicker: 'Opties', titel: 'Welke opties geef je de klant?', sub: 'Een saus, extra ingrediënten of een drankje erbij. Goed voor je omzet.', mascot: 'running-with-pizza', bubble: 'Goed voor je gemiddelde bestelling.', kant: 'right' },
    overzicht:    { kicker: 'Overzicht', titel: () => (bewerkt.id ? 'Dit is je gerecht' : 'Klaar om op de kaart te zetten?'), sub: 'Loop alles nog even na, wijzigen kan altijd.', mascot: 'waving-hello', bubble: 'Die staat straks mooi op de kaart.', kant: 'right' },
};

function toonMStap(stap) {
    mStap = stap;
    const info = M_INFO[stap];
    const val = (v) => (typeof v === 'function' ? v() : v);
    $$('#menuModal [data-mstap]').forEach((el) => {
        const actief = el.dataset.mstap === stap;
        el.classList.toggle('hidden', !actief);
        el.classList.toggle('actief', actief);
    });
    $('#mKicker').textContent = val(info.kicker);
    $('#mTitel').textContent = val(info.titel);
    $('#mSub').textContent = val(info.sub);
    /* Huisstijl 2.0: de wizard-mascotte is uit het ontwerp */
    const eerste = !mVanOverzicht && stap === (bewerkt.id ? 'overzicht' : 'naam');
    $('#mTerug').classList.toggle('hidden', eerste);
    $('#mVolgende').textContent = stap === 'overzicht' ? (bewerkt.id ? 'Opslaan' : 'Op de kaart zetten') : (mVanOverzicht ? 'Klaar' : 'Volgende');
    $('#mEnterHint').style.visibility = info.enter ? 'visible' : 'hidden';
    $('#menuVerwijder').classList.toggle('hidden', !(stap === 'overzicht' && bewerkt.id));
    if (stap === 'overzicht') renderMOverzicht();
    const focusEl = { naam: '#mNaam', prijs: '#mPrijs', beschrijving: '#mBeschrijving', ingredienten: '#mIngInput' }[stap];
    if (focusEl) setTimeout(() => $(focusEl)?.focus(), 80);
}

function valideerMStap() {
    if (mStap === 'naam' && !$('#mNaam').value.trim()) {
        $('#mNaam').classList.add('m-fout');
        $('#mNaamErr').textContent = 'Geef je gerecht eerst een naam.';
        return false;
    }
    if (mStap === 'prijs' && naarCenten($('#mPrijs').value) === null) {
        $('#mPrijs').classList.add('m-fout');
        $('#mPrijsErr').textContent = 'Vul een prijs in, bijvoorbeeld 9,50';
        return false;
    }
    return true;
}

function volgendeMStap() {
    if (!valideerMStap()) return;
    if (mStap === 'overzicht') return menuOpslaanNu();
    if (mVanOverzicht) { mVanOverzicht = false; return toonMStap('overzicht'); }
    toonMStap(M_STAPPEN[M_STAPPEN.indexOf(mStap) + 1]);
}

function terugMStap() {
    if (mVanOverzicht) { mVanOverzicht = false; return toonMStap('overzicht'); }
    const i = M_STAPPEN.indexOf(mStap);
    if (i > 0) toonMStap(M_STAPPEN[i - 1]);
}

function renderMOverzicht() {
    const rij = (label, waarde, stap) => `
        <div class="flex items-center gap-3 rounded-2xl px-4 py-3" style="background:var(--color-crema)">
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-extrabold uppercase tracking-wide text-cacao/45">${label}</p>
                <p class="text-sm font-extrabold break-words">${waarde}</p>
            </div>
            <button type="button" class="skip-link !mt-0 shrink-0" data-m-ga="${stap}">wijzig</button>
        </div>`;
    const leeg = '<span class="text-cacao/40">Geen</span>';
    const allerg = bewerkt.geenAllergenen
        ? 'Geen allergenen'
        : (bewerkt.allergenen.length ? bewerkt.allergenen.map(cap).join(', ') : '<span class="text-cacao/40">Nog niet ingevuld</span>');
    const groepen = bewerkt.opties.filter((g) => g.naam.trim() && g.keuzes.some((k) => k.naam.trim()));
    $('#mOverzicht').innerHTML =
        rij('Naam', esc($('#mNaam').value.trim()), 'naam')
        + rij('Prijs', euro(naarCenten($('#mPrijs').value) ?? 0), 'prijs')
        + rij('Categorie', esc((bewerkt.nieuweCat && $('#mCatNieuw')?.value.trim()) || bewerkt.categorie || 'Menu'), 'categorie')
        + rij('Beschrijving', esc($('#mBeschrijving').value.trim()) || leeg, 'beschrijving')
        + rij('Foto', bewerkt.foto ? `<img src="${bewerkt.foto}" alt="" class="w-11 h-11 rounded-xl object-cover border-2 border-white shadow">` : leeg, 'foto')
        + rij('Ingrediënten', bewerkt.ingredienten.length ? bewerkt.ingredienten.map(esc).join(', ') : leeg, 'ingredienten')
        + rij('Allergenen', allerg, 'allergenen')
        + rij('Opties', groepen.length ? groepen.map((g) => `${esc(g.naam)} (${g.keuzes.filter((k) => k.naam.trim()).length})`).join(', ') : leeg, 'opties');
}

function renderCatChips() {
    const cats = menuCategorieen();
    if (bewerkt.categorie && !cats.includes(bewerkt.categorie)) cats.push(bewerkt.categorie);
    $('#mCatChips').innerHTML = cats.map((c) =>
        `<button type="button" class="keuze-chip ${bewerkt.categorie === c ? 'aan' : ''}" data-m-cat="${esc(c)}">${esc(c)}</button>`).join('')
        + (bewerkt.nieuweCat
            ? '<input id="mCatNieuw" class="m-inp !w-48 !py-1.5" placeholder="Naam nieuwe categorie">'
            : '<button type="button" class="keuze-chip" data-m-cat-nieuw><i class="fa-solid fa-plus" aria-hidden="true"></i> Nieuwe categorie</button>');
    if (bewerkt.nieuweCat) $('#mCatNieuw')?.focus();
}

function renderMFoto() {
    $('#mFotoLeeg').classList.toggle('hidden', !!bewerkt.foto);
    $('#mFotoVol').classList.toggle('hidden', !bewerkt.foto);
    if (bewerkt.foto) $('#mFotoPreview').src = bewerkt.foto;
}

/* Foto verkleinen in de browser zodat de upload klein blijft (max 1000px, JPEG) */
function fotoVerkleinen(file) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            const schaal = Math.min(1, 1000 / Math.max(img.width, img.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(img.width * schaal);
            canvas.height = Math.round(img.height * schaal);
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            URL.revokeObjectURL(url);
            resolve(canvas.toDataURL('image/jpeg', 0.82));
        };
        img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('kan afbeelding niet lezen')); };
        img.src = url;
    });
}

function renderIngChips() {
    $('#mIngChips').innerHTML = bewerkt.ingredienten.map((ing, i) =>
        `<span class="chip">${esc(ing)}<button type="button" class="chip-x" data-m-ing-del="${i}" aria-label="Verwijder ${esc(ing)}"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button></span>`).join('')
        || '<span class="m-hint">Nog geen ingrediënten toegevoegd.</span>';
}

function renderAllergChips() {
    $('#mAllergChips').innerHTML = ALLERGENEN.map((a) =>
        `<button type="button" class="keuze-chip geel ${bewerkt.allergenen.includes(a) ? 'aan' : ''}" data-m-allerg="${a}">${cap(a)}</button>`).join('')
        + `<button type="button" class="keuze-chip ${bewerkt.geenAllergenen ? 'aan' : ''}" data-m-allerg-geen><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Geen allergenen</button>`;
}

function renderOptieTemplates() {
    $('#mOptieTemplates').innerHTML = OPTIE_TEMPLATES.map((t, i) =>
        `<button type="button" class="keuze-chip" data-m-tpl="${i}" ${bewerkt.opties.some((g) => g.naam === t.naam) ? 'disabled style="opacity:.35; pointer-events:none"' : ''}><i class="fa-solid fa-plus" aria-hidden="true"></i> ${t.naam}</button>`).join('')
        + '<button type="button" class="keuze-chip" data-m-groep-nieuw><i class="fa-solid fa-plus" aria-hidden="true"></i> Eigen groep</button>';
}

function renderOptieGroepen() {
    $('#mOptieGroepen').innerHTML = bewerkt.opties.map((g, gi) => `
        <div class="optie-groep">
            <div class="flex items-center gap-2 mb-2">
                <input class="m-inp !py-1.5 flex-1 min-w-0" value="${esc(g.naam)}" placeholder="Naam van de groep" data-m-g-naam="${gi}">
                <div class="seg shrink-0">
                    <button type="button" class="${g.type === 'een' ? 'aan' : ''}" data-m-g-type="${gi}" data-type="een">1 keuze</button>
                    <button type="button" class="${g.type === 'meerdere' ? 'aan' : ''}" data-m-g-type="${gi}" data-type="meerdere">Meerdere</button>
                </div>
                <button type="button" class="chip-x shrink-0 text-base" data-m-g-del="${gi}" aria-label="Groep verwijderen"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></button>
            </div>
            <div class="space-y-1.5">
                ${g.keuzes.map((k, ki) => `
                    <div class="flex items-center gap-2">
                        <input class="m-inp !py-1.5 flex-1 min-w-0" value="${esc(k.naam)}" placeholder="bijv. Knoflooksaus" data-m-k-naam="${gi}-${ki}">
                        <div class="relative w-24 shrink-0">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-extrabold text-cacao/40">+ €</span>
                            <input class="m-inp !py-1.5 !pl-9 !text-sm" value="${esc(k.prijs)}" placeholder="0,00" inputmode="decimal" data-m-k-prijs="${gi}-${ki}">
                        </div>
                        <button type="button" class="chip-x shrink-0" data-m-k-del="${gi}-${ki}" aria-label="Keuze verwijderen"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                    </div>`).join('')}
            </div>
            <button type="button" class="skip-link !mt-2 !text-xs" data-m-k-add="${gi}">+ keuze toevoegen</button>
        </div>`).join('');
    renderOptieTemplates();
}

function voegIngredientToe(ruw) {
    const autoNieuw = [];
    ruw.split(',').map((s) => s.trim()).filter(Boolean).forEach((ing) => {
        if (bewerkt.ingredienten.some((b) => b.toLowerCase() === ing.toLowerCase())) return;
        bewerkt.ingredienten.push(ing);
        allergenenBij(ing).forEach((a) => {
            if (!bewerkt.geenAllergenen && !bewerkt.allergenen.includes(a)) {
                bewerkt.allergenen.push(a);
                autoNieuw.push(a);
            }
        });
    });
    renderIngChips();
    renderAllergChips();
    if (autoNieuw.length) {
        $('#mAllergHint').innerHTML = `<b>${autoNieuw.map(cap).join(' en ')}</b> automatisch aangevinkt op basis van je ingrediënten. Controleer het altijd zelf.`;
    }
}

async function menuOpslaanNu() {
    const naam = $('#mNaam').value.trim();
    const prijs = naarCenten($('#mPrijs').value);
    if (!naam) { $('#mNaam').classList.add('m-fout'); $('#mNaam').focus(); return; }
    if (prijs === null) { $('#mPrijs').classList.add('m-fout'); $('#mPrijs').focus(); return; }
    const payload = {
        naam,
        prijs,
        categorie: (bewerkt.nieuweCat && $('#mCatNieuw')?.value.trim()) || bewerkt.categorie || 'Menu',
        beschrijving: $('#mBeschrijving').value.trim() || null,
        ingredienten: bewerkt.ingredienten,
        allergenen: bewerkt.geenAllergenen ? [] : (bewerkt.allergenen.length ? bewerkt.allergenen : null),
        foto: bewerkt.foto,
        opties: bewerkt.opties
            .map((g) => ({
                naam: g.naam.trim(),
                type: g.type,
                keuzes: g.keuzes.filter((k) => k.naam.trim()).map((k) => ({ naam: k.naam.trim(), prijs: naarCenten(k.prijs) ?? 0 })),
            }))
            .filter((g) => g.naam && g.keuzes.length),
        actief: bewerkt.actief,
    };
    const res = await postJson(bewerkt.id ? `/menukaart/${bewerkt.id}` : '/menukaart', payload, bewerkt.id ? 'PATCH' : 'POST');
    if (!res.ok) return;
    const item = await res.json();
    const idx = MENU.findIndex((m) => m.id === item.id);
    if (idx >= 0) MENU[idx] = item; else MENU.push(item);
    sluitMenuEditor();
    renderMenu(item.id);
}

/* ── Events menukaart ── */

$('#menuNieuw')?.addEventListener('click', () => openMenuEditor());
$('#menuLeeg')?.addEventListener('click', (e) => { if (e.target.closest('[data-menu-nieuw]')) openMenuEditor(); });
$('#menuModalSluit')?.addEventListener('click', sluitMenuEditor);
$('#mVolgende')?.addEventListener('click', volgendeMStap);
$('#mTerug')?.addEventListener('click', terugMStap);

/* Fotostap: kiezen, vervangen of weghalen */
$('#mFotoKies')?.addEventListener('click', () => $('#mFotoInp').click());
$('#mFotoAnders')?.addEventListener('click', () => $('#mFotoInp').click());
$('#mFotoWeg')?.addEventListener('click', () => { bewerkt.foto = null; $('#mFotoInp').value = ''; renderMFoto(); });
$('#mFotoInp')?.addEventListener('change', async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    $('#mFotoErr').textContent = '';
    try {
        bewerkt.foto = await fotoVerkleinen(file);
        renderMFoto();
    } catch {
        $('#mFotoErr').textContent = 'Deze afbeelding kunnen we niet lezen, probeer een andere.';
    }
    e.target.value = '';
});

$('#menuVerwijder')?.addEventListener('click', async (e) => {
    const knop = e.currentTarget;
    if (knop.dataset.zeker !== '1') {
        knop.dataset.zeker = '1';
        knop.textContent = 'Zeker weten? Tik nog een keer';
        return;
    }
    const id = bewerkt.id;
    await postJson(`/menukaart/${id}`, null, 'DELETE').catch(() => {});
    const idx = MENU.findIndex((m) => m.id === id);
    if (idx >= 0) MENU.splice(idx, 1);
    sluitMenuEditor();
    renderMenu();
});

$('#menuCats')?.addEventListener('click', (e) => {
    const tab = e.target.closest('[data-menucat]');
    if (!tab) return;
    menuCatFilter = tab.dataset.menucat;
    renderMenu();
});

$('#menuLijst')?.addEventListener('click', (e) => {
    const toggle = e.target.closest('[data-menu-actief]');
    if (toggle) {
        const item = MENU.find((m) => m.id === Number(toggle.dataset.menuActief));
        if (!item) return;
        item.actief = !item.actief;
        vervangMenuKaart(item);
        postJson(`/menukaart/${item.id}`, { actief: item.actief }, 'PATCH').catch(() => {});
        return;
    }
    const kaart = e.target.closest('[data-menu-id]');
    if (kaart) openMenuEditor(MENU.find((m) => m.id === Number(kaart.dataset.menuId)));
});

$('#mIngInput')?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        voegIngredientToe(e.target.value);
        e.target.value = '';
    }
});
$('#mIngInput')?.addEventListener('blur', (e) => {
    if (e.target.value.trim()) { voegIngredientToe(e.target.value); e.target.value = ''; }
});

$('#menuModal')?.addEventListener('input', (e) => {
    e.target.classList.remove('m-fout');
    if (e.target.id === 'mNaam') $('#mNaamErr').textContent = '';
    if (e.target.id === 'mPrijs') $('#mPrijsErr').textContent = '';
    if (!bewerkt) return;
    const d = e.target.dataset;
    if (d.mGNaam !== undefined) bewerkt.opties[+d.mGNaam].naam = e.target.value;
    else if (d.mKNaam !== undefined) { const [gi, ki] = d.mKNaam.split('-').map(Number); bewerkt.opties[gi].keuzes[ki].naam = e.target.value; }
    else if (d.mKPrijs !== undefined) { const [gi, ki] = d.mKPrijs.split('-').map(Number); bewerkt.opties[gi].keuzes[ki].prijs = e.target.value; }
});

$('#menuModal')?.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') return sluitMenuEditor();
    if (e.key !== 'Enter') return;
    if (e.target.id === 'mCatNieuw') {
        const naam = e.target.value.trim();
        if (naam) bewerkt.categorie = naam;
        bewerkt.nieuweCat = false;
        renderCatChips();
        return;
    }
    if (e.target.id === 'mNaam' || e.target.id === 'mPrijs') {
        e.preventDefault();
        volgendeMStap();
    }
});

$('#menuModal')?.addEventListener('click', (e) => {
    if (!bewerkt) return;
    if (e.target === $('#menuModal') || e.target === $('#mModalMidden')) return sluitMenuEditor();
    const ga = e.target.closest('[data-m-ga]');
    if (ga) { mVanOverzicht = true; return toonMStap(ga.dataset.mGa); }
    const cat = e.target.closest('[data-m-cat]');
    if (cat) { bewerkt.categorie = cat.dataset.mCat; bewerkt.nieuweCat = false; return renderCatChips(); }
    if (e.target.closest('[data-m-cat-nieuw]')) { bewerkt.nieuweCat = true; return renderCatChips(); }
    const ingDel = e.target.closest('[data-m-ing-del]');
    if (ingDel) { bewerkt.ingredienten.splice(+ingDel.dataset.mIngDel, 1); return renderIngChips(); }
    const allerg = e.target.closest('[data-m-allerg]');
    if (allerg) {
        const a = allerg.dataset.mAllerg;
        bewerkt.geenAllergenen = false;
        bewerkt.allergenen = bewerkt.allergenen.includes(a) ? bewerkt.allergenen.filter((x) => x !== a) : [...bewerkt.allergenen, a];
        return renderAllergChips();
    }
    if (e.target.closest('[data-m-allerg-geen]')) {
        bewerkt.geenAllergenen = !bewerkt.geenAllergenen;
        if (bewerkt.geenAllergenen) bewerkt.allergenen = [];
        return renderAllergChips();
    }
    const tpl = e.target.closest('[data-m-tpl]');
    if (tpl) { bewerkt.opties.push(JSON.parse(JSON.stringify(OPTIE_TEMPLATES[+tpl.dataset.mTpl]))); return renderOptieGroepen(); }
    if (e.target.closest('[data-m-groep-nieuw]')) { bewerkt.opties.push({ naam: '', type: 'meerdere', keuzes: [{ naam: '', prijs: '0,00' }] }); return renderOptieGroepen(); }
    const gType = e.target.closest('[data-m-g-type]');
    if (gType) { bewerkt.opties[+gType.dataset.mGType].type = gType.dataset.type; return renderOptieGroepen(); }
    const gDel = e.target.closest('[data-m-g-del]');
    if (gDel) { bewerkt.opties.splice(+gDel.dataset.mGDel, 1); return renderOptieGroepen(); }
    const kDel = e.target.closest('[data-m-k-del]');
    if (kDel) {
        const [gi, ki] = kDel.dataset.mKDel.split('-').map(Number);
        bewerkt.opties[gi].keuzes.splice(ki, 1);
        if (!bewerkt.opties[gi].keuzes.length) bewerkt.opties.splice(gi, 1);
        return renderOptieGroepen();
    }
    const kAdd = e.target.closest('[data-m-k-add]');
    if (kAdd) { bewerkt.opties[+kAdd.dataset.mKAdd].keuzes.push({ naam: '', prijs: '0,00' }); return renderOptieGroepen(); }
});

renderMenu();

/* ── Bezorgkosten: tarieven per straal, met kaart ─────────────── */

const BEZORG = window.PP_BEZORG || { lat: null, lng: null, tiers: [] };
const BEZORG_KLEUREN = ['#2C7A4B', '#B97F10', '#F97316', '#B04A3F', '#2563EB', '#241712'];

let bezorgTiers = (BEZORG.tiers && BEZORG.tiers.length)
    ? BEZORG.tiers.map((t) => ({ km: String(t.km).replace('.', ','), kosten: uitCenten(t.kosten), min: t.min ? uitCenten(t.min) : '' }))
    : [{ km: '2', kosten: '1,00', min: '' }, { km: '4', kosten: '2,50', min: '' }];
let bezorgLat = BEZORG.lat;
let bezorgLng = BEZORG.lng;
let bezorgMap = null;
let bezorgMarker = null;
let bezorgCirkels = [];

const naarKm = (str) => {
    const n = parseFloat(String(str).replace(',', '.'));
    return isNaN(n) || n <= 0 ? null : Math.min(30, n);
};

function renderBezorgTiers() {
    const el = $('#bezorgTiers');
    if (!el) return;
    el.innerHTML = bezorgTiers.map((t, i) => `
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full shrink-0" style="background:${BEZORG_KLEUREN[i % BEZORG_KLEUREN.length]}"></span>
            <span class="text-sm font-extrabold text-cacao/45 shrink-0">tot</span>
            <input class="m-inp !py-1.5 !w-14 text-center" value="${esc(t.km)}" inputmode="decimal" data-b-km="${i}" aria-label="Afstand in kilometers">
            <span class="text-sm font-extrabold text-cacao/45 shrink-0">km</span>
            <div class="relative flex-1 min-w-0">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-extrabold text-cacao/40">€</span>
                <input class="m-inp !py-1.5 !pl-7" value="${esc(t.kosten)}" placeholder="0,00" inputmode="decimal" data-b-kosten="${i}" aria-label="Bezorgkosten">
            </div>
            <div class="relative flex-1 min-w-0" title="Minimaal bestelbedrag voor deze straal">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-extrabold text-cacao/40">min €</span>
                <input class="m-inp !py-1.5 !pl-12" value="${esc(t.min)}" placeholder="0,00" inputmode="decimal" data-b-min="${i}" aria-label="Minimaal bestelbedrag">
            </div>
            <button type="button" class="chip-x shrink-0" data-b-del="${i}" aria-label="Straal verwijderen"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>`).join('');
}

/* Eigen pizza-pin in plaats van de standaard blauwe punaise */
function pizzaPin() {
    return L.divIcon({
        className: 'bezorg-pin',
        html: '<div class="bezorg-pin-bol"><span><i class="fa-solid fa-pizza-slice" aria-hidden="true"></i></span></div>',
        iconSize: [44, 44],
        iconAnchor: [22, 40],
    });
}

function tekenBezorgCirkels() {
    if (!bezorgMap || bezorgLat === null || bezorgLng === null) return;
    bezorgCirkels.forEach((c) => c.remove());
    bezorgCirkels = [];
    if (!bezorgMarker) bezorgMarker = L.marker([bezorgLat, bezorgLng], { icon: pizzaPin() }).addTo(bezorgMap);
    else bezorgMarker.setLatLng([bezorgLat, bezorgLng]);
    /* Grootste ring eerst tekenen, zodat de kleinere bovenop liggen */
    bezorgTiers
        .map((t, i) => ({ km: naarKm(t.km), kleur: BEZORG_KLEUREN[i % BEZORG_KLEUREN.length] }))
        .filter((t) => t.km)
        .sort((a, b) => b.km - a.km)
        .forEach((t) => {
            bezorgCirkels.push(L.circle([bezorgLat, bezorgLng], {
                radius: t.km * 1000, color: t.kleur, weight: 2, fillColor: t.kleur, fillOpacity: .07,
            }).addTo(bezorgMap));
        });
}

function pasBezorgZoom() {
    if (bezorgMap && bezorgCirkels[0]) bezorgMap.fitBounds(bezorgCirkels[0].getBounds().pad(0.15));
}

function initBezorgKaart() {
    const el = $('#bezorgMap');
    if (!el || typeof L === 'undefined') return;
    if (bezorgMap) { bezorgMap.invalidateSize(); return; }
    const heeftLocatie = bezorgLat !== null && bezorgLng !== null;
    bezorgMap = L.map('bezorgMap', { scrollWheelZoom: false, attributionControl: false })
        .setView(heeftLocatie ? [bezorgLat, bezorgLng] : [52.15, 5.38], heeftLocatie ? 12 : 7);
    /* Zachte cartoon-tegels (CARTO Voyager); de warme tint komt uit de CSS-filter */
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(bezorgMap);
    $('#bezorgHint').textContent = heeftLocatie
        ? 'Je zaak staat op de kaart op basis van je adres bij Bedrijfsgegevens.'
        : 'Vul je adres in bij Bedrijfsgegevens, dan zetten we je zaak automatisch op de kaart.';
    tekenBezorgCirkels();
    if (heeftLocatie) pasBezorgZoom();
}

$('#bezorgTiers')?.addEventListener('input', (e) => {
    const d = e.target.dataset;
    if (d.bKm !== undefined) { bezorgTiers[+d.bKm].km = e.target.value; tekenBezorgCirkels(); }
    if (d.bKosten !== undefined) bezorgTiers[+d.bKosten].kosten = e.target.value;
    if (d.bMin !== undefined) bezorgTiers[+d.bMin].min = e.target.value;
});

$('#bezorgTiers')?.addEventListener('click', (e) => {
    const del = e.target.closest('[data-b-del]');
    if (del) {
        bezorgTiers.splice(+del.dataset.bDel, 1);
        renderBezorgTiers();
        tekenBezorgCirkels();
    }
});

$('#bezorgTierAdd')?.addEventListener('click', () => {
    if (bezorgTiers.length >= 8) return;
    const laatste = naarKm(bezorgTiers[bezorgTiers.length - 1]?.km) || 2;
    bezorgTiers.push({ km: String(Math.min(30, laatste + 2)).replace('.', ','), kosten: '', min: '' });
    renderBezorgTiers();
    tekenBezorgCirkels();
});

$('#bezorgOpslaan')?.addEventListener('click', async () => {
    const tiers = bezorgTiers
        .map((t) => ({ km: naarKm(t.km), kosten: naarCenten(t.kosten) ?? 0, min: naarCenten(t.min) ?? 0 }))
        .filter((t) => t.km)
        .sort((a, b) => a.km - b.km);
    const res = await postJson('/instellingen/bezorg', { lat: bezorgLat, lng: bezorgLng, tiers }).catch(() => null);
    if (!res || !res.ok) return;
    bezorgTiers = tiers.map((t) => ({ km: String(t.km).replace('.', ','), kosten: uitCenten(t.kosten), min: t.min ? uitCenten(t.min) : '' }));
    renderBezorgTiers();
    tekenBezorgCirkels();
    const status = $('#bezorgStatus');
    status.style.display = '';
    setTimeout(() => { status.style.display = 'none'; }, 2500);
});

/* De kaart pas opbouwen zodra het paneel zichtbaar is: Leaflet heeft echte maten nodig */
$$('[data-nav="instellingen"]').forEach((btn) => btn.addEventListener('click', () => setTimeout(initBezorgKaart, 80)));
renderBezorgTiers();

/* ── Instellingen bewerken via popups (geen onboarding meer nodig) ── */

const DAGNAMEN_VOL = { ma: 'Maandag', di: 'Dinsdag', wo: 'Woensdag', do: 'Donderdag', vr: 'Vrijdag', za: 'Zaterdag', zo: 'Zondag' };
let instSectie = null;

/* Alle kleurstijlen komen uit dezelfde bron als de templates zelf */
const STIJL_KLEUREN = window.PP_KLEUREN || [];
const STIJL_TABS = [['alle', 'Alle'], ['warm', 'Warm'], ['fris', 'Fris'], ['modern', 'Modern'], ['klassiek', 'Klassiek']];
let instLogo = null;        // gekozen logo in de stijl-popup
let instKleur = null;       // gekozen kleur in de stijl-popup
let instThema = null;       // gekozen template in de stijl-popup
let instKleurTab = 'alle';  // actieve filter-tab in de kleurkiezer

/* Templatekaarten met mini-voorbeeld, zoals in de onboarding */
const STIJL_THEMAS = [
    { id: 'template1', emoji: '<i class="fa-solid fa-moped" aria-hidden="true"></i>', naam: 'Presto', desc: 'Licht en modern' },
    { id: 'template2', emoji: '<i class="fa-solid fa-wine-glass" aria-hidden="true"></i>', naam: 'Notte', desc: 'Klassiek en verfijnd' },
    { id: 'template3', emoji: '<i class="fa-solid fa-bolt" aria-hidden="true"></i>', naam: 'Forza', desc: 'Bold en vol energie' },
    { id: 'template4', emoji: '<i class="fa-solid fa-camera" aria-hidden="true"></i>', naam: 'Giro', desc: 'Fris met grote foto\'s' },
];

function renderStijlThemas() {
    const grid = $('#iThemaGrid');
    if (!grid || !window.PP_TILES) return;
    grid.innerHTML = STIJL_THEMAS.map((t) => `
        <button type="button" data-i-thema="${t.id}"
            class="text-left rounded-2xl border-2 overflow-hidden cursor-pointer transition-colors bg-white ${instThema === t.id ? 'border-basil' : 'border-crema-dark hover:border-basil/50'}">
            <span class="block p-1.5 pb-0"><span class="block aspect-video">${window.PP_TILES[t.id](instKleur)}</span></span>
            <span class="block px-3 py-2">
                <span class="block text-sm font-extrabold text-cacao">${t.emoji} ${t.naam}</span>
                <span class="block text-xs font-bold text-cacao/50">${t.desc}</span>
            </span>
        </button>`).join('');
}

/* Live voorbeeld rechts in de popup: de echte pagina met de nog niet opgeslagen keuzes.
   Kijken en scrollen mag; klikken in het voorbeeld wordt geblokkeerd. */
let instPagina = 'menu';

function updateStijlPreview() {
    const frame = $('#iStijlPreview');
    if (!frame) return;
    const laad = $('#iStijlLaad');
    const klaar = () => { if (laad) laad.style.display = 'none'; };
    if (laad) laad.style.display = '';
    clearTimeout(updateStijlPreview.timer);
    updateStijlPreview.timer = setTimeout(klaar, 12000);   /* vangnet als laden blijft hangen */
    const slug = window.PP_SLUG || 'jouwpizzeria';
    const q = `thema=${instThema}&kleur=${encodeURIComponent(instKleur)}`;
    frame.src = instPagina === 'menu'
        ? `${location.origin}/bestellen/${slug}?${q}`
        : `${location.origin}/bestellen/${slug}/${instPagina === 'status' ? 'bestelling' : 'afrekenen'}?voorbeeld=1&${q}`;
    frame.onload = () => {
        try {
            frame.contentDocument.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); }, true);
        } catch { /* geen toegang: dan blijft het voorbeeld gewoon staan */ }
        clearTimeout(updateStijlPreview.timer);
        klaar();
    };
}

function renderKleurKiezer() {
    const grid = $('#iKleurGrid');
    if (!grid) return;
    $('#iKleurTabs').innerHTML = STIJL_TABS.map(([id, label]) =>
        `<button type="button" data-i-kleurtab="${id}" class="keuze-chip !py-1.5 !px-3 !text-xs ${instKleurTab === id ? 'aan' : ''}">${label}</button>`).join('');
    const lijst = STIJL_KLEUREN.filter((k) => instKleurTab === 'alle' || k.stijl === instKleurTab);
    grid.innerHTML = lijst.map((k) => `
        <button type="button" data-i-kleur="${k.hex}" title="${esc(k.naam)}" aria-label="${esc(k.naam)}"
            class="w-10 h-10 rounded-xl grid place-items-center text-white font-extrabold shadow-sm cursor-pointer shrink-0"
            style="background:${k.palet.primair}; ${k.hex === instKleur ? 'outline:3px solid var(--color-basil); outline-offset:2px;' : ''}">${k.hex === instKleur ? '<i class="fa-solid fa-check" aria-hidden="true"></i>' : ''}</button>`).join('');
    const gekozen = STIJL_KLEUREN.find((k) => k.hex === instKleur);
    $('#iKleurNaam').textContent = gekozen ? `Gekozen: ${gekozen.naam}` : '';
}

/* Logo verkleinen naar een vierkant PNG-thumbnailtje (behoudt transparantie) */
function logoNaarThumb(file) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = 128;
            canvas.height = 128;
            const ctx = canvas.getContext('2d');
            const zijde = Math.min(img.width, img.height);
            ctx.drawImage(img, (img.width - zijde) / 2, (img.height - zijde) / 2, zijde, zijde, 0, 0, 128, 128);
            URL.revokeObjectURL(url);
            resolve(canvas.toDataURL('image/png'));
        };
        img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('kan afbeelding niet lezen')); };
        img.src = url;
    });
}

const INST_SECTIES = {
    stijl: {
        kicker: 'Jouw stijl', titel: 'Style je bestelpagina', sub: 'Kies je kleur en logo; de rest van het kleurenpalet stemmen we automatisch af.',
        mascot: 'behind-laptop', bubble: 'Zo zien je klanten je pagina.', kant: 'right', breed: true,
        velden: () => {
            instLogo = DATA.logo || null;
            instKleur = STIJL_KLEUREN.some((k) => k.hex === DATA.color) ? DATA.color : (STIJL_KLEUREN[0]?.hex || '#B04A3F');
            instThema = ['template1', 'template2', 'template3', 'template4'].includes(DATA.theme) ? DATA.theme : 'template1';
            instKleurTab = 'alle';
            instPagina = 'menu';
            setTimeout(() => { renderKleurKiezer(); renderStijlThemas(); updateStijlPreview(); }, 0);
            return `
                <div class="grid lg:grid-cols-[1fr_1.2fr] gap-8 items-stretch">
                    <div>
                        <div class="h-8 mb-2 flex items-center">
                            <p class="m-label !mb-0">Template</p>
                        </div>
                        <div id="iThemaGrid" class="grid grid-cols-2 gap-3"></div>
                    </div>
                    <div class="flex flex-col">
                        <div class="h-8 mb-2 flex items-center justify-between gap-3">
                            <p class="m-label !mb-0">Live voorbeeld</p>
                            <div class="flex gap-1.5">
                                <button type="button" data-i-pagina="menu" class="keuze-chip !py-1 !px-3 !text-xs aan">Menu</button>
                                <button type="button" data-i-pagina="afrekenen" class="keuze-chip !py-1 !px-3 !text-xs">Afrekenen</button>
                                <button type="button" data-i-pagina="status" class="keuze-chip !py-1 !px-3 !text-xs">Status</button>
                            </div>
                        </div>
                        <div class="relative flex-1 min-h-0 w-full overflow-hidden rounded-2xl border-2 border-crema-dark bg-white aspect-video lg:aspect-auto">
                            <iframe id="iStijlPreview" title="Voorbeeld van je bestelpagina" style="width:200%;height:200%;transform:scale(.5);transform-origin:top left;border:0"></iframe>
                            <div id="iStijlLaad" class="absolute inset-0 grid place-items-center bg-white/75" style="display:none">
                                <div class="text-center">
                                    <i class="fa-solid fa-pizza-slice fa-spin text-3xl text-cacao/60" aria-hidden="true"></i>
                                    <p class="mt-2 text-sm font-extrabold text-cacao/60">Voorbeeld laden…</p>
                                </div>
                            </div>
                        </div>
                        <p class="m-hint mt-2">Scrollen kan, klikken staat uit in het voorbeeld. Pas na opslaan zien je klanten het.</p>
                    </div>
                </div>
                <div>
                    <p class="m-label">Jouw kleurstijl</p>
                    <div id="iKleurTabs" class="flex flex-wrap gap-2 mb-3"></div>
                    <div id="iKleurGrid" class="flex flex-wrap gap-2.5"></div>
                    <p id="iKleurNaam" class="m-hint mt-2"></p>
                </div>
                <div>
                    <p class="m-label">Jouw logo</p>
                    <div class="flex items-center gap-3">
                        <div id="iLogoVak" class="w-14 h-14 rounded-xl border-2 border-crema-dark bg-crema grid place-items-center overflow-hidden shrink-0">
                            ${instLogo ? `<img id="iLogoPreview" src="${instLogo}" class="w-full h-full object-cover" alt="">` : '<span class="text-xl text-cacao/30"><i class="fa-solid fa-pizza-slice" aria-hidden="true"></i></span>'}
                        </div>
                        <button type="button" data-i-logo-kies class="keuze-chip">${instLogo ? 'Ander logo kiezen' : 'Logo uploaden'}</button>
                        <button type="button" data-i-logo-weg class="skip-link !mt-0 ${instLogo ? '' : 'hidden'}">Verwijderen</button>
                        <input id="iLogoInp" type="file" accept="image/*" class="hidden">
                    </div>
                </div>`;
        },
        payload: () => ({ color: instKleur, logo: instLogo, theme: instThema }),
    },
    zaak: {
        kicker: 'Jouw zaak', titel: 'Gegevens van je zaak', sub: 'Zo kennen je klanten en wij je zaak.',
        mascot: 'tossing-dough', bubble: 'Aangenaam kennis te maken.', kant: 'right',
        velden: () => `
            <div><label class="m-label" for="iNaam">Naam van je zaak</label><input id="iNaam" class="m-inp" maxlength="60" value="${esc(DATA.name || '')}"></div>
            <div><label class="m-label" for="iPersoon">Contactpersoon</label><input id="iPersoon" class="m-inp" maxlength="60" value="${esc(DATA.person || '')}"></div>
            <div><label class="m-label" for="iTelefoon">Telefoonnummer</label><input id="iTelefoon" class="m-inp" maxlength="20" inputmode="tel" value="${esc(DATA.phone || '')}"></div>`,
        payload: () => {
            const name = $('#iNaam').value.trim();
            const person = $('#iPersoon').value.trim();
            if (!name) { $('#iNaam').classList.add('m-fout'); $('#iNaam').focus(); return null; }
            if (!person) { $('#iPersoon').classList.add('m-fout'); $('#iPersoon').focus(); return null; }
            return { name, person, phone: $('#iTelefoon').value.trim() };
        },
    },
    bedrijf: {
        kicker: 'Bedrijfsgegevens', titel: 'Jouw bedrijfsgegevens', sub: 'Met je adres zetten we je zaak ook meteen op de kaart bij je bezorggebied.',
        mascot: 'behind-laptop', bubble: 'Even de administratie.', kant: 'left',
        velden: () => `
            <div><label class="m-label" for="iKvk">KVK-nummer</label><input id="iKvk" class="m-inp" maxlength="8" inputmode="numeric" value="${esc(DATA.kvk || '')}"></div>
            <div><label class="m-label" for="iStraat">Straat + huisnummer</label><input id="iStraat" class="m-inp" maxlength="80" value="${esc(DATA.street || '')}"></div>
            <div class="grid grid-cols-[8rem_1fr] gap-3">
                <div><label class="m-label" for="iPostcode">Postcode</label><input id="iPostcode" class="m-inp" maxlength="10" value="${esc(DATA.zip || '')}"></div>
                <div><label class="m-label" for="iPlaats">Plaats</label><input id="iPlaats" class="m-inp" maxlength="60" value="${esc(DATA.city || '')}"></div>
            </div>`,
        payload: () => ({ kvk: $('#iKvk').value.trim(), street: $('#iStraat').value.trim(), zip: $('#iPostcode').value.trim(), city: $('#iPlaats').value.trim() }),
    },
    tijden: {
        kicker: 'Openingstijden', titel: 'Wanneer ben je open?', sub: 'Tik op een dag om hem open of dicht te zetten.',
        mascot: 'pizza-in-oven', bubble: 'De oven staat al aan.', kant: 'right',
        velden: () => Object.keys(DAGNAMEN_VOL).map((d) => {
            const aan = !!(DATA.days || {})[d];
            const t = (DATA.hoursMode === 'perday' && DATA.dayTimes && DATA.dayTimes[d])
                ? DATA.dayTimes[d]
                : { open: DATA.open || '16:00', close: DATA.close || '21:30' };
            return `<div class="flex items-center gap-2 !mt-2">
                <button type="button" class="keuze-chip !w-32 justify-center shrink-0 ${aan ? 'aan' : ''}" data-i-dag="${d}">${DAGNAMEN_VOL[d]}</button>
                <input type="time" class="m-inp !py-1.5" value="${t.open}" data-i-open="${d}" ${aan ? '' : 'disabled style="opacity:.4"'}>
                <span class="text-cacao/40 font-extrabold shrink-0">-</span>
                <input type="time" class="m-inp !py-1.5" value="${t.close}" data-i-close="${d}" ${aan ? '' : 'disabled style="opacity:.4"'}>
            </div>`;
        }).join(''),
        payload: () => {
            const days = {};
            const dayTimes = {};
            Object.keys(DAGNAMEN_VOL).forEach((d) => {
                const aan = $(`[data-i-dag="${d}"]`).classList.contains('aan');
                days[d] = aan;
                if (aan) dayTimes[d] = { open: $(`[data-i-open="${d}"]`).value || '16:00', close: $(`[data-i-close="${d}"]`).value || '21:30' };
            });
            const tijden = Object.values(dayTimes);
            const zelfde = tijden.length && tijden.every((t) => t.open === tijden[0].open && t.close === tijden[0].close);
            return zelfde
                ? { days, hoursMode: 'same', open: tijden[0].open, close: tijden[0].close, dayTimes: {} }
                : { days, hoursMode: 'perday', dayTimes, open: tijden[0]?.open || '16:00', close: tijden[0]?.close || '21:30' };
        },
    },
    punten: {
        kicker: 'Spaarpunten', titel: 'Klanten laten sparen?', sub: 'Klanten sparen automatisch punten met elke bestelling. Die punten leveren straks extra korting op bij het afrekenen.',
        mascot: 'waving-hello', bubble: 'Zo komen klanten terug.', kant: 'right',
        velden: () => {
            const nu = DATA.spaarpunten ? 'aan' : 'uit';
            const labels = { aan: 'Aan, klanten sparen automatisch mee', uit: 'Uit, geen spaarprogramma' };
            return `<div class="flex flex-col gap-2">${Object.keys(labels).map((p) =>
                `<button type="button" class="keuze-chip justify-center !py-2.5 ${nu === p ? 'aan' : ''}" data-i-keuze="${p}">${labels[p]}</button>`).join('')}</div>`;
        },
        payload: () => ({ spaarpunten: $('#instVelden .keuze-chip.aan')?.dataset.iKeuze === 'aan' }),
    },
    domein: {
        kicker: 'Jouw domein', titel: 'Waar bestellen je klanten?', sub: 'Een gratis subdomein, of je eigen domeinnaam.',
        mascot: 'showing-pizza-order', bubble: 'Een eigen adres voor je zaak.', kant: 'right',
        velden: () => {
            const slug = String(DATA.name || 'jouwpizzeria')
                .normalize('NFD').replace(/[̀-ͯ]/g, '')
                .toLowerCase().replace(/[^a-z0-9]+/g, '').slice(0, 30) || 'jouwpizzeria';
            const eigen = DATA.domainMode === 'own';
            /* Eigen domein is voor abonnees: in de proefperiode staat de keuze op slot */
            const proef = window.PP_ABO === false;
            return `
                <div class="flex flex-col gap-2">
                    <button type="button" class="keuze-chip justify-center !py-2.5 ${eigen ? '' : 'aan'}" data-i-keuze="sub">Gratis subdomein: ${slug}.mijnpizzeria.nl</button>
                    <button type="button" class="keuze-chip justify-center !py-2.5 ${eigen ? 'aan' : ''} ${proef ? 'opacity-40 cursor-not-allowed' : ''}" ${proef ? 'disabled title="Kan zodra je abonnement actief is"' : ''} data-i-keuze="own">Mijn eigen domein${proef ? ' <i class="fa-solid fa-lock" aria-hidden="true"></i>' : ''}</button>
                </div>
                ${proef ? '<p class="m-hint mt-1">Een eigen domein kan zodra je abonnement actief is.</p>' : ''}
                <div id="iEigenDomeinVak" class="${eigen ? '' : 'hidden'}">
                    <label class="m-label" for="iEigenDomein">Jouw domeinnaam</label>
                    <input id="iEigenDomein" class="m-inp" maxlength="100" placeholder="pizzeriamario.nl" value="${esc(DATA.ownDomain || '')}">
                    <p class="m-hint mt-1">Bestellen gaat dan via bestellen.jouwdomein.nl</p>
                </div>`;
        },
        payload: () => ({
            domainMode: $('#instVelden .keuze-chip.aan')?.dataset.iKeuze || 'sub',
            ownDomain: $('#iEigenDomein')?.value.trim() || '',
        }),
    },
};

function openInstModal(sectie) {
    instSectie = sectie;
    const info = INST_SECTIES[sectie];
    if (!info) return;
    $('#instKicker').textContent = info.kicker;
    $('#instTitel').textContent = info.titel;
    $('#instSub').textContent = info.sub;
    /* Huisstijl 2.0: de wizard-mascotte is uit het ontwerp */
    $('#instKaartWrap').classList.toggle('max-w-xl', !info.breed);
    $('#instKaartWrap').classList.toggle('max-w-7xl', !!info.breed);
    $('#instVelden').innerHTML = info.velden();
    $('#instModal').classList.remove('hidden');
    setTimeout(() => $('#instVelden input:not([disabled])')?.focus(), 80);
}

function sluitInstModal() {
    $('#instModal').classList.add('hidden');
    instSectie = null;
}

document.addEventListener('click', (e) => {
    const knop = e.target.closest('[data-inst]');
    if (knop && knop.dataset.inst) openInstModal(knop.dataset.inst);
});

$('#instVelden')?.addEventListener('click', (e) => {
    const thema = e.target.closest('[data-i-thema]');
    if (thema) {
        instThema = thema.dataset.iThema;
        renderStijlThemas();
        updateStijlPreview();
        return;
    }
    const pagina = e.target.closest('[data-i-pagina]');
    if (pagina) {
        instPagina = pagina.dataset.iPagina;
        $$('#instVelden [data-i-pagina]').forEach((el) => el.classList.toggle('aan', el === pagina));
        updateStijlPreview();
        return;
    }
    const kleurTab = e.target.closest('[data-i-kleurtab]');
    if (kleurTab) {
        instKleurTab = kleurTab.dataset.iKleurtab;
        renderKleurKiezer();
        return;
    }
    const kleur = e.target.closest('[data-i-kleur]');
    if (kleur) {
        instKleur = kleur.dataset.iKleur;
        renderKleurKiezer();
        renderStijlThemas();
        updateStijlPreview();
        return;
    }
    if (e.target.closest('[data-i-logo-kies]')) return $('#iLogoInp').click();
    if (e.target.closest('[data-i-logo-weg]')) {
        instLogo = null;
        $('#iLogoVak').innerHTML = '<span class="text-xl text-cacao/30"><i class="fa-solid fa-pizza-slice" aria-hidden="true"></i></span>';
        $('#instVelden [data-i-logo-kies]').textContent = 'Logo uploaden';
        $('#instVelden [data-i-logo-weg]').classList.add('hidden');
        return;
    }
    const dag = e.target.closest('[data-i-dag]');
    if (dag) {
        const aan = dag.classList.toggle('aan');
        ['open', 'close'].forEach((kant) => {
            const inp = $(`[data-i-${kant}="${dag.dataset.iDag}"]`);
            inp.disabled = !aan;
            inp.style.opacity = aan ? '' : '.4';
        });
        return;
    }
    const keuze = e.target.closest('[data-i-keuze]');
    if (keuze) {
        $$('#instVelden [data-i-keuze]').forEach((el) => el.classList.toggle('aan', el === keuze));
        $('#iEigenDomeinVak')?.classList.toggle('hidden', keuze.dataset.iKeuze !== 'own');
        if (keuze.dataset.iKeuze === 'own') $('#iEigenDomein')?.focus();
    }
});

$('#instOpslaan')?.addEventListener('click', async () => {
    if (!instSectie) return;
    const payload = INST_SECTIES[instSectie].payload();
    if (!payload) return;
    const res = await postJson('/instellingen/gegevens', payload).catch(() => null);
    if (!res || !res.ok) return;
    /* Verse herlaad zodat drukte, checklist en waarschuwingen overal meebewegen */
    history.replaceState(null, '', '/dashboard/instellingen');
    location.reload();
});

$('#instModalSluit')?.addEventListener('click', sluitInstModal);
$('#instModal')?.addEventListener('click', (e) => {
    if (e.target === $('#instModal') || e.target === $('#instModalMidden')) sluitInstModal();
});
$('#instModal')?.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') sluitInstModal();
});
$('#instVelden')?.addEventListener('input', (e) => e.target.classList.remove('m-fout'));

/* Logo-upload in de stijl-popup */
$('#instVelden')?.addEventListener('change', async (e) => {
    if (e.target.id !== 'iLogoInp') return;
    const file = e.target.files?.[0];
    if (!file) return;
    try {
        instLogo = await logoNaarThumb(file);
        $('#iLogoVak').innerHTML = `<img src="${instLogo}" class="w-full h-full object-cover" alt="">`;
        $('#instVelden [data-i-logo-kies]').textContent = 'Ander logo kiezen';
        $('#instVelden [data-i-logo-weg]').classList.remove('hidden');
    } catch { /* onleesbaar bestand: laat alles staan */ }
    e.target.value = '';
});

/* ── Bestelpagina-paneel: link, kopieerknop, live voorbeeld en status ── */

/* Pizza Coach: slapende klanten bekijken en de combo-upsell aan of uit zetten */
(function coach() {
    $('#coachSlapendKnop')?.addEventListener('click', () => $('#coachSlapendModal').classList.remove('hidden'));
    $('#coachSlapendSluit')?.addEventListener('click', () => $('#coachSlapendModal').classList.add('hidden'));
    $('#coachSlapendModal')?.addEventListener('click', (e) => {
        if (!e.target.closest('.animate-pop')) $('#coachSlapendModal').classList.add('hidden');
    });

    const zetPin = async (waarde) => {
        const res = await postJson('/instellingen/gegevens', { upsellPin: waarde }).catch(() => null);
        if (res) {
            history.replaceState(null, '', '/dashboard');
            location.reload();
        }
    };
    document.querySelector('[data-coach-pin]')?.addEventListener('click', (e) => zetPin(Number(e.currentTarget.dataset.coachPin)));
    document.querySelector('[data-coach-pin-uit]')?.addEventListener('click', () => zetPin(null));
})();

(function paginaPaneel() {
    const linkTekst = $('#paginaLink');
    if (!linkTekst) return;
    const slug = window.PP_SLUG || 'jouwpizzeria';
    const link = window.PP_BESTEL_URL || `${location.origin}/bestellen/${slug}`;
    linkTekst.textContent = link;
    $('#paginaOpen').href = link;
    $('#paginaKopieer').addEventListener('click', async () => {
        try { await navigator.clipboard.writeText(link); } catch { /* stil */ }
        $('#paginaKopieer').textContent = 'Gekopieerd';
        setTimeout(() => { $('#paginaKopieer').textContent = 'Kopieer link'; }, 1600);
    });

    /* Het voorbeeld pas laden zodra het instellingen-paneel opengaat */
    $$('[data-nav="instellingen"]').forEach((knop) => knop.addEventListener('click', () => {
        const frame = $('#paginaPreview');
        if (frame && !frame.src) frame.src = link;
    }));
    if (location.hash === '#instellingen' || location.pathname.endsWith('/instellingen')) {
        const frame = $('#paginaPreview');
        if (frame && !frame.src) frame.src = link;
    }
})();

/* Startpaneel uit het pad (/dashboard/webshop); de oude #hash blijft werken als fallback */
const startPaneel = location.pathname.split('/')[2] || (location.hash.length > 1 ? location.hash.slice(1) : '');
if (startPaneel) document.querySelector(`.rail-item[data-nav="${startPaneel}"]`)?.click();

/* Mislukte of afgebroken betaling: even melden, verder niks kapot */
if (typeof window.PP_ABO_FOUT === 'string' && window.PP_ABO_FOUT) dashToast(window.PP_ABO_FOUT, 5200);

/* Melding bij een net geactiveerd abonnement: een toast midden onderin het scherm */
(function abonnementFeest() {
    if (window.PP_ABO_FEEST !== true) return;
    dashToast('Je abonnement is geactiveerd.', 5200);
})();

/* Abonnement-overlay: komt terug zodra iemand hem via devtools weghaalt of verbergt.
   De server weigert acties zonder abonnement sowieso, dit is alleen de voorkant. */
(function abonnementWacht() {
    if (!document.querySelector('#abonnementOverlay')) return;
    new MutationObserver(() => {
        const overlay = document.querySelector('#abonnementOverlay');
        if (!overlay || overlay.style.display === 'none' || overlay.style.visibility === 'hidden' || overlay.hidden) {
            location.reload();
        }
    }).observe(document.documentElement, { childList: true, subtree: true, attributes: true });
})();


/* ── Topbalk: de klok tikt per kwart minuut ─────────────────────── */
const topbalkKlok = $('#topbalkKlok');
if (topbalkKlok) {
    const topbalkTik = () => {
        topbalkKlok.textContent = new Date().toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' });
    };
    topbalkTik();
    setInterval(topbalkTik, 15000);
}
