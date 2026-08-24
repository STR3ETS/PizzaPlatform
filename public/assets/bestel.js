/* ═══════════════ Bestelpagina: kiezen, mandje, afrekenen ═══════════════ */

const $ = (sel) => document.querySelector(sel);
const B = window.BESTEL || { menu: [] };
const MENU = new Map(B.menu.map((m) => [m.id, m]));
const euro = (c) => '€ ' + (c / 100).toFixed(2).replace('.', ',');
const esc = (str) => String(str).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* ── Mandje ───────────────────────────────────────────────────── */

let mand = [];
try { mand = JSON.parse(localStorage.getItem('bestel_' + B.slug) || '[]'); } catch { mand = []; }
mand = mand.filter((r) => MENU.has(r.id));

const klantGeg = { naam: '', type: B.alleenAfhalen ? 'afhalen' : 'bezorgen', adres: '', postcode: '', plaats: '', opmerking: '' };

const bewaar = () => localStorage.setItem('bestel_' + B.slug, JSON.stringify(mand));

function regelPrijs(regel) {
    const m = MENU.get(regel.id);
    let prijs = m.prijs;
    (regel.keuzes || []).forEach(([gi, ki]) => {
        const keuze = m.opties?.[gi]?.keuzes?.[ki];
        if (keuze) prijs += Number(keuze.prijs) || 0;
    });
    return prijs * regel.aantal;
}
const mandTotaal = () => mand.reduce((som, r) => som + regelPrijs(r), 0);
const mandStuks = () => mand.reduce((som, r) => som + r.aantal, 0);

function werkBalkBij() {
    const balk = $('#mandBalk');
    if (balk) {
        balk.classList.toggle('hidden', mandStuks() === 0);
        $('#mandAantal').textContent = mandStuks();
        $('#mandTotaal').textContent = euro(mandTotaal());
    }
    renderZijMand();
}

/* Het sticky winkelmandje naast de menukaart (desktop) */
function renderZijMand() {
    const vak = $('#zijMand');
    if (!vak) return;
    const leeg = mand.length === 0;
    $('#zijLeeg')?.classList.toggle('hidden', !leeg);
    $('#zijOnder')?.classList.toggle('hidden', leeg);
    vak.innerHTML = mand.map((regel, i) => {
        const m = MENU.get(regel.id);
        return `
        <div class="flex items-start gap-2 text-sm">
            <div class="flex-1 min-w-0">
                <p class="font-extrabold truncate">${esc(m.naam)}</p>
                ${optieTekst(regel) ? `<p class="text-xs opacity-60">${esc(optieTekst(regel))}</p>` : ''}
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" class="b-chip !px-2 !py-0.5" data-r-min="${i}">&minus;</button>
                <span class="font-black w-4 text-center">${regel.aantal}</span>
                <button type="button" class="b-chip !px-2 !py-0.5" data-r-plus="${i}">+</button>
            </div>
            <span class="font-extrabold w-14 text-right shrink-0">${euro(regelPrijs(regel))}</span>
        </div>`;
    }).join('');
    const totaal = $('#zijTotaal');
    if (totaal) totaal.textContent = euro(mandTotaal());
}

/* ── Gerecht-detail ───────────────────────────────────────────── */

let openItem = null;   // { id, aantal, keuze: per groep (index of Set), zonder: Set }

function toonItem(id) {
    const m = MENU.get(id);
    if (!m) return;
    openItem = {
        id,
        aantal: 1,
        keuze: (m.opties || []).map((g) => (g.type === 'een' ? null : new Set())),
        zonder: new Set(),
    };
    renderItemSheet();
    $('#itemSheetVak').classList.remove('hidden');
}

function sluitItem() {
    $('#itemSheetVak').classList.add('hidden');
    openItem = null;
}

function openItemPrijs() {
    const m = MENU.get(openItem.id);
    let prijs = m.prijs;
    (m.opties || []).forEach((g, gi) => {
        if (g.type === 'een') {
            const ki = openItem.keuze[gi];
            if (ki !== null) prijs += Number(g.keuzes[ki].prijs) || 0;
        } else {
            openItem.keuze[gi].forEach((ki) => { prijs += Number(g.keuzes[ki].prijs) || 0; });
        }
    });
    return prijs * openItem.aantal;
}

