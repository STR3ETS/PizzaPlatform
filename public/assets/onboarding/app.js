/* ═══════════════ Shop & Eat onboarding wizard ═══════════════ */

/* Eigen scope: dit script draait ook in het dashboard (venster "zaak afmaken"),
   naast dashboard.js dat dezelfde hulpnamen ($, $$, esc) gebruikt. */
(() => {

/* De onboarding is ook de registratie: gasten krijgen alleen wat echt nodig is
   voor een account. Ingelogd zijn alle stappen beschikbaar, voor het aanvullen
   vanuit het dashboard (de setup-banner) en losse bewerkingen. */
const IS_AUTH = window.PP_AUTH === true;
/* Modal-modus: het venster "zaak afmaken" in het dashboard, direct na de registratie.
   Alleen de vervolgstappen; na afronden herlaadt het dashboard zonder venster. */
const IS_MODAL = window.PP_MODAL === true;
const STEPS = IS_MODAL
    ? ['hours', 'punten', 'domain', 'style']   /* geen menukaart en geen overzicht: huisstijl is de laatste vraag */
    : IS_AUTH
        ? ['name', 'person', 'contact', 'company', 'hours', 'menu', 'punten', 'domain', 'style', 'overview']
        : ['name', 'person', 'contact', 'wachtwoord', 'company', 'overview'];
const QUESTION_STEPS = STEPS.filter((s) => s !== 'overview');
const STORAGE_KEY = 'pp_onboarding_v2';

const DAYS = [
    { key: 'ma', label: 'ma' }, { key: 'di', label: 'di' }, { key: 'wo', label: 'wo' },
    { key: 'do', label: 'do' }, { key: 'vr', label: 'vr' }, { key: 'za', label: 'za' }, { key: 'zo', label: 'zo' },
];

/* Voorgestelde categorieën, elk met eigen quick-add gerechten */
const CATEGORY_SUGGESTIONS = [
    {
        key: 'klassiekers', emoji: '🍕', name: 'Klassiekers', presets: [
            { emoji: '🍕', name: 'Margherita', price: '9,50' },
            { emoji: '🍕', name: 'Salami', price: '11,00' },
            { emoji: '🍄', name: 'Funghi', price: '10,50' },
            { emoji: '🧀', name: 'Quattro Formaggi', price: '12,50' },
            { emoji: '🍍', name: 'Hawaï', price: '11,50' },
            { emoji: '🥟', name: 'Calzone', price: '12,50' },
        ],
    },
    {
        key: 'vlees', emoji: '🥩', name: 'Vlees', presets: [
            { emoji: '🌶️', name: 'Diavola', price: '12,00' },
            { emoji: '🥙', name: 'Shoarma', price: '13,50' },
            { emoji: '🍗', name: 'Pollo', price: '12,50' },
            { emoji: '🥓', name: 'Speciale', price: '12,50' },
        ],
    },
    {
        key: 'vis', emoji: '🐟', name: 'Vis', presets: [
            { emoji: '🐟', name: 'Tonno', price: '12,00' },
            { emoji: '🦐', name: 'Frutti di Mare', price: '14,00' },
            { emoji: '🍣', name: 'Salmone', price: '14,50' },
            { emoji: '🍤', name: 'Gamberetti', price: '13,50' },
        ],
    },
    {
        key: 'vega', emoji: '🥦', name: 'Vega', presets: [
            { emoji: '🥦', name: 'Vegetariana', price: '11,50' },
            { emoji: '🫑', name: 'Ortolana', price: '12,00' },
            { emoji: '🍅', name: 'Caprese', price: '11,00' },
            { emoji: '🌿', name: 'Pesto Verde', price: '12,00' },
        ],
    },
    {
        key: 'snacks', emoji: '🍢', name: 'Snacks', presets: [
            { emoji: '🌭', name: 'Frikandel', price: '2,50' },
            { emoji: '🧆', name: 'Kroket', price: '2,50' },
            { emoji: '🍗', name: 'Kipcorn', price: '3,00' },
            { emoji: '🧀', name: 'Kaassoufflé', price: '2,75' },
            { emoji: '🍢', name: 'Bamischijf', price: '2,75' },
            { emoji: '🥠', name: 'Loempia', price: '3,50' },
        ],
    },
    {
        key: 'friet', emoji: '🍟', name: 'Friet', presets: [
            { emoji: '🍟', name: 'Friet klein', price: '3,00' },
            { emoji: '🍟', name: 'Friet groot', price: '4,00' },
            { emoji: '🥫', name: 'Patat oorlog', price: '4,50' },
            { emoji: '🥙', name: 'Kapsalon', price: '9,50' },
        ],
    },
    {
        key: 'broodjes', emoji: '🥖', name: 'Broodjes', presets: [
            { emoji: '🥙', name: 'Broodje shoarma', price: '7,50' },
            { emoji: '🍔', name: 'Hamburger', price: '6,50' },
            { emoji: '🌭', name: 'Broodje frikandel', price: '4,00' },
            { emoji: '🥖', name: 'Broodje kroket', price: '4,00' },
        ],
    },
    {
        key: 'dranken', emoji: '🥤', name: 'Dranken', presets: [
            { emoji: '🥤', name: 'Cola', price: '2,50' },
            { emoji: '🍊', name: 'Fanta', price: '2,50' },
            { emoji: '💧', name: 'Spa blauw', price: '2,00' },
            { emoji: '🧋', name: 'Ice tea', price: '2,75' },
        ],
    },
    {
        key: 'desserts', emoji: '🍰', name: 'Desserts', presets: [
            { emoji: '🍰', name: 'Tiramisu', price: '5,50' },
            { emoji: '🍨', name: 'Vanille-ijs', price: '4,00' },
            { emoji: '🍩', name: 'Churros', price: '5,00' },
            { emoji: '🍫', name: 'Choco Calzone', price: '6,50' },
        ],
    },
];

const PICKER_EMOJIS = ['🍕', '🍟', '🌭', '🍔', '🧆', '🍢', '🥠', '🧀', '🍄', '🌶️', '🥩', '🍗', '🥓', '🥙', '🐟', '🍤', '🦐', '🍣', '🥦', '🫑', '🍅', '🌿', '🥗', '🍍', '🫒', '🥟', '🍝', '🥖', '🥤', '🍊', '🧋', '☕', '🍺', '🍷', '🍰', '🍨', '🍩', '🍫', '⭐', '🔥', '👨‍🍳', '🧄'];

/* Alle kleurstijlen komen uit dezelfde bron als de templates (gehydrateerd door de server) */
const KLEUREN = window.PP_KLEUREN || [{ hex: '#E63946', naam: 'Tomatenrood', stijl: 'warm', palet: { primair: '#E63946', primairDonker: '#B02A35', secundair: '#38151A', secundairLicht: '#54262C', witWarm: '#FFF6F5' } }];
const KLEUR_TABS = [['alle', 'Alle'], ['warm', 'Warm'], ['fris', 'Fris'], ['modern', 'Modern'], ['klassiek', 'Klassiek']];
let kleurTab = 'alle';

/* De templates: Presto (licht en modern) en Notte (donker en chic). De hoofdkleur bepaalt de rest van het palet. */
const THEMES = [
    {
        id: 'template1', name: 'Presto', desc: 'Licht en modern',
        font: '"Inter Tight", sans-serif', uppercase: false,
        cardBg: '#FFF7F0', itemBg: '#FFFFFF', text: '#3E2716', priceCol: 'rgba(62,39,22,.55)',
        headerUseAccent: true, itemRadius: '.7rem', btnRadius: '9999px',
    },
    {
        id: 'template2', name: 'Notte', desc: 'Klassiek en verfijnd',
        font: '"Fraunces", serif', uppercase: false,
        cardBg: '#FFF7F0', itemBg: '#FFFFFF', text: '#3E2716', priceCol: '#8A5A2B',
        headerUseAccent: false, itemRadius: '.4rem', btnRadius: '.25rem',
    },
    {
        id: 'template3', name: 'Forza', desc: 'Bold en vol energie',
        font: '"Anton", sans-serif', uppercase: true,
        cardBg: '#FFF7F0', itemBg: '#FFFFFF', text: '#3E2716', priceCol: '#3E2716',
        headerUseAccent: true, itemRadius: '0', btnRadius: '0',
    },
    {
        id: 'template4', name: 'Giro', desc: 'Fris met grote fotos',
        font: '"Plus Jakarta Sans", sans-serif', uppercase: false,
        cardBg: '#FFFFFF', itemBg: '#FFFFFF', text: '#3E2716', priceCol: '#3E2716',
        headerUseAccent: false, itemRadius: '1rem', btnRadius: '9999px',
    },
];

/* Van één hoofdkleur naar het volledige palet: zelfde tint, vaste rollen */
function hexNaarHsl(hex) {
    const n = hex.replace('#', '');
    const r = parseInt(n.slice(0, 2), 16) / 255;
    const g = parseInt(n.slice(2, 4), 16) / 255;
    const b = parseInt(n.slice(4, 6), 16) / 255;
    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    const l = (max + min) / 2;
    if (max === min) return [0, 0, l * 100];
    const d = max - min;
    const s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
    let h;
    if (max === r) h = ((g - b) / d + (g < b ? 6 : 0));
    else if (max === g) h = (b - r) / d + 2;
    else h = (r - g) / d + 4;
    return [h * 60, s * 100, l * 100];
}

function hslNaarHex(h, s, l) {
    s /= 100; l /= 100;
    const k = (n) => (n + h / 30) % 12;
    const a = s * Math.min(l, 1 - l);
    const f = (n) => l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)));
    const kanaal = (x) => Math.round(255 * x).toString(16).padStart(2, '0');
    return '#' + kanaal(f(0)) + kanaal(f(8)) + kanaal(f(4));
}

