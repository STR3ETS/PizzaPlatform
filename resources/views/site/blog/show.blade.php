@extends('site.layout')

@section('titel', ($post->meta_title ?: $post->title) . ' | MijnPizzeria')
@section('omschrijving', $post->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $post->excerpt), 155))

@section('meta')
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->og_title ?: $post->title }}">
    <meta property="og:description" content="{{ $post->og_description ?: $post->meta_description }}">
    @if($post->cover)
        <meta property="og:image" content="{{ rtrim(config('seo-content.factsheet.site'), '/') . $post->cover }}">
    @endif
    @if($preview)
        <meta name="robots" content="noindex, nofollow">
    @else
        <link rel="canonical" href="{{ rtrim(config('seo-content.factsheet.site'), '/') . '/blog/' . $post->slug }}">
    @endif
    <style>
        /* Artikel-opmaak: de content-machine levert kale HTML, dit maakt er huisstijl van */
        .artikel h2 { font-family: var(--font-display); font-size: 1.7rem; line-height: 1.2; margin: 2.4rem 0 .9rem; }
        .artikel h3 { font-weight: 700; font-size: 1.15rem; margin: 1.7rem 0 .5rem; }
        .artikel p { margin: 0 0 1.05rem; line-height: 1.8; font-weight: 600; color: color-mix(in srgb, var(--color-cacao) 86%, transparent); }
        .artikel ul { margin: 0 0 1.05rem 1.3rem; list-style: disc; }
        .artikel li { margin-bottom: .45rem; line-height: 1.7; font-weight: 600; color: color-mix(in srgb, var(--color-cacao) 86%, transparent); }
        .artikel li::marker { color: var(--color-tomato); }
        .artikel a { color: var(--color-tomato); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
        .artikel a:hover { color: var(--color-tomato-dark); }
        .artikel .artikel-cta { margin-top: 2.8rem; padding: 2rem 1.8rem; border-radius: 1rem; background: var(--color-tomato); box-shadow: 0 1px 2px rgb(36 23 18 / .1); }
        .artikel .artikel-cta h2 { color: #fff; margin: 0 0 .5rem; }
        .artikel .artikel-cta p { color: rgba(255, 255, 255, .88); margin-bottom: .9rem; }
        .artikel .artikel-cta a { display: inline-block; background: #fff; color: var(--color-tomato); border-radius: 9999px; padding: .75rem 1.7rem; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgb(36 23 18 / .1); }
        .artikel .artikel-cta a:hover { transform: translateY(-1px); color: var(--color-tomato-dark); }
    </style>
@endsection

@section('inhoud')
@if($preview)
    {{-- Beheer-balk: alleen zichtbaar in het voorbeeld vanuit het blogbeheer --}}
    <div class="sticky top-[6rem] z-30 bg-cacao text-white px-5 py-3">
        <div class="max-w-3xl mx-auto flex flex-wrap items-center gap-3">
            <p class="text-sm font-semibold"><i class="fa-solid fa-eye" aria-hidden="true"></i> Voorbeeld: dit concept is nog niet gepubliceerd.</p>
            @php $verdict = $post->ai_review['verdict'] ?? null; @endphp
            @if($verdict === 'pass')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-basil/30 text-white"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Gate: goedgekeurd</span>
            @elseif($verdict === 'fail')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-tomato text-white"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Gate: geblokkeerd</span>
            @endif
            <a href="/admin/blog" class="ml-auto text-sm font-semibold underline underline-offset-2 hover:text-gold">Terug naar blogbeheer</a>
        </div>
    </div>
@endif

<main class="max-w-3xl mx-auto px-5 pt-10 pb-20">

    {{-- Kruimelpad --}}
    <nav class="text-xs font-semibold text-cacao/40" aria-label="Kruimelpad">
        <a href="/" class="hover:text-cacao transition-colors">Home</a>
        <span class="mx-1.5">/</span>
        <a href="/blog" class="hover:text-cacao transition-colors">Blog</a>
        <span class="mx-1.5">/</span>
        <span class="text-cacao/60">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</span>
    </nav>

    {{-- Artikelkop --}}
    <header class="mt-6">
        <p class="text-xs font-semibold uppercase tracking-wide text-tomato">{{ $post->cluster }}</p>
        <h1 class="font-display text-3xl md:text-[2.6rem] leading-[1.15] mt-2.5">{{ $post->title }}</h1>
        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-sm font-semibold text-cacao/45">
            <span class="flex items-center gap-1.5"><i class="fa-regular fa-calendar" aria-hidden="true"></i> {{ ($post->published_at ?? $post->created_at)->translatedFormat('j F Y') }}</span>
            <span class="flex items-center gap-1.5"><i class="fa-regular fa-clock" aria-hidden="true"></i> {{ $post->leestijd }} min leestijd</span>
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-pizza-slice" aria-hidden="true"></i> {{ config('seo-content.author.naam') }}</span>
        </div>
    </header>

    {{-- Cover --}}
    @if($post->cover)
        <figure class="mt-7">
            <div class="rounded-2xl border border-crema-dark overflow-hidden bg-crema-dark">
                <img src="{{ $post->cover }}" alt="{{ $post->title }}" class="w-full aspect-[16/9] object-cover">
            </div>
            @if(!empty($post->briefing['cover_credit']))
                <figcaption class="mt-2 text-xs font-bold text-cacao/35">{{ $post->briefing['cover_credit'] }}</figcaption>
            @endif
        </figure>
    @endif

    {{-- De inhoud zelf, geschreven door de machine en langs de gate --}}
    <article class="artikel mt-8">
        {!! $post->content !!}
    </article>

    {{-- Verwante artikelen --}}
    @if($verwant->isNotEmpty())
        <section class="mt-14">
            <h2 class="font-display text-2xl">Meer uit {{ $post->cluster }}</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-3">
                @foreach($verwant as $ander)
                    <a href="/blog/{{ $ander->slug }}" class="group rounded-2xl border border-crema-dark bg-white overflow-hidden flex flex-col hover:border-tomato/40 transition-colors">
                        <div class="aspect-[16/9] overflow-hidden bg-crema-dark">
                            @if($ander->cover)
                                <img src="{{ $ander->cover }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="font-display text-base leading-snug group-hover:text-tomato transition-colors">{{ $ander->title }}</p>
                            <p class="mt-1.5 text-xs font-semibold text-cacao/40">{{ $ander->leestijd }} min leestijd</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</main>

{{-- JSON-LD structured data (alleen op de echte pagina, niet in het voorbeeld) --}}
@if(!$preview && $post->schema_json)
    <script type="application/ld+json">{!! json_encode($post->schema_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
@endsection
