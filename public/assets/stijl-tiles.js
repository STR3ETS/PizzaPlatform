/* Mini-voorbeelden (16:9) van de vier templates in het gekozen palet.
   Gedeeld door de onboarding en de stijl-popup in het dashboard. */
(function () {
    function palet(hex) {
        const bekend = (window.PP_KLEUREN || []).find((k) => k.hex.toLowerCase() === String(hex || '').toLowerCase());
        if (bekend) return bekend.palet;
        return { primair: hex || '#E63946', primairDonker: '#8F1D26', secundair: '#3E2716', secundairLicht: '#8A7563', witWarm: '#FFF7F0' };
    }

    window.PP_TILES = {
        template1: (c) => {
            const pal = palet(c);
            const kaartje = `<span class="flex-1 min-w-0 flex items-center gap-1 rounded-[5px] bg-white px-1.5 py-1" style="border:1px solid rgba(0,0,0,.08)">
                <span class="flex-1 min-w-0 flex flex-col gap-[3px]">
                    <span class="h-[4px] w-[75%] rounded-full" style="background:${pal.secundair}"></span>
                    <span class="h-[3px] w-[45%] rounded-full" style="background:rgba(0,0,0,.25)"></span>
                </span>
                <span class="w-2.5 h-2.5 shrink-0 rounded-full grid place-items-center text-[7px] font-bold" style="border:1px solid rgba(0,0,0,.14); color:${pal.primair}">+</span>
            </span>`;
            return `<span class="flex w-full h-full overflow-hidden rounded-[10px] text-left" style="background:${pal.witWarm}">
                <span class="flex-1 min-w-0 flex flex-col">
                    <span class="relative h-[40%] shrink-0" style="background:url('/assets/eten/pepperoni.jpg') center/cover">
                        <span class="absolute left-1.5 -bottom-1.5 w-4 h-4 rounded-[5px] bg-white" style="box-shadow:0 1px 3px rgba(0,0,0,.3)"></span>
                    </span>
                    <span class="flex flex-col gap-[4px] px-1.5 pt-2.5 pb-1.5 flex-1">
                        <span class="h-[5px] w-14 rounded-full" style="background:${pal.secundair}"></span>
                        <span class="h-[4px] w-9 rounded-full" style="background:${pal.primair}"></span>
                        <span class="h-[8px] w-full rounded-full bg-white" style="border:1px solid rgba(0,0,0,.08)"></span>
                        <span class="flex gap-[4px] flex-1 items-stretch">${kaartje}${kaartje}</span>
                    </span>
                </span>
                <span class="w-[26%] shrink-0 flex flex-col gap-[4px] p-1.5" style="background:${pal.secundair}">
                    <span class="h-[7px] rounded-full" style="background:${pal.primair}"></span>
                    <span class="h-[4px] rounded-full" style="background:rgba(255,255,255,.2)"></span>
                    <span class="h-[4px] w-[70%] rounded-full" style="background:rgba(255,255,255,.2)"></span>
                    <span class="mt-auto h-[8px] rounded-full" style="background:${pal.primair}"></span>
                </span>
            </span>`;
        },
        template2: (c) => {
            const pal = palet(c);
            const regel = `<span class="flex items-baseline gap-[4px]">
                <span class="h-[4px] w-9 rounded-full" style="background:${pal.secundair}"></span>
                <span class="flex-1 border-b border-dotted" style="border-color:rgba(0,0,0,.25)"></span>
                <span class="h-[4px] w-4 rounded-full" style="background:${pal.secundair}"></span>
            </span>`;
            return `<span class="flex flex-col w-full h-full overflow-hidden rounded-[10px] p-[7px] text-left" style="background:${pal.witWarm}">
                <span class="h-[7px] w-16 rounded-full mb-[5px]" style="background:${pal.secundair}"></span>
                <span class="flex items-baseline gap-[3px] mb-[5px]">
                    <span class="h-[4px] w-3 rounded-full" style="background:${pal.primair}"></span>
                    <span class="h-[4px] w-10 rounded-full" style="background:rgba(0,0,0,.2)"></span>
                </span>
                <span class="flex flex-col gap-[5px] flex-1">${regel}${regel}${regel}</span>
                <span class="h-[8px] rounded-[3px] mt-[4px] flex items-center px-[4px]" style="background:${pal.secundair}">
                    <span class="h-[3px] w-8 rounded-full" style="background:${pal.primair}"></span>
                </span>
            </span>`;
        },
        template3: (c) => {
            const pal = palet(c);
            const kaart = `<span class="flex-1 flex flex-col bg-white" style="border:1px solid rgba(0,0,0,.12)">
                <span class="h-[45%]" style="background:url('/assets/eten/pepperoni.jpg') center/cover"></span>
                <span class="flex-1 flex flex-col justify-center gap-[2px] px-1">
                    <span class="h-[4px] w-[80%]" style="background:${pal.secundair}"></span>
                    <span class="h-[3px] w-[45%]" style="background:${pal.primair}"></span>
                </span>
            </span>`;
            return `<span class="flex flex-col w-full h-full overflow-hidden rounded-[10px] text-left" style="background:${pal.witWarm}">
                <span class="relative h-[42%] shrink-0" style="background:url('/assets/eten/oven.jpg') center/cover">
                    <span class="absolute inset-0" style="background:linear-gradient(to top, rgba(0,0,0,.55), transparent)"></span>
                    <span class="absolute left-[6px] bottom-[4px] h-[7px] w-16 bg-white"></span>
                </span>
                <span class="flex gap-[6px] flex-1 p-[6px]">${kaart}${kaart}</span>
            </span>`;
        },
        template4: (c) => {
            const pal = palet(c);
            const kaart = `<span class="shrink-0 w-[38%] flex flex-col gap-[3px]">
                <span class="h-[55%] rounded-[6px]" style="background:url('/assets/eten/pepperoni.jpg') center/cover"></span>
                <span class="h-[4px] w-[85%] rounded-full" style="background:${pal.secundair}"></span>
                <span class="flex items-center justify-between">
                    <span class="h-[4px] w-6 rounded-full" style="background:rgba(0,0,0,.25)"></span>
                    <span class="w-2.5 h-2.5 rounded-full" style="background:${pal.primair}"></span>
                </span>
            </span>`;
            return `<span class="flex flex-col w-full h-full overflow-hidden rounded-[10px] bg-white p-[6px] gap-[5px] text-left">
                <span class="flex items-center gap-[4px]">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:${pal.witWarm}; border:1px solid rgba(0,0,0,.1)"></span>
                    <span class="h-[4px] w-8 rounded-full" style="background:${pal.secundair}"></span>
                    <span class="ml-auto h-[8px] w-8 rounded-full" style="background:${pal.primair}"></span>
                </span>
                <span class="rounded-[6px] p-[5px]" style="background:${pal.witWarm}">
                    <span class="block h-[5px] w-12 rounded-full" style="background:${pal.secundair}"></span>
                    <span class="mt-[3px] block h-[6px] w-full rounded-full bg-white"></span>
                </span>
                <span class="flex gap-[5px] flex-1 overflow-hidden">${kaart}${kaart}${kaart}</span>
            </span>`;
        },
    };
})();