function paletVoor(hex) {
    const kleur = KLEUREN.find((k) => k.hex === hex);
    if (kleur) return kleur.palet;
    const [h] = hexNaarHsl(hex || '#F97316');
    return {
        primair: hex || '#F97316',
        primairDonker: hslNaarHex(h, 88, 32),
        secundair: hslNaarHex(h, 35, 17),
        secundairLicht: hslNaarHex(h, 45, 24),
        witWarm: hslNaarHex(h, 100, 97),
    };
}

let state = {
    step: 'name',
    name: '', person: '', email: '', phone: '',
    kvk: '', street: '', zip: '', city: '',
    days: { ma: false, di: true, wo: true, do: true, vr: true, za: true, zo: true },
    open: '16:00', close: '21:30',
    hoursMode: 'same',           // 'same' | 'perday'
    dayTimes: {},                // { ma: { open, close }, ... } alleen gebruikt bij 'perday'
    categories: [{ id: 'klassiekers', emoji: '🍕', name: 'Klassiekers' }],
    activeCat: 'klassiekers',
    menu: [],                    // { cat, icon: {t:'e'|'p', v:emoji|dataURL}, name, price }
    spaarpunten: true,           // klanten sparen automatisch punten voor korting
    domainMode: 'sub', ownDomain: '',
    color: '#E63946',
    theme: 'template1',
    logo: null,
    returnTo: null,
    submitted: false,
};

let addingCat = false;           // "eigen categorie"-invoer open?
let pickerIdx = null;            // menu-index waarvoor de icoon-kiezer open staat
let pw = '';                     // wachtwoord bewust NIET in localStorage
let pw2 = '';

/* Wachtwoord-eisen: gedeeld door de checklist in de stap en de validatie */
const PW_EISEN = {
    lengte: (w) => w.length >= 8,
    hoofdletter: (w) => /[A-Z]/.test(w),
    kleineletter: (w) => /[a-z]/.test(w),
    cijfer: (w) => /[0-9]/.test(w),
};
const pwVoldoet = () => Object.values(PW_EISEN).every((eis) => eis(pw));

function renderPwChecklist() {
    Object.entries(PW_EISEN).forEach(([naam, eis]) => {
        const rij = document.querySelector(`[data-pweis="${naam}"]`);
        if (!rij) return;
        const ok = eis(pw);
        rij.classList.toggle('aan', ok);
        rij.querySelector('.pw-dot').innerHTML = ok
            ? '<i class="fa-solid fa-check" aria-hidden="true"></i>'
            : '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
    });
}

const $  = (sel) => document.querySelector(sel);
const $$ = (sel) => [...document.querySelectorAll(sel)];
const stepEl = (id) => $(`[data-step="${id}"]`);
const esc = (str) => String(str).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* ── Opslaan & herstellen ─────────────────────────────────────── */

function save() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
}

function normaliseerState() {
    if (!STEPS.includes(state.step)) state.step = STEPS[0];
    if (!THEMES.some((t) => t.id === state.theme)) state.theme = 'template1';
    state.spaarpunten = state.spaarpunten !== false;   // standaard aan
    if (!state.categories?.length) {
        state.categories = [{ id: 'klassiekers', emoji: '🍕', name: 'Klassiekers' }];
    }
    if (!state.categories.some((c) => c.id === state.activeCat)) state.activeCat = state.categories[0].id;
    state.menu = (state.menu || []).map((m) => ({
        ...(m.id ? { id: m.id } : {}),
        cat: m.cat && state.categories.some((c) => c.id === m.cat) ? m.cat : state.categories[0].id,
        icon: m.icon?.t === 'p' && m.icon.v ? m.icon : null,   /* geen emoji-icoontjes meer: foto of niets */
        name: m.name, price: m.price,
    }));
}

/* Heb je al een echte menukaart in je dashboard, dan is die hier leidend:
   je ziet en bewerkt in deze stap gewoon je huidige menu. */
function laadEchteMenukaart() {
    const echt = window.PP_MENU;
    if (!Array.isArray(echt) || !echt.length) return;
    const cats = [];
    const catVoor = (naam) => {
        let cat = cats.find((c) => c.name === naam);
        if (!cat) {
            cat = { id: 'cat-' + slugify(naam) + '-' + cats.length, emoji: '🍽️', name: naam };
            cats.push(cat);
        }
        return cat.id;
    };
    state.menu = echt.map((m) => ({
        id: m.id,
        cat: catVoor(m.categorie || 'Menu'),
        icon: m.foto ? { t: 'p', v: m.foto } : null,
        name: m.naam,
        price: ((m.prijs || 0) / 100).toFixed(2).replace('.', ','),
    }));
    state.categories = cats;
    state.activeCat = cats[0].id;
}

function restore() {
    /* Ingelogd: je opgeslagen onboarding uit de database is leidend,
       zodat je nooit iets opnieuw hoeft in te vullen */
    if (IS_AUTH && window.PP_SAVED && typeof window.PP_SAVED === 'object') {
        state = { ...state, ...window.PP_SAVED, step: STEPS[0], returnTo: null, submitted: false };
        laadEchteMenukaart();
        normaliseerState();
        return false;
    }
    try {
        const saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
        if (!saved || saved.submitted) { localStorage.removeItem(STORAGE_KEY); return false; }
        state = { ...state, ...saved, returnTo: null };
        normaliseerState();
        return state.step !== 'name';
    } catch { return false; }
}

function resetAll() {
    localStorage.removeItem(STORAGE_KEY);
    location.reload();
}

/* ── Navigatie ────────────────────────────────────────────────── */

let animating = false;

function show(id, { animate = true } = {}) {
    if (animating) return;
    const from = stepEl(state.step);
    const to = stepEl(id);
    if (!to) return;

    const swap = () => {
        from?.classList.add('hidden');
        from?.classList.remove('step-exit');
        state.step = id;
        onEnterStep(id);
        to.classList.remove('hidden');
        /* Alleen animeren bij een echte stap-wissel: bij het eerste laden zou de
           transform de zwevende mobiele knoppen tijdelijk aan de stap koppelen */
        if (animate) {
            to.classList.add('step-enter');
            setTimeout(() => to.classList.remove('step-enter'), 450);
        }
        updateChrome();
        save();
        ($('#setupOverlay') || window).scrollTo({ top: 0 });
        animating = false;
        const firstInput = to.querySelector('input:not([type="time"])');
        if (firstInput && window.matchMedia('(hover: hover)').matches) firstInput.focus();
    };

    if (animate && from && from !== to) {
        animating = true;
        from.classList.add('step-exit');
        setTimeout(swap, 180);
    } else {
        swap();
    }
}

/* Bestaat er al een account met dit adres? Dan houden we het hier al tegen,
   niet pas bij het versturen. De server telt het eigen account niet mee. */
let emailCheckBezig = false;
async function emailVrij() {
    if (emailCheckBezig) return false;
    emailCheckBezig = true;
    try {
        const res = await fetch('/onboarding/email-check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ email: state.email.trim() }),
        });
        const data = res.ok ? await res.json() : { bestaat: false };
        if (data.bestaat) {
            const el = $('[data-err="email"]');
            if (el) el.innerHTML = 'Er bestaat al een account met dit e-mailadres. <a href="/login" class="underline font-semibold">Log hier in</a> om verder te gaan.';
            nudge($('#inpEmail'));
            return false;
        }
        return true;
    } catch {
        return true;   /* server even niet bereikbaar: bij het afronden checken we sowieso opnieuw */
    } finally {
        emailCheckBezig = false;
    }
}

async function next() {
    if (!validate(state.step)) return;
    if (state.step === 'contact' && ! await emailVrij()) return;
    if (state.returnTo) {
        const target = state.returnTo;
        state.returnTo = null;
        show(target);
        return;
    }
    const i = STEPS.indexOf(state.step);
    if (i < STEPS.length - 1) show(STEPS[i + 1]);
}

function back() {
    if (state.returnTo) {
        const target = state.returnTo;
        state.returnTo = null;
        show(target);
        return;
    }
    const i = STEPS.indexOf(state.step);
    if (i > 0) show(STEPS[i - 1]);
    else if (!IS_MODAL) window.location.href = '/';    /* eerste stap: terug naar de welkomstpagina; in het venster is er geen terug */
}

function jumpTo(id) {
    state.returnTo = 'overview';
    show(id);
}

function updateChrome() {
    const wrap = $('#progressWrap');
    const isQuestion = QUESTION_STEPS.includes(state.step) || state.step === 'overview';

    wrap.classList.toggle('hidden', !isQuestion);

    if (state.step === 'overview') {
        if ($('#progressBar')) $('#progressBar').style.width = '100%';
        $('#stepCounter').textContent = 'Laatste check';
    } else if (isQuestion) {
        const i = QUESTION_STEPS.indexOf(state.step);
        if ($('#progressBar')) $('#progressBar').style.width = `${Math.round(((i + 1) / (QUESTION_STEPS.length + 1)) * 100)}%`;
        $('#stepCounter').textContent = `Stap ${i + 1} van ${QUESTION_STEPS.length}`;
    }
    renderStapBalk();
}

/* Stappenbalk (venster in het dashboard): een segment per stap met de naam eronder.
   Gedane stappen zijn gevuld, de huidige licht op, de rest is nog leeg. */
const STAP_NAMEN = {
    name: 'Naam', person: 'Contactpersoon', contact: 'Contact', wachtwoord: 'Wachtwoord', company: 'Bedrijf',
    hours: 'Openingstijden', menu: 'Menukaart', punten: 'Spaarpunten', domain: 'Bestel-adres', style: 'Huisstijl',
};