function renderItemSheet() {
    const m = MENU.get(openItem.id);
    const gekozen = (gi, ki) => (m.opties[gi].type === 'een' ? openItem.keuze[gi] === ki : openItem.keuze[gi].has(ki));
    $('#itemSheet').innerHTML = `
        ${m.foto ? `<div class="b-fotovak overflow-hidden mb-4" style="aspect-ratio: 16 / 7"><img class="b-foto" src="${esc(m.foto)}" alt="${esc(m.naam)}"></div>` : ''}
        <div class="flex items-start gap-3">
            <div class="flex-1 min-w-0">
                <p class="b-kop text-2xl">${m.icoon ? esc(m.icoon) + ' ' : ''}${esc(m.naam)}</p>
                ${m.beschrijving ? `<p class="text-sm opacity-70 mt-1">${esc(m.beschrijving)}</p>` : ''}
                ${(m.ingredienten || []).length ? `<p class="text-sm opacity-70 mt-1">${m.ingredienten.map(esc).join(', ')}</p>` : ''}
                ${Array.isArray(m.allergenen) && m.allergenen.length ? `<p class="b-allergeen mt-1">Allergenen: ${m.allergenen.map((a) => esc(a.charAt(0).toUpperCase() + a.slice(1))).join(', ')}</p>` : ''}
            </div>
            <button type="button" class="b-chip shrink-0" data-sluit-item>Sluiten</button>
        </div>

        ${(m.opties || []).map((g, gi) => `
            <div class="mt-5">
                <p class="b-kop text-lg">${esc(g.naam)} <span class="text-xs font-bold opacity-60">${g.type === 'een' ? 'kies er 1' : 'meerdere mogelijk'}</span></p>
                <div class="flex flex-wrap gap-2 mt-2">
                    ${g.keuzes.map((k, ki) => `
                        <button type="button" class="b-chip ${gekozen(gi, ki) ? 'aan' : ''}" data-g="${gi}" data-k="${ki}">
                            ${esc(k.naam)}${Number(k.prijs) ? ' +' + euro(Number(k.prijs)) : ''}
                        </button>`).join('')}
                </div>
            </div>`).join('')}

        ${(m.ingredienten || []).length ? `
            <div class="mt-5">
                <p class="b-kop text-lg">Liever zonder? <span class="text-xs font-bold opacity-60">tik aan wat je niet wilt</span></p>
                <div class="flex flex-wrap gap-2 mt-2">
                    ${m.ingredienten.map((ing) => `
                        <button type="button" class="b-chip ${openItem.zonder.has(ing) ? 'aan' : ''}" data-zonder="${esc(ing)}">zonder ${esc(ing)}</button>`).join('')}
                </div>
            </div>` : ''}

        <div class="flex items-center gap-4 mt-6">
            <div class="flex items-center gap-3">
                <button type="button" class="b-chip !px-4 !text-lg" data-min>&minus;</button>
                <span class="b-kop text-xl w-6 text-center">${openItem.aantal}</span>
                <button type="button" class="b-chip !px-4 !text-lg" data-plus>+</button>
            </div>
            <button type="button" class="b-knop flex-1 !py-3" data-toevoegen ${B.online ? '' : 'disabled'}>
                ${B.online ? 'Toevoegen' : 'Nu gesloten'} <span class="ml-auto">${euro(openItemPrijs())}</span>
            </button>
        </div>`;
}

/* ── Mandje en afrekenen ──────────────────────────────────────── */

let besteld = null;    // succes-info na het plaatsen

function toonMand() {
    renderMand();
    $('#mandSheetVak').classList.remove('hidden');
}

function sluitMand() {
    $('#mandSheetVak').classList.add('hidden');
    if (besteld) { besteld = null; werkBalkBij(); }
}

