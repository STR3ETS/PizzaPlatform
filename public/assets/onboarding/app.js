/* ═══════════════ PizzaPlatform onboarding wizard ═══════════════ */

/* De onboarding is ook de registratie: ingelogde gebruikers slaan de wachtwoord-stap over */
const IS_AUTH = window.PP_AUTH === true;
const STEPS = ['name', 'person', 'contact', ...(IS_AUTH ? [] : ['wachtwoord']), 'company', 'hours', 'menu', 'payment', 'domain', 'style', 'overview'];
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

const PICKER_EMOJIS = ['🍕', '🧀', '🍄', '🌶️', '🥩', '🍗', '🥓', '🥙', '🐟', '🍤', '🦐', '🍣', '🥦', '🫑', '🍅', '🌿', '🥗', '🍍', '🫒', '🥟', '🍝', '🥖', '🥤', '🍊', '🧋', '☕', '🍺', '🍷', '🍰', '🍨', '🍩', '🍫', '⭐', '🔥', '👨‍🍳', '🧄'];

const COLORS = [
    { hex: '#E63946', name: 'Tomatenrood' },
    { hex: '#2F8F46', name: 'Basilicumgroen' },
    { hex: '#F5B301', name: 'Goudgeel' },
    { hex: '#7C3AED', name: 'Paars' },
    { hex: '#2563EB', name: 'Blauw' },
    { hex: '#38221A', name: 'Cacao' },
];

/* Zes stijlen die echt van elkaar verschillen; de accentkleur komt van de klant */
const THEMES = [
    {
        id: 'fresco', emoji: '🍕', name: 'Fresco', desc: 'Speels en vrolijk',
        font: '"Lilita One", cursive', uppercase: false,
        cardBg: '#FFFFFF', itemBg: '#FFF6E8', text: '#38221A', priceCol: 'rgba(56,34,26,.55)',
        headerUseAccent: true, itemRadius: '.7rem', btnRadius: '9999px',
    },
    {
        id: 'nero', emoji: '🌙', name: 'Nero', desc: 'Donker en chic',
        font: 'Georgia, serif', uppercase: false,
        cardBg: '#1C1512', itemBg: '#2A211C', text: '#F3EAD9', priceCol: '#C9A96A',
        headerUseAccent: false, headerBg: '#1C1512', itemRadius: '.45rem', btnRadius: '.45rem',
    },
    {
        id: 'napoli', emoji: '🍝', name: 'Napoli', desc: 'Klassieke menukaart',
        font: '"Times New Roman", serif', uppercase: false,
        cardBg: '#FFFDF6', itemBg: '#FFF8E7', text: '#3A2A1A', priceCol: '#8A6A3B',
        headerUseAccent: true, stripe: true, itemRadius: '.35rem', btnRadius: '.35rem',
        itemBorder: '1px solid #E8DCC2',
    },
    {
        id: 'puro', emoji: '🌿', name: 'Puro', desc: 'Strak met foto-grid',
        font: '"Nunito", sans-serif', uppercase: false,
        cardBg: '#FFFFFF', itemBg: '#F4F7F4', text: '#233029', priceCol: '#5B6B60',
        headerUseAccent: true, itemRadius: '.7rem', btnRadius: '.7rem',
    },
    {
        id: 'blocco', emoji: '⚡', name: 'Blocco', desc: 'Bold en street',
        font: '"Arial Black", Impact, sans-serif', uppercase: true,
        cardBg: '#FFFFFF', itemBg: '#FFFFFF', text: '#111111', priceCol: '#111111',
        headerUseAccent: true, itemRadius: '0', btnRadius: '0',
        itemBorder: '2px solid #111111', btnBorder: '2px solid #111111', btnShadow: '3px 3px 0 0 #111111',
    },
    {
        id: 'retro', emoji: '🪩', name: 'Retro', desc: 'Vintage jaren 70',
        font: '"Cooper Black", Georgia, serif', uppercase: false,
        cardBg: '#FBEED3', itemBg: '#F5E0B4', text: '#5B3A21', priceCol: '#8A5A2B',
        headerUseAccent: true, itemRadius: '1.1rem', btnRadius: '2rem',
    },
];

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
    payment: null,
    domainMode: 'sub', ownDomain: '',
    color: '#E63946',
    theme: 'fresco',
    logo: null,
    returnTo: null,
    submitted: false,
};