function renderStapBalk() {
    const balk = $('#stapBalk');
    if (!balk) return;
    const huidig = state.step === 'overview' ? QUESTION_STEPS.length : QUESTION_STEPS.indexOf(state.step);
    balk.style.gridTemplateColumns = `repeat(${QUESTION_STEPS.length}, minmax(0, 1fr))`;
    balk.innerHTML = QUESTION_STEPS.map((s, i) => {
        const stand = i < huidig ? 'klaar' : (i === huidig ? 'actief' : '');
        return `<div class="stap-segment ${stand}">
            <span class="s-lijn"></span>
            <span class="s-tekst">${i < huidig ? '<i class="fa-solid fa-check" aria-hidden="true"></i> ' : ''}${STAP_NAMEN[s] || s}</span>
        </div>`;
    }).join('');
}

/* ── Validatie ────────────────────────────────────────────────── */

function setError(key, msg) {
    const el = $(`[data-err="${key}"]`);
    if (el) el.textContent = msg || '';
}

function nudge(input) {
    input?.classList.add('shake');
    setTimeout(() => input?.classList.remove('shake'), 450);
    input?.focus();
}

function validate(step) {
    switch (step) {
        case 'name':
            if (state.name.trim().length < 2) { setError('name', 'Vul eerst de naam van je zaak in.'); nudge($('#inpName')); return false; }
            setError('name'); return true;
        case 'person':
            if (state.person.trim().length < 2) { setError('person', 'Vul nog even je naam in.'); nudge($('#inpPerson')); return false; }
            setError('person'); return true;
        case 'contact':
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(state.email.trim())) {
                setError('email', 'Dit e-mailadres lijkt niet te kloppen, check het even.'); nudge($('#inpEmail')); return false;
            }
            setError('email'); return true;
        case 'wachtwoord':
            if (!pwVoldoet()) { setError('password', 'Je wachtwoord voldoet nog niet aan de eisen hierboven.'); nudge($('#inpPassword')); return false; }
            if (pw !== pw2) { setError('password', 'De wachtwoorden zijn niet hetzelfde.'); nudge($('#inpPassword2')); return false; }
            setError('password'); return true;
        case 'company':
            if ((state.kvk || '').trim() !== '' && !/^\d{8}$/.test(state.kvk.trim())) { setError('company', 'Een KvK-nummer bestaat uit 8 cijfers.'); nudge($('#inpKvk')); return false; }
            if ((state.street || '').trim().length < 3) { setError('company', 'Vul je straat en huisnummer in.'); nudge($('#inpStreet')); return false; }
            if (!/^\d{4}\s?[a-zA-Z]{2}$/.test((state.zip || '').trim())) { setError('company', 'Die postcode klopt nog niet, bijv. 1234 AB.'); nudge($('#inpZip')); return false; }
            if ((state.city || '').trim().length < 2) { setError('company', 'Vul je plaats nog even in.'); nudge($('#inpCity')); return false; }
            setError('company'); return true;
        case 'hours':
            if (!Object.values(state.days).some(Boolean)) { setError('hours', 'Kies minstens één dag dat je open bent.'); return false; }
            setError('hours'); return true;
        case 'menu':
            if (!state.menu.some((m) => (m.name || '').trim() !== '')) { toast('Voeg minstens één gerecht toe aan je menukaart.'); return false; }
            return true;
        case 'domain':
            if (state.domainMode === 'own' && !/.+\..{2,}/.test(state.ownDomain.trim())) {
                setError('domain', 'Vul je website in, bijv. pizzeriamario.nl'); nudge($('#inpOwnDomain')); return false;
            }
            setError('domain'); return true;
        default:
            return true;
    }
}

/* ── Stap-specifiek gedrag bij binnenkomst ────────────────────── */

function onEnterStep(id) {
    if (id === 'domain') {
        $$('[data-slug]').forEach((el) => (el.textContent = slugify(state.name) || 'jouwzaak'));
        runDomainCheck();
    }
    if (id === 'style') { pvCart = {}; pvActiveCat = null; pvMode = 'bezorgen'; renderThemes(); renderColors(); renderLogoUI(); renderPreview(); obPagina = 'menu'; updateObPreview(); }
    if (id === 'overview') renderSummary();
}

/* ── Hulpfuncties ─────────────────────────────────────────────── */

function slugify(str) {
    return str.normalize('NFD').replace(/[̀-ͯ]/g, '')
        .toLowerCase().replace(/[^a-z0-9]+/g, '').slice(0, 30);
}

function cleanDomain(str) {
    return str.trim().toLowerCase().replace(/^https?:\/\//, '').replace(/^www\./, '').replace(/\/.*$/, '');
}

function formatPrice(raw) {
    const num = parseFloat(String(raw).replace(',', '.'));
    if (isNaN(num)) return null;
    return num.toFixed(2).replace('.', ',');
}

function iconHtml(item, cls) {
    if (item.icon?.t === 'p') return `<img src="${item.icon.v}" class="${cls} rounded-md object-cover inline-block align-middle" alt="">`;
    return '<i class="fa-solid fa-camera inline-block align-middle opacity-40" aria-hidden="true"></i>';
}

function toast(msg, ms = 2600) {
    const el = $('#toast');
    if (!el) return;
    el.textContent = msg;
    el.classList.add('aan');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('aan'), ms);
}

/* ── Openingstijden ───────────────────────────────────────────── */

function renderDays() {
    $('#dayChips').innerHTML = DAYS.map((d) =>
        `<button type="button" class="day-chip ${state.days[d.key] ? 'on' : ''}" data-day="${d.key}">${d.label}</button>`
    ).join('');
}

function ensureDayTimes() {
    DAYS.forEach((d) => {
        if (!state.dayTimes[d.key]) state.dayTimes[d.key] = { open: state.open, close: state.close };
    });
}

function renderPerDayRows() {
    ensureDayTimes();
    const on = DAYS.filter((d) => state.days[d.key]);
    $('#perDayTimes').innerHTML = on.length
        ? on.map((d) =>
            `<div class="day-time-row">
                <span class="d-label">${d.label}</span>
                <span class="sep">van</span>
                <input type="time" class="inp-time" value="${state.dayTimes[d.key].open}" data-dt="open" data-dtday="${d.key}" aria-label="Openingstijd ${d.label}">
                <span class="sep">tot</span>
                <input type="time" class="inp-time" value="${state.dayTimes[d.key].close}" data-dt="close" data-dtday="${d.key}" aria-label="Sluitingstijd ${d.label}">
            </div>`
        ).join('')
        : `<div class="empty-box">Tik hierboven eerst een dag aan</div>`;
}

function renderHoursUI() {
    renderDays();
    $$('[data-hoursmode]').forEach((b) => b.classList.toggle('on', b.dataset.hoursmode === state.hoursMode));
    $('#sameTimes').classList.toggle('hidden', state.hoursMode !== 'same');
    $('#perDayTimes').classList.toggle('hidden', state.hoursMode !== 'perday');
    if (state.hoursMode === 'perday') renderPerDayRows();
}

function formatDaysSummary() {
    const on = DAYS.filter((d) => state.days[d.key]);
    if (!on.length) return 'Nog niet ingesteld';
    const label = on.length === 7 ? 'elke dag' : on.map((d) => d.label).join(', ');
    if (state.hoursMode === 'perday') {
        const times = on.map((d) => state.dayTimes[d.key]).filter(Boolean);
        const allSame = times.length && times.every((t) => t.open === times[0].open && t.close === times[0].close);
        if (!allSame) return `${label}, tijden per dag ingesteld`;
        if (times.length) return `${label}, ${times[0].open} - ${times[0].close}`;
    }
    return `${label}, ${state.open} - ${state.close}`;
}

/* ── Menu: categorieën ────────────────────────────────────────── */

function activeCategory() {
    return state.categories.find((c) => c.id === state.activeCat) || null;
}

function suggestionFor(catId) {
    return CATEGORY_SUGGESTIONS.find((s) => s.key === catId) || null;
}

function renderCatBar() {
    const remaining = CATEGORY_SUGGESTIONS.filter((s) => !state.categories.some((c) => c.id === s.key));
    const own = state.categories.map((c) =>
        `<button type="button" class="cat-chip ${c.id === state.activeCat ? 'active' : ''}" data-cat="${c.id}">
            <span>${c.emoji}</span><span>${esc(c.name)}</span>
            <span class="cat-del" data-delcat="${c.id}" title="Categorie verwijderen"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span>
        </button>`
    ).join('');
    const suggest = remaining.map((s) =>
        `<button type="button" class="cat-chip suggest" data-addcat="${s.key}">+ ${s.emoji} ${s.name}</button>`
    ).join('');
    const custom = addingCat
        ? `<input id="newCatInp" type="text" class="cat-inp" placeholder="Bijv. Broodjes" maxlength="24">`
        : `<button type="button" id="addOwnCat" class="cat-chip suggest">+ Eigen categorie</button>`;
    $('#catBar').innerHTML = own + suggest + custom;
    if (addingCat) $('#newCatInp')?.focus();
}

function addCategory(id, emoji, name, { activate = true } = {}) {
    if (state.categories.some((c) => c.id === id)) return;
    state.categories.push({ id, emoji, name });
    if (activate) state.activeCat = id;
    renderMenuUI();
    save();
}

function removeCategory(id) {
    const cat = state.categories.find((c) => c.id === id);
    if (!cat) return;
    state.categories = state.categories.filter((c) => c.id !== id);
    state.menu = state.menu.filter((m) => m.cat !== id);
    if (state.activeCat === id) state.activeCat = state.categories[0]?.id || null;
    renderMenuUI();
    save();
    toast(`Categorie "${cat.name}" verwijderd`);
}

function confirmOwnCat() {
    if (!addingCat) return;
    const inp = $('#newCatInp');
    const name = inp?.value.trim();
    addingCat = false;
    if (name && name.length >= 2) {
        const id = 'cat-' + slugify(name) + '-' + state.categories.length;
        addCategory(id, '🍽️', name);
    } else {
        renderCatBar();
    }
}

/* ── Menu: gerechten ──────────────────────────────────────────── */

