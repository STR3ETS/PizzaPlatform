@extends('site.layout')

@section('titel', 'Blog voor pizzeria-eigenaren | MijnPizzeria')
@section('omschrijving', 'Praktische artikelen over online bestellen zonder commissie, marketing en het runnen van je pizzeria. Geschreven voor pizzeria-eigenaren in Nederland.')

@section('inhoud')
<main class="max-w-6xl mx-auto px-5 pt-16 sm:pt-24 pb-24 sm:pb-32">

    {{-- Kop: links uitgelijnd --}}
    <div class="max-w-2xl">
        <p class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-crema-dark text-xs font-semibold uppercase tracking-wide text-cacao/60"><i class="fa-solid fa-newspaper" aria-hidden="true"></i> Blog</p>
        <h1 class="font-display text-4xl md:text-5xl mt-4">Slimmer je pizzeria runnen</h1>
        <p class="mt-3 text-lg font-bold text-cacao/60">Praktische artikelen over online bestellen zonder commissie, meer vaste klanten en de dagelijkse praktijk van je zaak. Geen wollige marketingpraat.</p>
    </div>

    @if($posts->isEmpty())
        <div class="mt-12 rounded-2xl border border-crema-dark bg-white p-10 max-w-xl">
            <p class="font-display text-2xl">De oven is nog aan het voorverwarmen <i class="fa-solid fa-fire text-tomato" aria-hidden="true"></i></p>
            <p class="mt-2 font-bold text-cacao/60">De eerste artikelen liggen in de maak. Kom snel terug, of start alvast met je eigen bestelpagina.</p>
            <a href="/onboarding" class="btn-primary inline-block mt-5">Gratis starten</a>
        </div>
    @else
        @php $uitgelicht = $posts->first(); $rest = $posts->slice(1); @endphp

        {{-- Uitgelicht: het nieuwste artikel groot en asymmetrisch --}}
        <a href="/blog/{{ $uitgelicht->slug }}" class="group mt-10 grid gap-0 lg:grid-cols-[1.2fr_1fr] rounded-2xl border border-crema-dark bg-white overflow-hidden hover:border-tomato/40 transition-colors">
            <div class="aspect-[16/9] lg:aspect-auto lg:min-h-[20rem] overflow-hidden bg-crema-dark">
                @if($uitgelicht->cover)
                    <img src="{{ $uitgelicht->cover }}" alt="" class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                @endif
            </div>
            <div class="p-7 lg:p-9 flex flex-col">
                <p class="text-xs font-semibold uppercase tracking-wide text-tomato">{{ $uitgelicht->cluster }}</p>
                <h2 class="font-display text-2xl lg:text-3xl mt-2 group-hover:text-tomato transition-colors">{{ $uitgelicht->title }}</h2>
                <p class="mt-3 font-bold text-cacao/60 line-clamp-3">{{ $uitgelicht->excerpt }}</p>
                <p class="mt-auto pt-5 text-sm font-semibold text-cacao/40">{{ $uitgelicht->published_at->translatedFormat('j F Y') }} &middot; {{ $uitgelicht->leestijd }} min leestijd</p>
            </div>
        </a>

        {{-- De rest in een grid --}}
        @if($rest->isNotEmpty())
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($rest as $post)
                    <a href="/blog/{{ $post->slug }}" class="group rounded-2xl border border-crema-dark bg-white overflow-hidden flex flex-col hover:border-tomato/40 transition-colors rise" style="--d:{{ 0.03 + ($loop->index % 6) * 0.03 }}s">
                        <div class="aspect-[16/9] overflow-hidden bg-crema-dark">
                            @if($post->cover)
                                <img src="{{ $post->cover }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                            @endif
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-tomato">{{ $post->cluster }}</p>
                            <h2 class="font-display text-xl mt-1.5 group-hover:text-tomato transition-colors">{{ $post->title }}</h2>
                            <p class="mt-2 text-sm font-bold text-cacao/60 line-clamp-3">{{ $post->excerpt }}</p>
                            <p class="mt-auto pt-4 text-xs font-semibold text-cacao/40">{{ $post->published_at->translatedFormat('j F Y') }} &middot; {{ $post->leestijd }} min leestijd</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    @endif
</main>
@endsection