let addingCat = false;           // "eigen categorie"-invoer open?
let pickerIdx = null;            // menu-index waarvoor de icoon-kiezer open staat
let pw = '';                     // wachtwoord bewust NIET in localStorage
let pw2 = '';

const $  = (sel) => document.querySelector(sel);
const $$ = (sel) => [...document.querySelectorAll(sel)];
const stepEl = (id) => $(`[data-step="${id}"]`);
const esc = (str) => String(str).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* ── Opslaan & herstellen ─────────────────────────────────────── */

function save() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
}

function normaliseerState() {
    if (!STEPS.includes(state.step)) state.step = 'name';
    if (!state.categories?.length) {
        state.categories = [{ id: 'klassiekers', emoji: '🍕', name: 'Klassiekers' }];
    }
    if (!state.categories.some((c) => c.id === state.activeCat)) state.activeCat = state.categories[0].id;
    state.menu = (state.menu || []).map((m) => ({
        cat: m.cat && state.categories.some((c) => c.id === m.cat) ? m.cat : state.categories[0].id,
        icon: m.icon?.v ? m.icon : { t: 'e', v: m.emoji || '🍕' },
        name: m.name, price: m.price,
    }));
}

function restore() {
    /* Ingelogd: je opgeslagen onboarding uit de database is leidend,
       zodat je nooit iets opnieuw hoeft in te vullen */
    if (IS_AUTH && window.PP_SAVED && typeof window.PP_SAVED === 'object') {
        state = { ...state, ...window.PP_SAVED, step: 'name', returnTo: null, submitted: false };
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
        to.classList.add('step-enter');
        setTimeout(() => to.classList.remove('step-enter'), 450);
        updateChrome();
        save();
        window.scrollTo({ top: 0 });
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

function next() {
    if (!validate(state.step)) return;
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
    else window.location.href = '/';    /* eerste stap: terug naar de welkomstpagina */
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
        $('#progressBar').style.width = '100%';
        $('#stepCounter').textContent = 'Laatste check ✓';
    } else if (isQuestion) {
        const i = QUESTION_STEPS.indexOf(state.step);
        $('#progressBar').style.width = `${Math.round(((i + 1) / (QUESTION_STEPS.length + 1)) * 100)}%`;
        $('#stepCounter').textContent = `Stap ${i + 1} van ${QUESTION_STEPS.length}`;
    }
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
            if (state.name.trim().length < 2) { setError('name', 'Zonder naam geen pizza 😉'); nudge($('#inpName')); return false; }
            setError('name'); return true;
        case 'person':
            if (state.person.trim().length < 2) { setError('person', 'We willen toch écht even weten wie je bent 😄'); nudge($('#inpPerson')); return false; }
            setError('person'); return true;
        case 'contact':
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(state.email.trim())) {
                setError('email', 'Hmm, dit e-mailadres ziet er nog niet helemaal lekker uit 🧐'); nudge($('#inpEmail')); return false;
            }
            setError('email'); return true;
        case 'wachtwoord':
            if (pw.length < 8) { setError('password', 'Minimaal 8 tekens, dan zit je goed 💪'); nudge($('#inpPassword')); return false; }
            if (pw !== pw2) { setError('password', 'De wachtwoorden zijn niet hetzelfde 🧐'); nudge($('#inpPassword2')); return false; }
            setError('password'); return true;
        case 'hours':
            if (!Object.values(state.days).some(Boolean)) { setError('hours', 'Kies minstens één dag, of sla deze stap over 👇'); return false; }
            setError('hours'); return true;
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
        $$('[data-slug]').forEach((el) => (el.textContent = slugify(state.name) || 'jouwpizzeria'));
        runDomainCheck();
    }
    if (id === 'style') { pvCart = {}; pvActiveCat = null; pvMode = 'bezorgen'; renderThemes(); renderColors(); renderLogoUI(); renderPreview(); }
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
    if (item.icon.t === 'p') return `<img src="${item.icon.v}" class="${cls} rounded-md object-cover inline-block align-middle" alt="">`;
    return `<span class="inline-block align-middle">${item.icon.v}</span>`;
}