function renderPresets() {
    const sug = suggestionFor(state.activeCat);
    if (!sug) { $('#presetGrid').innerHTML = ''; return; }
    $('#presetGrid').innerHTML = sug.presets.map((p, i) => {
        const added = state.menu.some((m) => m.cat === state.activeCat && m.name === p.name);
        return `<button type="button" class="preset-card ${added ? 'added' : ''}" data-preset="${i}">
            <span class="p-name">${p.name}</span>
            <span class="p-price">€ ${p.price}</span>
        </button>`;
    }).join('');
}

function renderMenuList(popIdx = null) {
    const list = $('#menuList');
    if (!state.categories.length) {
        list.innerHTML = `<div class="empty-box">Voeg eerst een categorie toe</div>`;
        return;
    }
    const cat = activeCategory();
    const items = state.menu.map((m, gi) => ({ ...m, gi })).filter((m) => m.cat === state.activeCat);
    if (!items.length) {
        list.innerHTML = `<div class="empty-box">Nog niks in ${esc(cat.name)}. Tik hierboven iets aan of voeg zelf toe.</div>`;
        return;
    }
    list.innerHTML = items.map((m) =>
        `<div class="menu-row${m.gi === popIdx ? ' pop' : ''}">
            <button type="button" class="m-icon" data-icon-idx="${m.gi}" title="Foto kiezen">${iconHtml(m, 'w-full h-full')}</button>
            <span class="m-name">${esc(m.name)}</span>
            <span class="font-semibold text-cacao/40 text-sm">€</span>
            <input type="text" inputmode="decimal" value="${m.price}" data-price-idx="${m.gi}" aria-label="Prijs van ${esc(m.name)}">
            <button type="button" class="m-del" data-del-idx="${m.gi}" aria-label="Verwijder ${esc(m.name)}"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>`
    ).join('');
}

function renderMenuUI(popIdx = null) {
    renderCatBar();
    renderPresets();
    renderMenuList(popIdx);
}

function ensureCategory() {
    if (!state.categories.length) {
        state.categories.push({ id: 'menu', emoji: '🍽️', name: 'Menu' });
        state.activeCat = 'menu';
    }
    if (!state.activeCat) state.activeCat = state.categories[0].id;
}

function addMenuItem(name, price) {
    ensureCategory();
    state.menu.push({ cat: state.activeCat, icon: null, name, price });
    renderMenuUI(state.menu.length - 1);    /* alleen de nieuwe rij krijgt de pop-animatie */
    save();
}

function addCustomItem() {
    const nameInp = $('#customName');
    const priceInp = $('#customPrice');
    const name = nameInp.value.trim();
    const price = formatPrice(priceInp.value) || '12,50';
    if (name.length < 2) { nudge(nameInp); return; }
    addMenuItem(name, price);
    nameInp.value = '';
    priceInp.value = '';
    nameInp.focus();
}

/* ── Icoon/foto-kiezer ────────────────────────────────────────── */

function openIconPicker(idx) {
    /* Geen emoji-kiezer meer: direct de bestandskiezer voor een eigen foto */
    pickerIdx = idx;
    $('#photoInp').click();
}

function closeIconPicker() {
    pickerIdx = null;
    const inp = $('#photoInp');
    if (inp) inp.value = '';
}

function setItemIcon(icon) {
    if (pickerIdx === null || !state.menu[pickerIdx]) return;
    state.menu[pickerIdx].icon = icon;
    renderMenuList();
    save();
    closeIconPicker();
}

/* Afbeelding verkleinen tot een vierkante thumbnail zodat localStorage klein blijft.
   PNG voor logo's (behoudt transparantie), JPEG voor foto's (kleiner). */
function photoToThumb(file, format = 'image/jpeg', size = 96) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext('2d');
            if (format === 'image/jpeg') { ctx.fillStyle = '#FFFFFF'; ctx.fillRect(0, 0, size, size); }
            const side = Math.min(img.width, img.height);
            ctx.drawImage(img, (img.width - side) / 2, (img.height - side) / 2, side, side, 0, 0, size, size);
            URL.revokeObjectURL(url);
            resolve(canvas.toDataURL(format, 0.82));
        };
        img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('kan afbeelding niet lezen')); };
        img.src = url;
    });
}

/* ── Domein-check (mock) ──────────────────────────────────────── */

let domainCheckTimer;
function runDomainCheck() {
    const el = $('#domainCheck');
    clearTimeout(domainCheckTimer);    /* ook annuleren bij wissel naar eigen domein */
    if (state.domainMode !== 'sub') { el.textContent = ''; return; }
    el.textContent = 'Even checken of dit adres vrij is…';
    el.className = 'mt-4 h-6 text-sm font-semibold text-cacao/45';
    domainCheckTimer = setTimeout(() => {
        el.innerHTML = '<i class="fa-solid fa-check" aria-hidden="true"></i> Beschikbaar, die is voor jou.';
        el.className = 'mt-4 h-6 text-sm font-semibold text-basil';
    }, 900);
}

/* ── Huisstijl & preview ──────────────────────────────────────── */

function currentTheme() {
    return THEMES.find((t) => t.id === state.theme) || THEMES[0];
}

/* De 16:9 mini-weergaven staan in assets/stijl-tiles.js (gedeeld met het dashboard) */
const TILES = window.PP_TILES;

function renderThemes() {
    $('#themeGrid').innerHTML = THEMES.map((t) =>
        `<button type="button" class="theme-card ${state.theme === t.id ? 'selected' : ''}" data-theme="${t.id}">
            <span class="t-tile" style="background:${t.itemBg}; height:auto; aspect-ratio:16/9; padding:0;">${TILES[t.id](state.color)}</span>
            <span class="t-name">${t.name}</span>
            <span class="t-desc">${t.desc}</span>
        </button>`
    ).join('');
}

function renderLogoUI() {
    const has = !!state.logo;
    $('#logoDropThumb').classList.toggle('hidden', !has);
    if (has) $('#logoDropThumb img').src = state.logo;
    $('#logoDropText').textContent = has ? 'Ander logo kiezen' : 'Logo uploaden';
    $('#logoDropHint').classList.toggle('hidden', has);
    $('#logoRemove').style.display = has ? 'grid' : 'none';
}

function renderColors() {
    // Alleen de vaste kleuren: het bijbehorende palet leiden we zelf af
    if (!KLEUREN.some((k) => k.hex === state.color)) state.color = KLEUREN[0].hex;
    const lijst = KLEUREN.filter((k) => kleurTab === 'alle' || k.stijl === kleurTab);
    const gekozen = KLEUREN.find((k) => k.hex === state.color);
    $('#colorGrid').innerHTML = `
        <div class="flex flex-wrap gap-2 mb-4">${KLEUR_TABS.map(([id, label]) =>
            `<button type="button" data-kleurtab="${id}" class="keuze-chip !py-1.5 !px-3.5 !text-xs ${kleurTab === id ? 'aan' : ''}">${label}</button>`).join('')}
        </div>
        <div class="grid grid-cols-5 sm:grid-cols-10 gap-3 w-full sm:w-fit">${lijst.map((k) =>
            `<button type="button" class="swatch ${state.color === k.hex ? 'selected' : ''}" data-color="${k.hex}"
                style="background:${k.palet.primair}" title="${esc(k.naam)}" aria-label="${esc(k.naam)}"></button>`).join('')}
        </div>
        <p class="mt-3 text-sm font-semibold text-cacao/50">${gekozen ? 'Gekozen: ' + esc(gekozen.naam) : ''}</p>`;
}

/* De preview is een werkende mini-bestelpagina: categorieën, plusjes, mandje */
const PV_DEMO = [
    { icon: { t: 'e', v: '🍕' }, name: 'Margherita', price: '9,50' },
    { icon: { t: 'e', v: '🍕' }, name: 'Salami', price: '11,00' },
    { icon: { t: 'e', v: '🍄' }, name: 'Funghi', price: '10,50' },
    { icon: { t: 'e', v: '🌶️' }, name: 'Diavola', price: '12,00' },
    { icon: { t: 'e', v: '🥟' }, name: 'Calzone', price: '12,50' },
    { icon: { t: 'e', v: '🧀' }, name: 'Quattro Formaggi', price: '12,50' },
    { icon: { t: 'e', v: '🍍' }, name: 'Hawaï', price: '11,50' },
    { icon: { t: 'e', v: '🐟' }, name: 'Tonno', price: '12,00' },
];

let pvCart = {};          // key → aantal (alleen voor de demo, wordt niet bewaard)
let pvActiveCat = null;
let pvMode = 'bezorgen';

function pvDataset() {
    const hasMenu = state.menu.length > 0;
    const cats = hasMenu
        ? state.categories.filter((c) => state.menu.some((m) => m.cat === c.id))
        : [{ id: 'demo', emoji: '🍕', name: 'Populair' }];
    const items = hasMenu
        ? state.menu.map((m, gi) => ({ ...m, key: 'm' + gi, catId: m.cat }))
        : PV_DEMO.map((m, i) => ({ ...m, key: 'd' + i, catId: 'demo' }));
    return { cats, items };
}

const priceCents = (p) => Math.round(parseFloat(String(p).replace(',', '.')) * 100) || 0;

function pvViewData() {
    const { cats, items } = pvDataset();
    if (!cats.some((c) => c.id === pvActiveCat)) pvActiveCat = cats[0].id;
    const fillers = PV_DEMO.map((m, i) => ({ ...m, key: 'f' + i }));
    const catItems = items.filter((m) => m.catId === pvActiveCat);
    const shown = [...catItems, ...fillers.filter((f) => !catItems.some((c) => c.name.toLowerCase() === f.name.toLowerCase()))].slice(0, 8);
    const count = Object.values(pvCart).reduce((a, b) => a + b, 0);
    const total = Object.entries(pvCart).reduce((sum, [key, qty]) => {
        const item = [...items, ...fillers].find((i) => i.key === key);
        return sum + (item ? priceCents(item.price) * qty : 0);
    }, 0);
    const nm = state.name.trim() || 'Jouw Pizzeria';
    return {
        nm: esc(nm),
        initial: esc((nm[0] || 'P').toUpperCase()),
        color: state.color,
        cats: cats.slice(0, 4).map((c) => ({ id: c.id, name: esc(c.name), active: c.id === pvActiveCat })),
        shown,
        count,
        totalStr: (total / 100).toFixed(2).replace('.', ','),
    };
}