function optieTekst(regel) {
    const m = MENU.get(regel.id);
    const delen = [];
    (regel.keuzes || []).forEach(([gi, ki]) => {
        const keuze = m.opties?.[gi]?.keuzes?.[ki];
        if (keuze) delen.push(keuze.naam);
    });
    (regel.zonder || []).forEach((z) => delen.push('zonder ' + z));
    return delen.join(', ');
}

function renderMand() {
    if (besteld) return renderSucces();
    const bezorgen = klantGeg.type === 'bezorgen';
    $('#mandSheet').innerHTML = `
        <div class="flex items-center gap-3 mb-4">
            <p class="b-kop text-2xl flex-1">Jouw bestelling</p>
            <button type="button" class="b-chip" data-sluit-mand>Sluiten</button>
        </div>

        ${mand.length === 0 ? '<p class="opacity-70">Je mandje is nog leeg.</p>' : `
        <div class="space-y-3">
            ${mand.map((regel, i) => {
                const m = MENU.get(regel.id);
                return `
                <div class="flex items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="b-kop">${esc(m.naam)}</p>
                        ${optieTekst(regel) ? `<p class="text-xs opacity-70">${esc(optieTekst(regel))}</p>` : ''}
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" class="b-chip !px-2.5 !py-1" data-r-min="${i}">&minus;</button>
                        <span class="font-black w-5 text-center">${regel.aantal}</span>
                        <button type="button" class="b-chip !px-2.5 !py-1" data-r-plus="${i}">+</button>
                    </div>
                    <span class="b-kop b-prijs shrink-0 w-16 text-right">${euro(regelPrijs(regel))}</span>
                </div>`;
            }).join('')}
        </div>

        <div class="border-t-2 mt-4 pt-3 space-y-1" style="border-color: var(--prijs)">
            <div class="flex justify-between font-extrabold"><span>Subtotaal</span><span>${euro(mandTotaal())}</span></div>
            ${bezorgen && B.laagsteBezorgkosten ? `<div class="flex justify-between text-sm opacity-70"><span>Bezorgkosten</span><span>vanaf ${euro(B.laagsteBezorgkosten)}</span></div>` : ''}
        </div>

        <div class="mt-5 space-y-3">
            <p class="b-kop text-lg">Afrekenen</p>
            <input id="cNaam" class="b-veld" placeholder="Je naam" maxlength="60" value="${esc(klantGeg.naam)}">
            ${B.alleenAfhalen ? '<p class="text-sm opacity-70">Op dit moment alleen afhalen.</p>' : `
            <div class="flex gap-2">
                <button type="button" class="b-chip flex-1 justify-center ${bezorgen ? 'aan' : ''}" data-c-type="bezorgen">🛵 Bezorgen</button>
                <button type="button" class="b-chip flex-1 justify-center ${bezorgen ? '' : 'aan'}" data-c-type="afhalen">🥡 Afhalen</button>
            </div>`}
            ${bezorgen && !B.alleenAfhalen ? `
            <input id="cAdres" class="b-veld" placeholder="Straat + huisnummer" maxlength="120" value="${esc(klantGeg.adres)}">
            <div class="flex gap-2">
                <input id="cPostcode" class="b-veld !w-32" placeholder="Postcode" maxlength="10" value="${esc(klantGeg.postcode)}">
                <input id="cPlaats" class="b-veld flex-1" placeholder="Plaats" maxlength="60" value="${esc(klantGeg.plaats)}">
            </div>` : ''}
            <textarea id="cOpmerking" class="b-veld" rows="2" maxlength="300" placeholder="Opmerking voor de zaak (mag leeg)">${esc(klantGeg.opmerking)}</textarea>
            <p id="cFout" class="text-sm font-extrabold" style="color:#E63946"></p>
            <button type="button" class="b-knop w-full !py-3.5 !text-lg" data-plaatsen ${B.online && mand.length ? '' : 'disabled'}>
                ${B.online ? 'Bestelling plaatsen' : 'Nu gesloten'}
            </button>
            <p class="text-xs opacity-60 text-center">Betalen doe je bij het ${bezorgen ? 'bezorgen' : 'afhalen'}. Online betalen komt eraan!</p>
        </div>`}`;
}