function toast(msg, ms = 2600) {
    const el = $('#toast');
    el.textContent = msg;
    el.classList.remove('hidden');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.add('hidden'), ms);
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
        : `<div class="empty-box">Tik hierboven eerst een dag aan 👆</div>`;
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
            <span class="cat-del" data-delcat="${c.id}" title="Categorie verwijderen">✕</span>
        </button>`
    ).join('');
    const suggest = remaining.map((s) =>
        `<button type="button" class="cat-chip suggest" data-addcat="${s.key}">+ ${s.emoji} ${s.name}</button>`
    ).join('');
    const custom = addingCat
        ? `<input id="newCatInp" type="text" class="cat-inp" placeholder="Bijv. Broodjes" maxlength="24">`
        : `<button type="button" id="addOwnCat" class="cat-chip suggest">+ ✏️ Eigen categorie</button>`;
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
    toast(`Categorie "${cat.name}" verwijderd 🗑️`);
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
            <span class="p-emoji">${p.emoji}</span>
            <span class="p-name">${p.name}</span>
            <span class="p-price">€ ${p.price}</span>
        </button>`;
    }).join('');
}

function renderMenuList(popIdx = null) {
    const list = $('#menuList');
    if (!state.categories.length) {
        list.innerHTML = `<div class="empty-box">Voeg eerst een categorie toe 👆</div>`;
        return;
    }
    const cat = activeCategory();
    const items = state.menu.map((m, gi) => ({ ...m, gi })).filter((m) => m.cat === state.activeCat);
    if (!items.length) {
        list.innerHTML = `<div class="empty-box">Nog niks in ${esc(cat.name)}. Tik hierboven iets aan of voeg zelf toe 👇</div>`;
        return;
    }
    list.innerHTML = items.map((m) =>
        `<div class="menu-row${m.gi === popIdx ? ' pop' : ''}">
            <button type="button" class="m-icon" data-icon-idx="${m.gi}" title="Plaatje of foto kiezen">${iconHtml(m, 'w-full h-full')}</button>
            <span class="m-name">${esc(m.name)}</span>
            <span class="font-extrabold text-cacao/40 text-sm">€</span>
            <input type="text" inputmode="decimal" value="${m.price}" data-price-idx="${m.gi}" aria-label="Prijs van ${esc(m.name)}">
            <button type="button" class="m-del" data-del-idx="${m.gi}" aria-label="Verwijder ${esc(m.name)}">✕</button>
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

function addMenuItem(emoji, name, price) {
    ensureCategory();
    state.menu.push({ cat: state.activeCat, icon: { t: 'e', v: emoji }, name, price });
    renderMenuUI(state.menu.length - 1);    /* alleen de nieuwe rij krijgt de pop-animatie */
    save();
}

function addCustomItem() {
    const nameInp = $('#customName');
    const priceInp = $('#customPrice');
    const name = nameInp.value.trim();
    const price = formatPrice(priceInp.value) || '12,50';
    if (name.length < 2) { nudge(nameInp); return; }
    addMenuItem('⭐', name, price);
    nameInp.value = '';
    priceInp.value = '';
    nameInp.focus();
}

/* ── Icoon/foto-kiezer ────────────────────────────────────────── */

function openIconPicker(idx) {
    pickerIdx = idx;
    const picker = $('#iconPicker');
    picker.classList.remove('hidden');
    picker.classList.add('flex');
}

function closeIconPicker() {
    pickerIdx = null;
    $('#photoInp').value = '';
    const picker = $('#iconPicker');
    picker.classList.add('hidden');
    picker.classList.remove('flex');
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
    el.textContent = 'Even checken of dit adres vrij is… 🔍';
    el.className = 'mt-4 h-6 text-sm font-extrabold text-cacao/45';
    domainCheckTimer = setTimeout(() => {
        el.textContent = '✓ Beschikbaar! Die is voor jou.';
        el.className = 'mt-4 h-6 text-sm font-extrabold text-basil';
    }, 900);
}

/* ── Huisstijl & preview ──────────────────────────────────────── */

function currentTheme() {
    return THEMES.find((t) => t.id === state.theme) || THEMES[0];
}

/* Mini-wireframes die de lay-out van elk template laten zien */
const TILES = {
    fresco: (c) => `<span class="flex flex-col gap-[3px] w-full h-full p-[6px]"><span class="h-3 rounded-b-[8px] rounded-t-[3px]" style="background:${c}"></span><span class="h-1.5 rounded-full bg-white"></span><span class="h-1.5 rounded-full bg-white"></span></span>`,
    nero: (c) => `<span class="flex flex-col items-center justify-center gap-[3px] w-full h-full"><span class="w-2.5 h-2.5 rounded-full" style="border:1px solid #C9A96A"></span><span class="w-8" style="height:2px; background:#C9A96A"></span><span class="w-6" style="height:2px; background:rgba(255,255,255,.35)"></span></span>`,
    napoli: (c) => `<span class="flex flex-col gap-[4px] w-full h-full p-[6px]"><span class="flex w-full" style="height:3px"><span style="flex:1;background:#2F8F46"></span><span style="flex:1;background:#fff"></span><span style="flex:1;background:#E63946"></span></span><span class="w-10 mx-auto" style="height:2px; background:#3A2A1A"></span><span class="w-full" style="height:2px; background:#D9C9A8"></span><span class="w-full" style="height:2px; background:#D9C9A8"></span></span>`,
    puro: (c) => `<span class="grid grid-cols-2 gap-[3px] w-full h-full p-[6px]"><span class="rounded-[3px] bg-white"></span><span class="rounded-[3px] bg-white"></span><span class="rounded-[3px] bg-white"></span><span class="rounded-[3px]" style="background:${c}"></span></span>`,
    blocco: (c) => `<span class="flex flex-col gap-[3px] w-full h-full p-[6px]"><span class="h-2.5" style="background:${c}; border:2px solid #111"></span><span class="h-2 bg-white" style="border:2px solid #111"></span></span>`,
    retro: (c) => `<span class="flex flex-col gap-[3px] w-full h-full p-[5px]"><span class="h-3" style="background:${c}; border-radius:0 0 50% 50%"></span><span class="h-1.5 rounded-full bg-white"></span><span class="h-1.5 rounded-full" style="background:#E9CD9B"></span></span>`,
};

function renderThemes() {
    $('#themeGrid').innerHTML = THEMES.map((t) =>
        `<button type="button" class="theme-card ${state.theme === t.id ? 'selected' : ''}" data-theme="${t.id}">
            <span class="t-tile" style="background:${t.itemBg}; ${t.itemBorder ? 'border:' + t.itemBorder + ';' : ''}">${TILES[t.id](state.color)}</span>
            <span class="t-name">${t.emoji} ${t.name}</span>
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
    const isCustom = !COLORS.some((c) => c.hex === state.color);
    const customStyle = isCustom
        ? `background:${state.color}`
        : 'background:conic-gradient(#E63946, #F5B301, #2F8F46, #2563EB, #7C3AED, #E63946)';
    $('#colorGrid').innerHTML = COLORS.map((c) =>
        `<button type="button" class="swatch ${state.color === c.hex ? 'selected' : ''}" data-color="${c.hex}"
            style="background:${c.hex}" title="${c.name}" aria-label="${c.name}"></button>`
    ).join('') +
        `<button type="button" id="customSwatch" class="swatch ${isCustom ? 'selected' : ''}" style="${customStyle}"
            title="Eigen kleur" aria-label="Eigen kleur kiezen">${isCustom ? '' : '<span class="swatch-plus">+</span>'}</button>`;
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
        : [{ id: 'demo', emoji: '🍕', name: "Pizza's" }];
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

/* Zes echt verschillende lay-outs voor de bestelpagina */
const PV_TEMPLATES = {
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
                <span class="flex items-center gap-1.5 min-w-0">${m.icon.t === 'p' ? iconHtml(m, 'w-4 h-4') : ''}<span class="truncate">${esc(m.name)}</span></span>
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
    const t = currentTheme();
    $('#pvFrame').style.background = t.cardBg;
    $('#pvScreen').innerHTML = (PV_TEMPLATES[t.id] || PV_TEMPLATES.fresco)(pvViewData());
}

/* ── Overzicht ────────────────────────────────────────────────── */

function renderSummary() {
    const payLabels = { mollie: 'iDEAL via Mollie', stripe: 'Creditcard via Stripe', later: 'Regelen we samen later' };
    const address = [state.street, [state.zip, state.city].filter(Boolean).join(' ')].filter(Boolean).join(', ');
    const orderUrl = state.domainMode === 'own' && state.ownDomain
        ? `bestellen.${cleanDomain(state.ownDomain)}`
        : `${slugify(state.name) || 'jouwpizzeria'}.bestelpagina.nl`;
    const colorName = COLORS.find((c) => c.hex === state.color)?.name || 'Eigen kleur';
    const theme = currentTheme();
    const usedCats = state.categories.filter((c) => state.menu.some((m) => m.cat === c.id));
    const menuValue = state.menu.length
        ? `${state.menu.length} gerecht${state.menu.length === 1 ? '' : 'en'} in ${usedCats.length} categorie${usedCats.length === 1 ? '' : 'ën'}: ${usedCats.map((c) => esc(c.name)).join(', ')}`
        : 'Maken we later samen af 👍';

    const rows = [
        { label: 'Pizzeria',      value: esc(state.name), step: 'name' },
        { label: 'Contact',       value: `${esc(state.person)}${state.phone ? ', ' + esc(state.phone) : ''}`, step: 'person' },
        { label: 'E-mail',        value: esc(state.email), step: 'contact' },
        ...(IS_AUTH ? [] : [{ label: 'Wachtwoord', value: pw ? '••••••••' : 'Nog niet gekozen', step: 'wachtwoord' }]),
        { label: 'KvK & adres',   value: esc([state.kvk, address].filter(Boolean).join(', ')) || 'Doen we later samen 👍', step: 'company' },
        { label: 'Open',          value: formatDaysSummary(), step: 'hours' },
        { label: 'Menu',          value: menuValue, step: 'menu' },
        { label: 'Betaling',      value: payLabels[state.payment] || 'Regelen we samen later', step: 'payment' },
        { label: 'Bestel-adres',  value: esc(orderUrl), step: 'domain' },
        { label: 'Template',      value: `${theme.emoji} ${esc(theme.name)}, <span class="inline-block w-4 h-4 rounded-full align-middle mx-1" style="background:${state.color}"></span>${colorName}${state.logo ? ', met logo' : ''}`, step: 'style' },
    ];

    $('#summary').innerHTML = rows.map((r) =>
        `<button type="button" class="sum-row" data-jump="${r.step}">
            <span class="sum-label">${r.label}</span>
            <span class="sum-value">${r.value}</span>
            <span class="sum-edit">aanpassen ✎</span>
        </button>`
    ).join('');
}

/* ── Confetti ─────────────────────────────────────────────────── */

function launchConfetti() {
    const canvas = $('#confetti');
    const ctx = canvas.getContext('2d');
    canvas.width = innerWidth;
    canvas.height = innerHeight;
    canvas.classList.remove('hidden');

    const colors = ['#E63946', '#2F8F46', '#F5B301', '#7C3AED', '#2563EB', '#FFF6E8'];
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
    el.value = state[key];
    el.addEventListener('input', () => {
        state[key] = el.value;
        save();
        extra?.();
    });
}

function init() {
    const hadProgress = restore();

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
    renderMenuUI();
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
    $('#catBar').addEventListener('click', (e) => {
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
    $('#catBar').addEventListener('focusout', (e) => {
        if (e.target.id === 'newCatInp') confirmOwnCat();
    });

    /* Menu-presets & lijst */
    $('#presetGrid').addEventListener('click', (e) => {
        const card = e.target.closest('[data-preset]');
        if (!card) return;
        const p = suggestionFor(state.activeCat)?.presets[card.dataset.preset];
        if (!p) return;
        const idx = state.menu.findIndex((m) => m.cat === state.activeCat && m.name === p.name);
        if (idx >= 0) { state.menu.splice(idx, 1); renderMenuUI(); save(); }
        else addMenuItem(p.emoji, p.name, p.price);
    });
    $('#menuList').addEventListener('click', (e) => {
        const icon = e.target.closest('[data-icon-idx]');
        if (icon) { openIconPicker(Number(icon.dataset.iconIdx)); return; }
        const del = e.target.closest('[data-del-idx]');
        if (!del) return;
        state.menu.splice(del.dataset.delIdx, 1);
        renderMenuUI();
        save();
    });
    $('#menuList').addEventListener('change', (e) => {
        const inp = e.target.closest('[data-price-idx]');
        if (!inp) return;
        const formatted = formatPrice(inp.value);
        if (formatted) state.menu[inp.dataset.priceIdx].price = formatted;
        inp.value = state.menu[inp.dataset.priceIdx].price;
        save();
    });
    $('#customAdd').addEventListener('click', addCustomItem);

    /* Icoon/foto-kiezer */
    $('#emojiGrid').innerHTML = PICKER_EMOJIS.map((e) =>
        `<button type="button" class="emoji-opt" data-emoji="${e}">${e}</button>`
    ).join('');
    $('#emojiGrid').addEventListener('click', (e) => {
        const opt = e.target.closest('[data-emoji]');
        if (opt) setItemIcon({ t: 'e', v: opt.dataset.emoji });
    });
    $('#photoInp').addEventListener('change', async (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        try {
            setItemIcon({ t: 'p', v: await photoToThumb(file) });
            toast('Mooie foto! 📸');
        } catch {
            toast('Hmm, die afbeelding lukt niet. Probeer een andere 🙈');
        }
    });
    $('#pickerClose').addEventListener('click', closeIconPicker);
    $('#iconPicker').addEventListener('click', (e) => {
        if (e.target.id === 'iconPicker') closeIconPicker();
    });

    /* Betaling: automatisch door naar de volgende stap */
    $$('[data-pay]').forEach((card) => {
        if (state.payment === card.dataset.pay) card.classList.add('selected');
        card.addEventListener('click', () => {
            $$('[data-pay]').forEach((c) => c.classList.remove('selected'));
            card.classList.add('selected');
            state.payment = card.dataset.pay;
            save();
            setTimeout(next, 380);
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
        save();
    });

    /* Kleuren */
    $('#colorGrid').addEventListener('click', (e) => {
        if (e.target.closest('#customSwatch')) {
            const inp = $('#customColor');
            inp.value = /^#[0-9a-f]{6}$/i.test(state.color) ? state.color : '#E63946';
            inp.click();
            return;
        }
        const sw = e.target.closest('[data-color]');
        if (!sw) return;
        state.color = sw.dataset.color;
        renderColors();
        renderThemes();
        renderPreview();
        save();
    });
    $('#customColor').addEventListener('input', (e) => {
        state.color = e.target.value;
        renderColors();
        renderThemes();
        renderPreview();
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
            toast('Mooi logo! 🤩');
        } catch {
            toast('Hmm, dat bestand lukt niet. Probeer een ander 🙈');
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
    $('#pvScreen').addEventListener('click', (e) => {
        const add = e.target.closest('[data-pv-add]');
        if (add) { pvCart[add.dataset.pvAdd] = (pvCart[add.dataset.pvAdd] || 0) + 1; renderPreview(); return; }
        const chip = e.target.closest('[data-pv-cat]');
        if (chip) { pvActiveCat = chip.dataset.pvCat; renderPreview(); return; }
        const seg = e.target.closest('[data-pv-mode]');
        if (seg) { pvMode = seg.dataset.pvMode; renderPreview(); }
    });

    /* Overzicht */
    $('#summary').addEventListener('click', (e) => {
        const row = e.target.closest('[data-jump]');
        if (row) jumpTo(row.dataset.jump);
    });

    /* Wachtwoordvelden (alleen voor gasten aanwezig) */
    $('#inpPassword')?.addEventListener('input', (e) => { pw = e.target.value; setError('password'); });
    $('#inpPassword2')?.addEventListener('input', (e) => { pw2 = e.target.value; });

    /* Snel opslaan (alleen ingelogd): wijziging bewaren zonder alle stappen af te lopen */
    $('#quickSave')?.addEventListener('click', async () => {
        const btn = $('#quickSave');
        btn.disabled = true;
        btn.textContent = 'Opslaan…';
        try {
            const res = await verstuurOnboarding();
            if (res.ok) {
                btn.textContent = 'Opgeslagen ✓';
                setTimeout(() => { window.location.href = '/dashboard'; }, 500);
                return;
            }
            const data = await res.json().catch(() => ({}));
            toast(data.errors ? Object.values(data.errors)[0][0] : 'Opslaan lukte niet, probeer het nog eens 🙏', 4000);
        } catch {
            toast('Geen verbinding. Check je internet en probeer opnieuw 📶');
        }
        btn.disabled = false;
        btn.textContent = 'Opslaan ✓';
    });

    /* Versturen: maakt het account aan (of werkt het bij) en logt direct in */
    $('#submitBtn').addEventListener('click', async () => {
        const btn = $('#submitBtn');
        if (!IS_AUTH && pw.length < 8) {
            toast('Kies eerst nog even een wachtwoord 🔐');
            jumpTo('wachtwoord');
            return;
        }
        btn.disabled = true;
        btn.textContent = 'Momentje… 🛵💨';

        try {
            const res = await verstuurOnboarding();
            if (res.ok) {
                state.submitted = true;
                save();
                btn.textContent = 'Klaar! 🎉';
                launchConfetti();
                setTimeout(() => { window.location.href = '/dashboard'; }, 1400);
                return;
            }
            const data = await res.json().catch(() => ({}));
            const firstError = data.errors ? Object.values(data.errors)[0][0] : 'Er ging iets mis. Probeer het zo nog eens 🙏';
            toast(firstError, 4200);
            if (data.errors?.['state.email']) jumpTo('contact');
            else if (data.errors?.password) jumpTo('wachtwoord');
        } catch {
            toast('Geen verbinding. Check je internet en probeer opnieuw 📶');
        }
        btn.disabled = false;
        btn.textContent = 'Onboarding afronden 🚀';
    });

    /* Navigatie-knoppen */
    $$('[data-next]').forEach((btn) => btn.addEventListener('click', next));
    $$('[data-skip]').forEach((btn) => btn.addEventListener('click', () => {
        if (state.returnTo) { const t = state.returnTo; state.returnTo = null; show(t); }
        else {
            const i = STEPS.indexOf(state.step);
            show(STEPS[i + 1]);
        }
    }));
    $$('[data-back]').forEach((btn) => btn.addEventListener('click', back));
    $('#resetLink').addEventListener('click', resetAll);

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
        toast('We zijn verdergegaan waar je was gebleven 👌');
    } else {
        show('name', { animate: false });
    }
    if (hadSaved || hadProgress) $('#resetLink').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', init);