const pvLogoInner = () => (state.logo ? `<img src="${state.logo}" class="w-full h-full object-cover" alt="">` : null);
const pvQty = (key) => pvCart[key] || '+';

/* Lay-outs voor de preview; Presto (template1) is de actieve */
const PV_TEMPLATES = {
    template1(d) {
        const pal = paletVoor(d.color);
        const logo = pvLogoInner() || `<span class="font-extrabold text-[11px]" style="color:${pal.primair}">${d.initial}</span>`;
        const rows = d.shown.map((m) => `
            <div class="flex items-center gap-2 rounded-[10px] bg-white px-2.5 py-2" style="border:1px solid rgba(0,0,0,.08)">
                <span class="flex-1 min-w-0 text-left">
                    <span class="block truncate text-[10px] font-bold" style="color:${pal.secundair}">${esc(m.name)}</span>
                    <span class="block text-[9px] font-semibold" style="color:rgba(0,0,0,.4)">&euro; ${m.price}</span>
                </span>
                <button type="button" data-pv-add="${m.key}" class="w-5 h-5 shrink-0 rounded-full grid place-items-center text-[10px] font-bold bg-white" style="border:1px solid rgba(0,0,0,.12); color:${pal.primair}">${pvQty(m.key)}</button>
            </div>`).join('');
        return `
        <div class="shrink-0 relative" style="background:${pal.witWarm}">
            <div class="h-14" style="background:linear-gradient(135deg, ${pal.secundairLicht}, ${pal.secundair}); border-radius:0 0 14px 14px"></div>
            <span class="absolute left-3 -bottom-3 w-8 h-8 rounded-[9px] overflow-hidden grid place-items-center bg-white shadow" style="border:2px solid rgba(255,255,255,.6)">${logo}</span>
        </div>
        <div class="shrink-0 px-3 pt-4 pb-2 text-left" style="background:${pal.witWarm}">
            <p class="text-[12px] font-extrabold leading-tight" style="color:${pal.secundair}">${d.nm}</p>
            <p class="text-[8px] font-bold" style="color:${pal.secundair}"><span style="color:${pal.primair}">★</span> 4,8 (1.200+)</p>
            <div class="mt-1.5 px-2.5 py-1.5 rounded-full bg-white text-[8px] font-semibold" style="border:1px solid rgba(0,0,0,.08); color:rgba(0,0,0,.35)">Zoeken ${d.nm}</div>
            <div class="mt-1.5 flex gap-1 overflow-hidden">
                ${d.cats.map((c) => `<button type="button" data-pv-cat="${c.id}" class="shrink-0 px-2 py-1 rounded-full text-[8px] font-bold" style="${c.active ? `background:${pal.secundair}; color:#fff` : 'color:rgba(0,0,0,.5)'}">${c.name}</button>`).join('')}
            </div>
        </div>
        <div class="flex-1 overflow-auto px-3 py-1.5 space-y-1.5" style="background:${pal.witWarm}">${rows}</div>
        <div class="shrink-0 p-2 space-y-1.5" style="background:${pal.secundair}">
            <div class="grid grid-cols-2 gap-1 rounded-full p-0.5" style="background:rgba(255,255,255,.12)">
                <button type="button" data-pv-mode="bezorgen" class="py-1 rounded-full text-[8px] font-bold" style="${pvMode === 'bezorgen' ? `background:${pal.primair}; color:#fff` : 'color:rgba(255,255,255,.6)'}">Bezorgen</button>
                <button type="button" data-pv-mode="afhalen" class="py-1 rounded-full text-[8px] font-bold" style="${pvMode === 'afhalen' ? `background:${pal.primair}; color:#fff` : 'color:rgba(255,255,255,.6)'}">Afhalen</button>
            </div>
            <div class="py-1.5 rounded-full text-center text-[9px] font-bold text-white" style="background:${pal.primair}">${d.count ? `Afrekenen (${d.count}) &euro; ${d.totalStr}` : 'Ga naar afrekenen'}</div>
        </div>`;
    },
    /* Speels: ronde banner, gecentreerd logo, pill-vormen */
    fresco(d) {
        const logo = pvLogoInner() || `<span class="font-display" style="color:${d.color}">${d.initial}</span>`;
        return `
        <div style="background:${d.color}" class="shrink-0 rounded-b-3xl pt-8 pb-3 text-center text-white">
            <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-white overflow-hidden text-[13px]">${logo}</span>
            <p class="font-display text-[13px] leading-tight truncate px-3 mt-1">${d.nm}</p>
            <p class="text-[8px] font-extrabold opacity-85">★ 4,8 (150+)  Bezorging € 0,99</p>
        </div>
        <div class="mx-3 mt-2 shrink-0 grid grid-cols-2 gap-1 rounded-full p-1" style="background:#FFF6E8">
            ${['bezorgen', 'afhalen'].map((mo) => `<span data-pv-mode="${mo}" class="text-center text-[8px] font-extrabold rounded-full py-1 cursor-pointer" style="${pvMode === mo ? `background:${d.color}; color:#fff` : 'color:#38221A'}">${mo === 'bezorgen' ? 'Bezorgen' : 'Afhalen'}</span>`).join('')}
        </div>
        <div class="mx-3 mt-1.5 shrink-0 flex gap-1.5 overflow-hidden">
            ${d.cats.map((c) => `<button type="button" data-pv-cat="${c.id}" class="text-[8px] font-extrabold rounded-full px-2 py-1 whitespace-nowrap cursor-pointer" style="${c.active ? `background:${d.color}; color:#fff` : 'background:#FFF6E8; color:#38221A'}">${c.name}</button>`).join('')}
        </div>
        <div class="p-3 pt-2 space-y-1.5 flex-1 overflow-hidden">
            ${d.shown.map((m) => `<div class="flex items-center justify-between gap-1.5 rounded-xl px-2.5 py-1.5 text-[9px] font-extrabold" style="background:#FFF6E8; color:#38221A">
                <span class="flex items-center gap-1.5 min-w-0">${m.icon?.t === 'p' ? iconHtml(m, 'w-4 h-4') : ''}<span class="truncate">${esc(m.name)}</span></span>
                <span class="flex items-center gap-1.5 shrink-0"><span class="opacity-60">€ ${m.price}</span><button type="button" data-pv-add="${m.key}" class="pv-add rounded-full" style="background:${d.color}">${pvQty(m.key)}</button></span>
            </div>`).join('')}
        </div>
        <div class="px-3 pb-3 mt-auto shrink-0">
            <div class="rounded-full text-white text-[10px] font-display py-2 px-3 text-center" style="background:${d.color}">${d.count ? `<span class="flex justify-between"><span>Bestellen (${d.count})</span><span>€ ${d.totalStr}</span></span>` : 'Bestellen'}</div>
        </div>`;
    },

    /* Fine dining: gecentreerd, gouden accenten, menuregels met puntjes */
    nero(d) {
        const logo = pvLogoInner() || `<span style="color:#C9A96A; font-family:Georgia,serif">${d.initial}</span>`;
        return `
        <div class="shrink-0 pt-8 pb-3 text-center" style="color:#F3EAD9">
            <span class="inline-grid place-items-center w-9 h-9 rounded-full overflow-hidden text-[12px]" style="border:1px solid #C9A96A">${logo}</span>
            <p class="text-[11px] uppercase mt-1.5 truncate px-3" style="font-family:Georgia,serif; letter-spacing:.25em">${d.nm}</p>
            <p class="text-[6px] uppercase mt-0.5" style="color:#C9A96A; letter-spacing:.4em">Ristorante</p>
            <div class="w-8 mx-auto mt-2" style="height:1px; background:#C9A96A"></div>
        </div>
        <div class="shrink-0 flex justify-center gap-3 px-3 pb-1 overflow-hidden">
            ${d.cats.map((c) => `<button type="button" data-pv-cat="${c.id}" class="text-[7px] uppercase whitespace-nowrap cursor-pointer pb-0.5" style="letter-spacing:.15em; ${c.active ? `color:${d.color}; border-bottom:1px solid ${d.color}` : 'color:#8D8177'}">${c.name}</button>`).join('')}
        </div>
        <div class="px-4 py-2 space-y-2 flex-1 overflow-hidden">
            ${d.shown.map((m) => `<div class="flex items-end gap-1 text-[9px]" style="color:#F3EAD9; font-family:Georgia,serif">
                <span class="truncate">${esc(m.name)}</span>
                <span class="flex-1 mb-0.5" style="border-bottom:1px dotted rgba(243,234,217,.3)"></span>
                <span style="color:#C9A96A">€ ${m.price}</span>
                <button type="button" data-pv-add="${m.key}" class="ml-1 w-3.5 h-3.5 grid place-items-center rounded-full text-[8px] cursor-pointer shrink-0" style="border:1px solid #C9A96A; color:#C9A96A">${pvQty(m.key)}</button>
            </div>`).join('')}
        </div>
        <div class="px-4 pb-4 mt-auto shrink-0">
            <div class="text-center uppercase text-[8px] py-2" style="border:1px solid ${d.color}; color:${d.color}; letter-spacing:.3em">${d.count ? `Bestellen (${d.count})  € ${d.totalStr}` : 'Bestellen'}</div>
        </div>`;
    },

    /* Gedrukte Italiaanse menukaart */
    napoli(d) {
        return `
        <div class="shrink-0 flex" style="height:5px"><span style="flex:1;background:#2F8F46"></span><span style="flex:1;background:#fff"></span><span style="flex:1;background:#E63946"></span></div>
        <div class="shrink-0 text-center pt-5 pb-2" style="color:#3A2A1A; font-family:'Times New Roman',serif">
            <p class="text-[8px] italic">Ristorante Pizzeria</p>
            <p class="text-[15px] font-bold leading-tight truncate px-3">${d.nm}</p>
            <div class="w-16 mx-auto mt-1.5" style="border-top:3px double #3A2A1A"></div>
        </div>
        <div class="shrink-0 flex justify-center gap-2.5 px-3 py-1 overflow-hidden" style="font-family:'Times New Roman',serif">
            ${d.cats.map((c) => `<button type="button" data-pv-cat="${c.id}" class="text-[8px] whitespace-nowrap cursor-pointer ${c.active ? 'font-bold underline' : ''}" style="color:${c.active ? d.color : '#8A6A3B'}">${c.name}</button>`).join('')}
        </div>
        <div class="px-4 py-1 flex-1 overflow-hidden" style="font-family:'Times New Roman',serif; color:#3A2A1A">
            ${d.shown.map((m) => `<div class="flex items-center justify-between gap-1.5 text-[9px]" style="padding:5px 0; border-bottom:1px dashed #E8DCC2">
                <span class="truncate">${esc(m.name)}</span>
                <span class="flex items-center gap-1.5 shrink-0"><span class="font-bold">€ ${m.price}</span><button type="button" data-pv-add="${m.key}" class="w-3.5 h-3.5 grid place-items-center text-[8px] cursor-pointer" style="border:1px solid #8A6A3B; color:#8A6A3B">${pvQty(m.key)}</button></span>
            </div>`).join('')}
        </div>
        <div class="shrink-0 mt-auto text-center py-2 mx-4 mb-2 text-[9px] italic" style="border-top:3px double #3A2A1A; font-family:'Times New Roman',serif; color:#3A2A1A">
            ${d.count ? `Bestelling: <b style="color:${d.color}">${d.count} stuks, € ${d.totalStr}</b>` : 'Buon appetito'}
        </div>`;
    },

    /* Strak en modern: kaarten-grid met foto's */
    puro(d) {
        const logo = pvLogoInner() || `<span class="font-extrabold text-[10px]" style="color:${d.color}">${d.initial}</span>`;
        return `
        <div class="shrink-0 flex items-center justify-between px-3 pt-7 pb-2" style="color:#233029">
            <p class="text-[11px] font-extrabold truncate">${d.nm}</p>
            <span class="grid place-items-center w-6 h-6 rounded-full overflow-hidden shrink-0" style="background:#F4F7F4">${logo}</span>
        </div>
        <div class="mx-3 mb-1.5 shrink-0 rounded-lg px-2.5 py-1.5 text-[8px] font-bold" style="background:#F4F7F4; color:#5B6B60">Zoeken</div>
        <div class="mx-3 shrink-0 flex gap-1.5 overflow-hidden">
            ${d.cats.map((c) => `<button type="button" data-pv-cat="${c.id}" class="text-[8px] font-extrabold rounded-md px-2 py-1 whitespace-nowrap cursor-pointer" style="${c.active ? `background:${d.color}; color:#fff` : 'background:#F4F7F4; color:#233029'}">${c.name}</button>`).join('')}
        </div>
        <div class="p-3 pt-2 grid grid-cols-2 gap-1.5 content-start flex-1 overflow-hidden">
            ${d.shown.slice(0, 6).map((m) => `<div class="rounded-lg p-1.5" style="background:#F4F7F4">
                ${m.icon.t === 'p' ? `<div class="h-9 rounded-md overflow-hidden mb-1"><img src="${m.icon.v}" class="w-full h-full object-cover" alt=""></div>` : `<div class="h-9 rounded-md mb-1 grid place-items-center text-[13px] font-extrabold" style="background:#fff; color:${d.color}">${esc(m.name[0])}</div>`}
                <p class="text-[8px] font-extrabold truncate" style="color:#233029">${esc(m.name)}</p>
                <div class="flex items-center justify-between mt-0.5"><span class="text-[8px] font-bold" style="color:#5B6B60">€ ${m.price}</span><button type="button" data-pv-add="${m.key}" class="w-4 h-4 grid place-items-center rounded-full text-white text-[8px] font-extrabold cursor-pointer" style="background:${d.color}">${pvQty(m.key)}</button></div>
            </div>`).join('')}
        </div>
        <div class="shrink-0 mt-auto pb-3 flex justify-center">
            <div class="rounded-full text-white text-[9px] font-extrabold px-5 py-1.5" style="background:${d.color}">${d.count ? `Bestellen (${d.count})  € ${d.totalStr}` : 'Bekijk bestelling'}</div>
        </div>`;
    },

    /* Street: blokletters, marquee, harde randen */
    blocco(d) {
        return `
        <div class="shrink-0 px-3 pt-6 pb-1.5" style="color:#111; font-family:'Arial Black',Impact,sans-serif">
            <p class="text-[15px] leading-[1.05] uppercase truncate">${d.nm}</p>
            <span class="block h-1.5 w-14 mt-1" style="background:${d.color}"></span>
        </div>
        <div class="shrink-0 px-3 py-1 text-[7px] uppercase whitespace-nowrap overflow-hidden" style="background:#111; color:#fff; font-family:'Arial Black',Impact,sans-serif; letter-spacing:.1em">Gratis bezorging vanaf € 20 ★ Vers uit de oven ★ Gratis bezorging</div>
        <div class="mx-3 mt-1.5 shrink-0 flex gap-1.5 overflow-hidden">
            ${d.cats.map((c) => `<button type="button" data-pv-cat="${c.id}" class="text-[8px] uppercase px-1.5 py-0.5 whitespace-nowrap cursor-pointer" style="border:2px solid #111; font-family:'Arial Black',Impact,sans-serif; ${c.active ? `background:${d.color}; color:#fff` : 'background:#fff; color:#111'}">${c.name}</button>`).join('')}
        </div>
        <div class="p-3 pt-2 space-y-2 flex-1 overflow-hidden">
            ${d.shown.map((m) => `<div class="flex items-center justify-between gap-1.5 px-2 py-1.5 text-[9px] uppercase" style="border:2px solid #111; box-shadow:2px 2px 0 0 #111; background:#fff; color:#111; font-family:'Arial Black',Impact,sans-serif">
                <span class="truncate">${esc(m.name)}</span>
                <span class="flex items-center gap-1.5 shrink-0"><span>€ ${m.price}</span><button type="button" data-pv-add="${m.key}" class="w-4 h-4 grid place-items-center text-white text-[9px] cursor-pointer" style="background:${d.color}; border:2px solid #111">${pvQty(m.key)}</button></span>
            </div>`).join('')}
        </div>
        <div class="shrink-0 mt-auto flex justify-between items-center px-3 py-2 text-[9px] uppercase" style="background:#111; color:#fff; font-family:'Arial Black',Impact,sans-serif">
            <span>${d.count ? `Bestellen (${d.count})` : 'Bestellen'}</span><span style="color:${d.color}">€ ${d.count ? d.totalStr : '0,00'}</span>
        </div>`;
    },

    /* Jaren 70: boogheader, ovale rijen, prijsbadges */
    retro(d) {
        const logo = pvLogoInner() || `<span class="text-[12px] font-extrabold" style="color:${d.color}; font-family:'Cooper Black',Georgia,serif">${d.initial}</span>`;
        return `
        <div class="shrink-0 text-center pt-7 pb-5" style="background:${d.color}; border-radius:0 0 50% 50% / 0 0 28px 28px">
            <span class="inline-grid place-items-center w-9 h-9 rounded-full bg-white overflow-hidden">${logo}</span>
        </div>
        <div class="shrink-0 text-center" style="color:#5B3A21">
            <p class="text-[13px] leading-tight truncate px-3 mt-1" style="font-family:'Cooper Black',Georgia,serif">${d.nm}</p>
            <p class="text-[6px] uppercase font-extrabold opacity-60" style="letter-spacing:.3em">Sinds 1974</p>
        </div>
        <div class="mx-3 mt-1.5 shrink-0 flex justify-center gap-1.5 overflow-hidden">
            ${d.cats.map((c) => `<button type="button" data-pv-cat="${c.id}" class="text-[8px] font-extrabold rounded-full px-2 py-0.5 whitespace-nowrap cursor-pointer" style="${c.active ? `background:${d.color}; color:#fff` : 'background:#F5E0B4; color:#5B3A21'}">${c.name}</button>`).join('')}
        </div>
        <div class="p-3 pt-2 space-y-1.5 flex-1 overflow-hidden">
            ${d.shown.map((m, i) => `<div class="flex items-center justify-between gap-1.5 rounded-full pl-3 pr-1 py-1 text-[9px] font-extrabold" style="background:${i % 2 ? '#F5E0B4' : '#FDF4DD'}; color:#5B3A21">
                <span class="truncate">${esc(m.name)}</span>
                <span class="flex items-center gap-1 shrink-0"><span class="rounded-full px-1.5 py-0.5 text-[8px]" style="background:#5B3A21; color:#FBEED3">€ ${m.price}</span><button type="button" data-pv-add="${m.key}" class="w-4 h-4 grid place-items-center rounded-full text-white text-[9px] font-extrabold cursor-pointer" style="background:${d.color}">${pvQty(m.key)}</button></span>
            </div>`).join('')}
        </div>
        <div class="px-3 pb-3 mt-auto shrink-0">
            <div class="rounded-full text-white text-[10px] font-extrabold py-2 text-center" style="background:${d.color}; box-shadow:0 3px 0 0 #8A5A2B; font-family:'Cooper Black',Georgia,serif">${d.count ? `Bestellen (${d.count})  € ${d.totalStr}` : 'Bestellen'}</div>
        </div>`;
    },
};

function renderPreview() {
    /* De telefoon-preview is vervallen; de templatekaart toont de pagina al in het echt */
    if (!$('#pvFrame')) return;
    const t = currentTheme();
    $('#pvFrame').style.background = t.id === 'template1' ? paletVoor(state.color).witWarm : t.cardBg;
    $('#pvScreen').innerHTML = (PV_TEMPLATES[t.id] || PV_TEMPLATES.template1)(pvViewData());
}

/* Live voorbeeld in de stijl-stap: de echte demopagina in gekozen template en kleur.
   Kijken en scrollen mag; klikken in het voorbeeld wordt geblokkeerd. */
let obPagina = 'menu';

function updateObPreview() {
    const frame = $('#obStijlPreview');
    if (!frame) return;
    document.querySelectorAll('[data-ob-pagina]').forEach((el) => el.classList.toggle('aan', el.dataset.obPagina === obPagina));
    const laad = $('#obStijlLaad');
    const klaar = () => { if (laad) laad.style.display = 'none'; };
    if (laad) laad.style.display = '';
    clearTimeout(updateObPreview.timer);
    updateObPreview.timer = setTimeout(klaar, 12000);   /* vangnet als laden blijft hangen */
    const q = `kleur=${encodeURIComponent(state.color)}`;
    frame.src = obPagina === 'menu'
        ? `${location.origin}/${state.theme}?${q}`
        : `${location.origin}/${state.theme}/${obPagina === 'status' ? 'bestelling' : 'afrekenen'}?voorbeeld=1&${q}`;
    frame.onload = () => {
        try {
            frame.contentDocument.addEventListener('click', (ev) => { ev.preventDefault(); ev.stopPropagation(); }, true);
        } catch { /* geen toegang: dan blijft het voorbeeld gewoon staan */ }
        clearTimeout(updateObPreview.timer);
        klaar();
    };
}

/* ── Overzicht ────────────────────────────────────────────────── */

function renderSummary() {
    const address = [state.street, [state.zip, state.city].filter(Boolean).join(' ')].filter(Boolean).join(', ');
    const orderUrl = state.domainMode === 'own' && state.ownDomain
        ? `bestellen.${cleanDomain(state.ownDomain)}`
        : `${slugify(state.name) || 'jouwzaak'}.${window.PP_DOMEIN}`;
    const colorName = KLEUREN.find((k) => k.hex === state.color)?.naam || 'Eigen kleur';
    const theme = currentTheme();
    const usedCats = state.categories.filter((c) => state.menu.some((m) => m.cat === c.id));
    const menuValue = state.menu.length
        ? `${state.menu.length} gerecht${state.menu.length === 1 ? '' : 'en'} in ${usedCats.length} categorie${usedCats.length === 1 ? '' : 'ën'}: ${usedCats.map((c) => esc(c.name)).join(', ')}`
        : 'Maken we later samen af';

    const rows = [
        { label: 'Zaak',          value: esc(state.name), step: 'name' },
        { label: 'Contact',       value: `${esc(state.person)}${state.phone ? ', ' + esc(state.phone) : ''}`, step: 'person' },
        { label: 'E-mail',        value: esc(state.email), step: 'contact' },
        ...(IS_AUTH ? [] : [{ label: 'Wachtwoord', value: pw ? '••••••••' : 'Nog niet gekozen', step: 'wachtwoord' }]),
        { label: 'KvK & adres',   value: esc([state.kvk, address].filter(Boolean).join(', ')) || 'Doen we later samen', step: 'company' },
        { label: 'Open',          value: formatDaysSummary(), step: 'hours' },
        { label: 'Menu',          value: menuValue, step: 'menu' },
        { label: 'Spaarpunten',   value: state.spaarpunten ? 'Aan' : 'Uit', step: 'punten' },
        { label: 'Bestel-adres',  value: esc(orderUrl), step: 'domain' },
        { label: 'Template',      value: `${esc(theme.name)}, <span class="inline-block w-4 h-4 rounded-full align-middle mx-1" style="background:${paletVoor(state.color).primair}"></span>${colorName}${state.logo ? ', met logo' : ''}`, step: 'style' },
    ];

    $('#summary').innerHTML = rows.filter((r) => STEPS.includes(r.step)).map((r) =>
        `<button type="button" class="sum-row" data-jump="${r.step}">
            <span class="sum-label">${r.label}</span>
            <span class="sum-value">${r.value}</span>
            <span class="sum-edit">aanpassen <i class="fa-solid fa-pen" aria-hidden="true"></i></span>
        </button>`
    ).join('');
}

/* ── Confetti ─────────────────────────────────────────────────── */

function launchConfetti() {
    const canvas = $('#confetti');
    if (!canvas) return;   /* geen confetti in het dashboard-venster */
    const ctx = canvas.getContext('2d');
    canvas.width = innerWidth;
    canvas.height = innerHeight;
    canvas.classList.remove('hidden');

    const colors = ['#B04A3F', '#2C7A4B', '#B97F10', '#7C3AED', '#2563EB', '#FAF8F4'];
    const pieces = Array.from({ length: 160 }, () => ({
        x: Math.random() * canvas.width,
        y: -20 - Math.random() * canvas.height * 0.5,
        w: 6 + Math.random() * 8,
        h: 8 + Math.random() * 10,
        vy: 2.5 + Math.random() * 3.5,
        vx: -1.5 + Math.random() * 3,
        rot: Math.random() * Math.PI,
        vr: -0.12 + Math.random() * 0.24,
        color: colors[Math.floor(Math.random() * colors.length)],
    }));

    const start = performance.now();
    (function tick(now) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (const p of pieces) {
            p.y += p.vy; p.x += p.vx; p.rot += p.vr;
            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate(p.rot);
            ctx.fillStyle = p.color;
            ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
            ctx.restore();
        }
        if (now - start < 4000) requestAnimationFrame(tick);
        else canvas.classList.add('hidden');
    })(start);
}

/* ── Versturen naar de server ─────────────────────────────────── */

function verstuurOnboarding() {
    const payload = { ...state };
    delete payload.returnTo;
    delete payload.submitted;
    delete payload.step;
    return fetch('/onboarding/afronden', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ state: payload, password: IS_AUTH ? undefined : pw }),
    });
}

/* ── Events ───────────────────────────────────────────────────── */

function bindInput(id, key, extra) {
    const el = $(id);
    if (!el) return;   /* in het venster bestaan de registratievelden niet */
    el.value = state[key];
    el.addEventListener('input', () => {
        state[key] = el.value;
        save();
        extra?.();
    });
}

function init() {
    const hadProgress = restore();
    /* Vanaf de site: ?naam=… vult de naam van de zaak alvast in, alleen bij een verse start */
    const naamParam = new URLSearchParams(location.search).get('naam');
    if (naamParam && !hadProgress && !window.PP_MODAL) state.name = naamParam.trim().slice(0, 50);

    bindInput('#inpName', 'name');
    bindInput('#inpPerson', 'person');
    bindInput('#inpEmail', 'email');
    bindInput('#inpPhone', 'phone');
    bindInput('#inpKvk', 'kvk');
    bindInput('#inpStreet', 'street');
    bindInput('#inpZip', 'zip');
    bindInput('#inpCity', 'city');

    $('#inpOpen').value = state.open;
    $('#inpClose').value = state.close;
    $('#inpOpen').addEventListener('change', (e) => { state.open = e.target.value; save(); });
    $('#inpClose').addEventListener('change', (e) => { state.close = e.target.value; save(); });

    renderHoursUI();
    if ($('#menuList')) renderMenuUI();   /* de menustap zit niet in het venster */
    renderThemes();
    renderColors();

    /* Dag-chips */
    $('#dayChips').addEventListener('click', (e) => {
        const chip = e.target.closest('[data-day]');
        if (!chip) return;
        state.days[chip.dataset.day] = !state.days[chip.dataset.day];
        chip.classList.toggle('on');
        if (state.hoursMode === 'perday') renderPerDayRows();
        setError('hours');
        save();
    });

    /* Zelfde tijden of per dag apart */
    $$('[data-hoursmode]').forEach((btn) => btn.addEventListener('click', () => {
        state.hoursMode = btn.dataset.hoursmode;
        renderHoursUI();
        save();
    }));
    $('#perDayTimes').addEventListener('change', (e) => {
        const inp = e.target.closest('[data-dt]');
        if (!inp) return;
        state.dayTimes[inp.dataset.dtday][inp.dataset.dt] = inp.value;
        save();
    });

    /* Categorieën */
    $('#catBar')?.addEventListener('click', (e) => {
        const del = e.target.closest('[data-delcat]');
        if (del) { removeCategory(del.dataset.delcat); return; }
        const add = e.target.closest('[data-addcat]');
        if (add) {
            const sug = suggestionFor(add.dataset.addcat);
            addCategory(sug.key, sug.emoji, sug.name);
            return;
        }
        if (e.target.closest('#addOwnCat')) {
            addingCat = true;
            renderCatBar();
            return;
        }
        const chip = e.target.closest('[data-cat]');
        if (chip) {
            state.activeCat = chip.dataset.cat;
            renderMenuUI();
            save();
        }
    });
    $('#catBar')?.addEventListener('focusout', (e) => {
        if (e.target.id === 'newCatInp') confirmOwnCat();
    });

    /* Menu-presets & lijst */
    $('#presetGrid')?.addEventListener('click', (e) => {
        const card = e.target.closest('[data-preset]');
        if (!card) return;
        const p = suggestionFor(state.activeCat)?.presets[card.dataset.preset];
        if (!p) return;
        const idx = state.menu.findIndex((m) => m.cat === state.activeCat && m.name === p.name);
        if (idx >= 0) { state.menu.splice(idx, 1); renderMenuUI(); save(); }
        else addMenuItem(p.name, p.price);
    });
    $('#menuList')?.addEventListener('click', (e) => {
        const icon = e.target.closest('[data-icon-idx]');
        if (icon) { openIconPicker(Number(icon.dataset.iconIdx)); return; }
        const del = e.target.closest('[data-del-idx]');
        if (!del) return;
        state.menu.splice(del.dataset.delIdx, 1);
        renderMenuUI();
        save();
    });
    $('#menuList')?.addEventListener('change', (e) => {
        const inp = e.target.closest('[data-price-idx]');
        if (!inp) return;
        const formatted = formatPrice(inp.value);
        if (formatted) state.menu[inp.dataset.priceIdx].price = formatted;
        inp.value = state.menu[inp.dataset.priceIdx].price;
        save();
    });
    $('#customAdd')?.addEventListener('click', addCustomItem);

    /* Foto-kiezer */
    $('#photoInp')?.addEventListener('change', async (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        try {
            setItemIcon({ t: 'p', v: await photoToThumb(file) });
            toast('Foto toegevoegd');
        } catch {
            toast('Die afbeelding lukt niet, probeer een andere.');
        }
    });

    /* Spaarpunten: keuzekaart selecteren, verder gaat via Volgende */
    $$('[data-punten]').forEach((card) => {
        if ((state.spaarpunten ? 'aan' : 'uit') === card.dataset.punten) card.classList.add('selected');
        card.addEventListener('click', () => {
            $$('[data-punten]').forEach((c) => c.classList.remove('selected'));
            card.classList.add('selected');
            state.spaarpunten = card.dataset.punten === 'aan';
            save();
        });
    });

    /* Domein */
    const syncDomainUI = () => {
        $$('[data-domain]').forEach((c) => c.classList.toggle('selected', c.dataset.domain === state.domainMode));
        $('#ownDomainWrap').classList.toggle('hidden', state.domainMode !== 'own');
        runDomainCheck();
    };
    $$('[data-domain]').forEach((card) => {
        card.addEventListener('click', () => {
            /* Eigen domein is voor abonnees: in de proefperiode blijft het subdomein actief */
            if (card.dataset.domain === 'own' && window.PP_PROEF === true) {
                toast('Een eigen domein kan zodra je abonnement actief is. Start \'m vanuit je dashboard.', 4200);
                return;
            }
            state.domainMode = card.dataset.domain;
            setError('domain');
            save();
            syncDomainUI();
            if (state.domainMode === 'own') $('#inpOwnDomain').focus();
        });
    });
    syncDomainUI();

    const ownInp = $('#inpOwnDomain');
    ownInp.value = state.ownDomain;
    ownInp.addEventListener('input', () => {
        state.ownDomain = ownInp.value;
        const preview = $('#ownDomainPreview');
        const clean = cleanDomain(ownInp.value);
        if (/.+\..{2,}/.test(clean)) {
            preview.querySelector('span').textContent = `bestellen.${clean}`;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
        setError('domain');
        save();
    });

    /* Thema's */
    $('#themeGrid').addEventListener('click', (e) => {
        const card = e.target.closest('[data-theme]');
        if (!card) return;
        state.theme = card.dataset.theme;
        renderThemes();
        renderPreview();
        updateObPreview();
        save();
    });

    /* Paginaknoppen bij het live voorbeeld */
    document.addEventListener('click', (e) => {
        const knop = e.target.closest('[data-ob-pagina]');
        if (!knop) return;
        obPagina = knop.dataset.obPagina;
        updateObPreview();
    });

    /* Kleuren: filter-tabs en de kleurstijlen zelf */
    $('#colorGrid').addEventListener('click', (e) => {
        const tab = e.target.closest('[data-kleurtab]');
        if (tab) {
            kleurTab = tab.dataset.kleurtab;
            renderColors();
            return;
        }
        const sw = e.target.closest('[data-color]');
        if (!sw) return;
        state.color = sw.dataset.color;
        renderColors();
        renderThemes();
        renderPreview();
        updateObPreview();
        save();
    });

    /* Logo uploaden */
    $('#logoInp').addEventListener('change', async (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        try {
            state.logo = await photoToThumb(file, 'image/png', 128);
            renderLogoUI();
            renderPreview();
            save();
            toast('Logo toegevoegd');
        } catch {
            toast('Dat bestand lukt niet, probeer een ander.');
        }
        e.target.value = '';
    });
    $('#logoRemove').addEventListener('click', () => {
        state.logo = null;
        renderLogoUI();
        renderPreview();
        save();
    });

    /* Mini-bestelpagina in de preview (één luisteraar voor alle templates) */
    $('#pvScreen')?.addEventListener('click', (e) => {
        const add = e.target.closest('[data-pv-add]');
        if (add) { pvCart[add.dataset.pvAdd] = (pvCart[add.dataset.pvAdd] || 0) + 1; renderPreview(); return; }
        const chip = e.target.closest('[data-pv-cat]');
        if (chip) { pvActiveCat = chip.dataset.pvCat; renderPreview(); return; }
        const seg = e.target.closest('[data-pv-mode]');
        if (seg) { pvMode = seg.dataset.pvMode; renderPreview(); }
    });

    /* Overzicht */
    $('#summary')?.addEventListener('click', (e) => {
        const row = e.target.closest('[data-jump]');
        if (row) jumpTo(row.dataset.jump);
    });

    /* Wachtwoordvelden (alleen voor gasten aanwezig) */
    $('#inpPassword')?.addEventListener('input', (e) => { pw = e.target.value; setError('password'); renderPwChecklist(); });
    $('#inpPassword2')?.addEventListener('input', (e) => { pw2 = e.target.value; });
    renderPwChecklist();

    /* Oogjes: wachtwoord tonen of weer verbergen */
    $$('[data-oog]').forEach((oog) => oog.addEventListener('click', () => {
        const veld = $('#' + oog.dataset.oog);
        const toon = veld.type === 'password';
        veld.type = toon ? 'text' : 'password';
        oog.innerHTML = toon
            ? '<i class="fa-solid fa-eye-slash" aria-hidden="true"></i>'
            : '<i class="fa-solid fa-eye" aria-hidden="true"></i>';
        veld.focus();
        veld.setSelectionRange(veld.value.length, veld.value.length);
    }));

    /* Versturen: maakt het account aan (of werkt het bij) en logt direct in */
    $('#submitBtn')?.addEventListener('click', async () => {
        const btn = $('#submitBtn');
        /* In het venster staat de knop op de laatste vraag zelf: die eerst nog even nakijken */
        if (IS_MODAL && !validate(state.step)) return;
        if (!IS_AUTH && !pwVoldoet()) {
            toast('Kies eerst nog een wachtwoord.');
            jumpTo('wachtwoord');
            return;
        }
        btn.disabled = true;
        btn.textContent = 'Momentje…';

        /* Ingelogd afronden = de vervolg-stappen zijn doorlopen: de zaak is compleet */
        if (IS_AUTH) { state.setup_compleet = true; save(); }

        try {
            const res = await verstuurOnboarding();
            if (res.ok) {
                state.submitted = true;
                save();
                btn.textContent = 'Klaar!';
                if (IS_MODAL) {
                    /* Het dashboard herlaadt zonder venster, met de menukaart erin */
                    setTimeout(() => { window.location.reload(); }, 600);
                    return;
                }
                launchConfetti();
                setTimeout(() => { window.location.href = '/dashboard'; }, 1400);
                return;
            }
            const data = await res.json().catch(() => ({}));
            const firstError = data.errors ? Object.values(data.errors)[0][0] : 'Er ging iets mis. Probeer het zo nog eens.';
            toast(firstError, 4200);
            if (data.errors?.['state.email']) jumpTo('contact');
            else if (data.errors?.password) jumpTo('wachtwoord');
            else if (['state.kvk', 'state.street', 'state.zip', 'state.city'].some((k) => data.errors?.[k])) jumpTo('company');
        } catch {
            toast('Geen verbinding. Check je internet en probeer opnieuw.');
        }
        btn.disabled = false;
        btn.textContent = IS_MODAL ? 'Zaak afronden' : 'Onboarding afronden';
    });

    /* Navigatie-knoppen */
    /* Live voorbeeld op mobiel: standaard ingeklapt, uitklappen via de knop */
    $('#pvToggle')?.addEventListener('click', () => {
        const open = ! $('#pvInhoud').classList.toggle('hidden');
        $('#pvToggle').textContent = open ? 'Verberg live voorbeeld' : 'Bekijk live voorbeeld';
    });

    $$('[data-next]').forEach((btn) => btn.addEventListener('click', next));
    $$('[data-back]').forEach((btn) => btn.addEventListener('click', back));
    $('#resetLink')?.addEventListener('click', resetAll);

    /* Enter = volgende */
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && pickerIdx !== null) { closeIconPicker(); return; }
        if (e.key !== 'Enter') return;
        if (e.target.id === 'newCatInp') { e.preventDefault(); confirmOwnCat(); return; }
        if (['customName', 'customPrice'].includes(e.target.id)) { e.preventDefault(); addCustomItem(); return; }
        if (e.target.tagName === 'BUTTON' || e.target.tagName === 'TEXTAREA') return;
        if (!stepEl(state.step)?.querySelector('[data-next]')) return;
        e.preventDefault();
        next();
    });

    /* Startpunt (met ?stap=… deep-link vanaf het dashboard) */
    const hadSaved = !!localStorage.getItem(STORAGE_KEY);
    const stapParam = new URLSearchParams(location.search).get('stap');
    if (stapParam && STEPS.includes(stapParam)) {
        $$('.step').forEach((s) => s.classList.add('hidden'));
        show(stapParam, { animate: false });
    } else if (hadProgress) {
        $$('.step').forEach((s) => s.classList.add('hidden'));
        show(state.step, { animate: false });
        toast('We zijn verdergegaan waar je was gebleven.');
    } else {
        show(STEPS[0], { animate: false });
    }
    if (hadSaved || hadProgress) $('#resetLink')?.classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', init);

})();
