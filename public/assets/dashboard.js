/* ═══════════════ Dashboard: app-gevoel ═══════════════ */

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => [...document.querySelectorAll(sel)];
const DATA = window.PP_DATA || {};
const esc = (str) => String(str).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* ── Begroeting op basis van het moment van de dag ────────────── */

(function dagregel() {
    const uur = new Date().getHours();
    const deel = uur < 6 ? 'Nachtbraker' : uur < 12 ? 'Goedemorgen' : uur < 18 ? 'Goedemiddag' : 'Goedenavond';
    const datum = new Date().toLocaleDateString('nl-NL', { weekday: 'long', day: 'numeric', month: 'long' });
    $('#dagregel').textContent = `${deel}! Het is vandaag ${datum}.`;
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
    window.scrollTo({ top: 0, behavior: 'smooth' });
}));

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
    { label: 'Menukaart gevuld (3+ gerechten)', emoji: '📋', af: (DATA.menu || []).length >= 3, stap: 'menu' },
    { label: 'Logo geüpload', emoji: '🖼️', af: !!DATA.logo, stap: 'style' },
    { label: 'Betaalmethode gekozen', emoji: '💶', af: !!DATA.payment && DATA.payment !== 'later', stap: 'payment' },
    { label: 'Bedrijfsgegevens compleet', emoji: '📇', af: !!DATA.kvk && !!DATA.street, stap: 'company' },
    { label: 'Telefoonnummer toegevoegd', emoji: '📞', af: !!DATA.phone, stap: 'contact' },
];