function renderSucces() {
    $('#mandSheet').innerHTML = `
        <div class="text-center py-6" id="besteldOk">
            <span class="inline-grid place-items-center w-16 h-16 rounded-full text-white text-3xl mb-4" style="background:#2F8F46">✓</span>
            <p class="b-kop text-2xl mb-1">Bedankt, ${esc(klantGeg.naam)}!</p>
            <p class="opacity-75 mb-1">Je bestelling <b>#${besteld.nummer}</b> is binnen bij de zaak.</p>
            ${besteld.bezorgkosten ? `<p class="text-sm opacity-70">Bezorgkosten: ${euro(besteld.bezorgkosten)}</p>` : ''}
            <p class="b-kop text-xl mt-2">Totaal: ${euro(besteld.totaal)}</p>
            <p class="text-sm opacity-70 mt-3 max-w-xs mx-auto">${klantGeg.type === 'bezorgen' ? 'We gaan voor je aan de slag en komen eraan zodra hij klaar is!' : 'We gaan voor je aan de slag. Tot zo bij het afhalen!'}</p>
            <button type="button" class="b-knop mt-5" data-sluit-mand>Sluiten</button>
        </div>`;
}

async function plaatsBestelling() {
    const fout = (tekst) => { $('#cFout').textContent = tekst; };
    klantGeg.naam = $('#cNaam').value.trim();
    if (!klantGeg.naam) { $('#cNaam').classList.add('fout'); return fout('Vul even je naam in.'); }
    if (klantGeg.type === 'bezorgen' && !B.alleenAfhalen) {
        klantGeg.adres = $('#cAdres').value.trim();
        klantGeg.postcode = $('#cPostcode').value.trim();
        klantGeg.plaats = $('#cPlaats').value.trim();
        if (!klantGeg.adres) { $('#cAdres').classList.add('fout'); return fout('Vul je adres in.'); }
        if (!klantGeg.plaats) { $('#cPlaats').classList.add('fout'); return fout('Vul je plaats in.'); }
    }
    klantGeg.opmerking = $('#cOpmerking').value.trim();

    const knop = document.querySelector('[data-plaatsen]');
    knop.disabled = true;
    knop.textContent = 'Even geduld...';

    try {
        const res = await fetch(B.plaatsUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                klant: klantGeg.naam,
                type: klantGeg.type,
                adres: klantGeg.adres || null,
                postcode: klantGeg.postcode || null,
                plaats: klantGeg.plaats || null,
                opmerking: klantGeg.opmerking || null,
                items: mand.map((r) => ({ id: r.id, aantal: r.aantal, keuzes: r.keuzes || [], zonder: r.zonder || [] })),
            }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok) {
            renderMand();
            return fout(data.melding || 'Er ging iets mis, probeer het nog eens.');
        }
        besteld = data;
        mand = [];
        bewaar();
        renderSucces();
    } catch {
        renderMand();
        fout('Er ging iets mis, probeer het nog eens.');
    }
}

/* ── Events ───────────────────────────────────────────────────── */

