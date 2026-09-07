{{-- Gedeelde beheer-navigatie. Op de indexpagina onderschept JavaScript de
     kliks voor client-side wisselen; op detailpagina's zijn het gewone links. --}}
@php $actiefPaneel = $actiefPaneel ?? 'overzicht'; @endphp

<!-- Zwevende navigatie-rail (desktop) -->
<aside class="dash-rail">
    <div class="flex items-center gap-2.5 px-5 py-5">
        <span class="w-9 h-9 rounded-xl bg-tomato grid place-items-center text-white"><i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i></span>
        <span class="font-display text-xl">Beheer</span>
    </div>
    <nav class="flex-1 px-3 space-y-1 overflow-y-auto">
        @foreach(['overzicht' => ['fa-house', 'Overzicht'], 'pizzerias' => ['fa-store', "Pizzeria's"], 'financieel' => ['fa-euro-sign', 'Financieel'], 'sales' => ['fa-phone', 'Sales'], 'blog' => ['fa-pen-nib', 'Blog']] as $navPaneel => [$navIcoon, $navLabel])
            <a href="{{ $navPaneel === 'overzicht' ? '/admin' : '/admin/' . $navPaneel }}" data-nav="{{ $navPaneel }}" class="rail-item {{ $actiefPaneel === $navPaneel ? 'actief' : '' }}"><span class="r-ico"><i class="fa-solid {{ $navIcoon }}" aria-hidden="true"></i></span> {{ $navLabel }}</a>
        @endforeach
    </nav>
    <div class="px-3 pb-3">
        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('admin.scherm') }}" target="_blank" rel="noopener" class="rail-item !text-sm"><span class="r-ico"><i class="fa-solid fa-tv text-sm" aria-hidden="true"></i></span> Zaakscherm</a>
    </div>
    <div class="p-4 border-t border-crema-dark">
        <div class="flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-full bg-crema grid place-items-center font-display text-tomato shrink-0">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-cacao/45 hover:text-tomato transition-colors cursor-pointer">Uitloggen</button>
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- Tabbalk (mobiel) -->
<nav class="dash-tabbar">
    @foreach(['overzicht' => 'fa-house', 'pizzerias' => 'fa-store', 'financieel' => 'fa-euro-sign', 'sales' => 'fa-phone', 'blog' => 'fa-pen-nib'] as $navPaneel => $navIcoon)
        <a href="{{ $navPaneel === 'overzicht' ? '/admin' : '/admin/' . $navPaneel }}" data-nav="{{ $navPaneel }}" class="tab-item {{ $actiefPaneel === $navPaneel ? 'actief' : '' }}" aria-label="{{ ucfirst($navPaneel) }}"><span class="t-ico"><i class="fa-solid {{ $navIcoon }}" aria-hidden="true"></i></span></a>
    @endforeach
</nav>