(function renderChecklist() {
    const lijst = $('#checklist');
    if (!lijst) return;
    lijst.innerHTML = CHECKS.map((c) =>
        `<button type="button" class="check-item ${c.af ? 'af' : ''}" data-stap="${c.stap}">
            <span class="c-dot">✓</span>
            <span>${c.emoji}</span>
            <span class="c-lbl">${c.label}</span>
        </button>`
    ).join('');
    lijst.addEventListener('click', (e) => {
        const item = e.target.closest('[data-stap]');
        if (item) window.location.href = `/onboarding?stap=${item.dataset.stap}`;
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
    const menu = DATA.menu || [];
    if (menu.length) {
        const cats = (DATA.categories || []).filter((c) => menu.some((m) => m.cat === c.id)).length;
        $('#menuSamenvatting').textContent = `Je hebt ${menu.length} gerecht${menu.length === 1 ? '' : 'en'} in ${cats} categorie${cats === 1 ? '' : 'ën'} staan.`;
    }

    if (DATA.days) {
        const namen = { ma: 'ma', di: 'di', wo: 'wo', do: 'do', vr: 'vr', za: 'za', zo: 'zo' };
        const open = Object.keys(namen).filter((d) => DATA.days[d]);
        if (open.length) {
            const label = open.length === 7 ? 'elke dag' : open.map((d) => namen[d]).join(', ');
            $('#tijdenSamenvatting').textContent = DATA.hoursMode === 'perday'
                ? `Open op ${label}, met eigen tijden per dag.`
                : `Open op ${label}, van ${DATA.open || '16:00'} tot ${DATA.close || '21:30'}.`;
        }
    }

    const THEMA_NAMEN = { fresco: 'Fresco', nero: 'Nero', napoli: 'Napoli', puro: 'Puro', blocco: 'Blocco', retro: 'Retro' };
    if (DATA.theme) {
        $('#paginaSamenvatting').innerHTML = `Template: <b>${THEMA_NAMEN[DATA.theme] || 'Fresco'}</b>${DATA.logo ? ', met logo' : ''}. Jouw kleur: <span class="inline-block w-3.5 h-3.5 rounded-full align-middle" style="background:${esc(DATA.color || '#E63946')}"></span>`;
    }
})();

/* ── Demo-bestelling afspelen ─────────────────────────────────── */

const GERECHTEN = [['Margherita', 950], ['Salami', 1100], ['Quattro Formaggi', 1250], ['Diavola', 1200], ['Calzone', 1250], ['Cola', 250]];
const KLANTEN = ['Sanne', 'Ahmed', 'Julia', 'Daan', 'Fatima', 'Ruben', 'Lisa', 'Tom'];
const STATUSSEN = ['Nieuw', 'In de oven', 'Onderweg', 'Bezorgd'];
let orderNr = 412;

$('#demoOrderBtn')?.addEventListener('click', () => {
    const aantal = 1 + Math.floor(Math.random() * 3);
    const items = Array.from({ length: aantal }, () => GERECHTEN[Math.floor(Math.random() * GERECHTEN.length)]);
    const totaal = (items.reduce((s, [, p]) => s + p, 0) / 100).toFixed(2).replace('.', ',');
    const klant = KLANTEN[Math.floor(Math.random() * KLANTEN.length)];
    orderNr += 1 + Math.floor(Math.random() * 4);

    const card = document.createElement('div');
    card.className = 'dash-card order-card';
    card.innerHTML = `
        <div class="flex items-center justify-between gap-3 mb-2">
            <p class="font-display text-lg">#${orderNr} voor ${klant}</p>
            <p class="font-display text-lg">€ ${totaal}</p>
        </div>
        <p class="text-sm font-extrabold text-cacao/55 mb-3">${items.map(([n]) => '1× ' + n).join(', ')}</p>
        <div class="flex gap-1.5 flex-wrap">
            ${STATUSSEN.map((s) => `<span class="order-step text-[11px] font-extrabold rounded-full px-2.5 py-1 bg-crema text-cacao/45">${s}</span>`).join('')}
        </div>`;
    $('#demoOrders').prepend(card);

    const stappen = [...card.querySelectorAll('.order-step')];
    let i = 0;
    stappen[0].classList.add('nu');
    const timer = setInterval(() => {
        stappen[i].classList.remove('nu');
        stappen[i].classList.add('klaar');
        i++;
        if (i >= stappen.length) { clearInterval(timer); card.style.opacity = '.6'; return; }
        stappen[i].classList.add('nu');
    }, 1500);
});

/* ── Tip-mascotte ─────────────────────────────────────────────── */

const TIPS = [
    'Zet je populairste pizza bovenaan je menukaart 📈',
    'Een eigen foto bij een gerecht verkoopt beter dan een icoontje 📸',
    'Houd je openingstijden actueel, een misgelopen bestelling is zonde 🕐',
    'Deel je bestelpagina op social media zodra je live staat 📱',
    'Straks sparen je klanten punten via de QR op de doos 🎁',
    'Een logo maakt je pagina in één klap professioneler 🖼️',
];

$('#tipMascotte')?.addEventListener('click', () => {
    const mascotte = $('#tipMascotte');
    $('#tipBubble').textContent = TIPS[Math.floor(Math.random() * TIPS.length)];
    mascotte.classList.remove('wieg');
    void mascotte.offsetWidth;
    mascotte.classList.add('wieg');
});

/* ── Online gaan + openingstijden van vandaag ─────────────────── */

const STATUS = window.PP_STATUS || { online: false, mode: 'bezorgen_afhalen' };

function renderStatus() {
    $('#statusDot').classList.toggle('aan', STATUS.online);
    $('#statusTitel').textContent = STATUS.online
        ? (STATUS.mode === 'alleen_afhalen' ? 'Je staat online, klanten kunnen afhalen' : 'Je staat online, klanten kunnen bestellen')
        : 'Je bent offline';
    const knop = $('#onlineToggle');
    knop.textContent = STATUS.online ? 'Ga offline' : 'Online gaan 🟢';
    knop.classList.toggle('btn-grey', STATUS.online);
    $('#modeSwitch').classList.toggle('hidden', !STATUS.online);
    $$('#modeSwitch [data-mode]').forEach((b) => b.classList.toggle('on', b.dataset.mode === STATUS.mode));

    /* Altijd zichtbaar in de navigatie (rail + mobiele tabbalk) */
    $('#railDot')?.classList.toggle('aan', STATUS.online);
    $('#tabDot')?.classList.toggle('aan', STATUS.online);
    const railTitel = $('#railStatusTitel');
    const railSub = $('#railStatusSub');
    if (railTitel) railTitel.textContent = STATUS.online ? 'Online' : 'Offline';
    if (railSub) railSub.textContent = STATUS.online
        ? (STATUS.mode === 'alleen_afhalen' ? 'neemt bestellingen aan (alleen afhalen)' : 'neemt bestellingen aan')
        : 'neemt geen bestellingen aan';
}

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

$('#onlineToggle')?.addEventListener('click', () => {
    STATUS.online = !STATUS.online;
    renderStatus();
    bewaarStatus();
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
    if (!DATA.days) { el.textContent = '🕐 Stel eerst je openingstijden in'; return; }
    if (!DATA.days[key]) { el.textContent = '🕐 Vandaag ben je gesloten'; return; }
    const tijd = (DATA.hoursMode === 'perday' && DATA.dayTimes && DATA.dayTimes[key])
        ? DATA.dayTimes[key]
        : { open: DATA.open || '16:00', close: DATA.close || '21:30' };
    el.textContent = `🕐 Vandaag open van ${tijd.open} tot ${tijd.close}`;
})();

renderStatus();

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