document.addEventListener('click', (e) => {
    const item = e.target.closest('.menu-item');
    if (item) return toonItem(Number(item.dataset.item));
    if (e.target.closest('[data-sluit-item]')) return sluitItem();
    if (e.target.closest('[data-sluit-mand]')) return sluitMand();
    if (e.target.closest('#mandOpen')) return toonMand();

    if (openItem) {
        const keuze = e.target.closest('[data-g]');
        if (keuze) {
            const gi = Number(keuze.dataset.g);
            const ki = Number(keuze.dataset.k);
            const m = MENU.get(openItem.id);
            if (m.opties[gi].type === 'een') openItem.keuze[gi] = openItem.keuze[gi] === ki ? null : ki;
            else openItem.keuze[gi].has(ki) ? openItem.keuze[gi].delete(ki) : openItem.keuze[gi].add(ki);
            return renderItemSheet();
        }
        const zonder = e.target.closest('[data-zonder]');
        if (zonder) {
            const ing = zonder.dataset.zonder;
            openItem.zonder.has(ing) ? openItem.zonder.delete(ing) : openItem.zonder.add(ing);
            return renderItemSheet();
        }
        if (e.target.closest('[data-min]')) { openItem.aantal = Math.max(1, openItem.aantal - 1); return renderItemSheet(); }
        if (e.target.closest('[data-plus]')) { openItem.aantal = Math.min(10, openItem.aantal + 1); return renderItemSheet(); }
        if (e.target.closest('[data-toevoegen]')) {
            const m = MENU.get(openItem.id);
            const keuzes = [];
            (m.opties || []).forEach((g, gi) => {
                if (g.type === 'een') { if (openItem.keuze[gi] !== null) keuzes.push([gi, openItem.keuze[gi]]); }
                else openItem.keuze[gi].forEach((ki) => keuzes.push([gi, ki]));
            });
            const regel = { id: openItem.id, aantal: openItem.aantal, keuzes, zonder: [...openItem.zonder] };
            const handtekening = JSON.stringify([regel.id, regel.keuzes, regel.zonder]);
            const bestaand = mand.find((r) => JSON.stringify([r.id, r.keuzes, r.zonder]) === handtekening);
            if (bestaand) bestaand.aantal = Math.min(10, bestaand.aantal + regel.aantal);
            else mand.push(regel);
            bewaar();
            werkBalkBij();
            sluitItem();
            return;
        }
    }

    const rMin = e.target.closest('[data-r-min]');
    if (rMin) {
        const i = Number(rMin.dataset.rMin);
        mand[i].aantal -= 1;
        if (mand[i].aantal < 1) mand.splice(i, 1);
        bewaar(); werkBalkBij(); renderMand();
        return;
    }
    const rPlus = e.target.closest('[data-r-plus]');
    if (rPlus) {
        const i = Number(rPlus.dataset.rPlus);
        mand[i].aantal = Math.min(10, mand[i].aantal + 1);
        bewaar(); werkBalkBij(); renderMand();
        return;
    }
    const cType = e.target.closest('[data-c-type]');
    if (cType) { klantGeg.type = cType.dataset.cType; return renderMand(); }
    if (e.target.closest('[data-plaatsen]')) return plaatsBestelling();

    const zijType = e.target.closest('[data-zij-type]');
    if (zijType) {
        klantGeg.type = zijType.dataset.zijType;
        document.querySelectorAll('[data-zij-type]').forEach((el) => el.classList.toggle('aan', el === zijType));
        return;
    }
    if (e.target.closest('#zijAfrekenen')) return toonMand();
});

/* Live zoeken binnen de menukaart */
document.addEventListener('input', (e) => {
    if (e.target.id !== 'menuZoek') return;
    const zoek = e.target.value.trim().toLowerCase();
    document.querySelectorAll('.menu-item').forEach((el) => {
        el.classList.toggle('hidden', !!zoek && !el.textContent.toLowerCase().includes(zoek));
    });
    document.querySelectorAll('[data-cat-sectie]').forEach((sectie) => {
        const zichtbaar = [...sectie.querySelectorAll('.menu-item')].some((el) => !el.classList.contains('hidden'));
        sectie.classList.toggle('hidden', !zichtbaar);
    });
});

document.addEventListener('input', (e) => {
    e.target.classList.remove('fout');
    const koppel = { cNaam: 'naam', cAdres: 'adres', cPostcode: 'postcode', cPlaats: 'plaats', cOpmerking: 'opmerking' };
    if (koppel[e.target.id]) klantGeg[koppel[e.target.id]] = e.target.value;
});

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    if (openItem) sluitItem();
    else if (!$('#mandSheetVak').classList.contains('hidden')) sluitMand();
});

werkBalkBij();

/* Deep-link vanaf de homepage: ?gerecht=id opent direct de gerecht-sheet */
const gevraagd = Number(new URLSearchParams(location.search).get('gerecht'));
if (gevraagd && MENU.has(gevraagd)) toonItem(gevraagd);
