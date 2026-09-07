{{-- Blogbeheer: de content-machine schrijft, de AI-review-gate controleert, jij publiceert --}}
<section data-panel="blog" class="panel">
    <p class="font-display text-2xl mb-1">Blog</p>
    <p class="text-sm font-semibold text-cacao/50 mb-4">De machine schrijft artikelen voor mijnpizzeria.nl/blog, de AI-gate controleert op verzinsels en jij hebt het laatste woord.</p>

    {{-- Meldingen van de blog-acties --}}
    @if(session('blogSucces'))
        <div class="dash-card rise mb-4 !py-3" style="border-color:color-mix(in srgb, var(--color-basil) 55%, transparent)">
            <p class="text-sm font-semibold text-basil"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> {{ session('blogSucces') }}</p>
        </div>
    @endif
    @if(session('blogFout'))
        <div class="dash-card rise mb-4 !py-3" style="border-color:color-mix(in srgb, var(--color-tomato) 55%, transparent)">
            <p class="text-sm font-semibold text-tomato"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> {{ session('blogFout') }}</p>
        </div>
    @endif

    {{-- Statustegels --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div class="dash-card rise" style="--d:.03s">
            <p class="lbl !ml-0">Gepubliceerd</p>
            <p class="font-display text-4xl mt-1">{{ $blog['gepubliceerd']->count() }}</p>
            <p class="text-xs font-semibold text-cacao/40 mt-1">artikelen live op /blog</p>
        </div>
        <div class="dash-card rise" style="--d:.06s">
            <p class="lbl !ml-0">Wacht op review</p>
            <p class="font-display text-4xl mt-1">{{ $blog['concepten']->count() }}<span class="text-xl text-cacao/35">/{{ $blog['cap'] }}</span></p>
            <p class="text-xs font-semibold text-cacao/40 mt-1">{{ $blog['concepten']->count() >= $blog['cap'] ? 'wachtrij vol, eerst beoordelen' : 'ruimte voor nieuwe concepten' }}</p>
        </div>
        <div class="dash-card rise" style="--d:.09s">
            <p class="lbl !ml-0">Onderwerpen gepland</p>
            <p class="font-display text-4xl mt-1">{{ $blog['topics']->where('status', 'planned')->count() }}</p>
            <p class="text-xs font-semibold text-cacao/40 mt-1">klaar om geschreven te worden</p>
        </div>
        <div class="dash-card rise" style="--d:.12s">
            <p class="lbl !ml-0">AI-schrijver</p>
            <p class="font-display text-4xl mt-1">@if($blog['llmOk'])<i class="fa-solid fa-circle-check text-basil" aria-hidden="true"></i>@else<i class="fa-solid fa-circle-xmark text-tomato" aria-hidden="true"></i>@endif</p>
            <p class="text-xs font-semibold text-cacao/40 mt-1">{{ $blog['llmOk'] ? config('seo-content.llm.model') : 'SEO_LLM_KEY ontbreekt in .env' }}</p>
        </div>
    </div>

    {{-- Acties --}}
    <div class="dash-card rise mb-4" style="--d:.15s">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="min-w-0 flex-1">
                <p class="font-display text-xl"><i class="fa-solid fa-robot" aria-hidden="true"></i> De machine aan het werk zetten</p>
                <p class="text-sm font-semibold text-cacao/50">Schrijven duurt 2 tot 5 minuten per artikel: outline, secties, FAQ, cover en de review-gate draaien in één keer.</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <form method="POST" action="{{ route('blog.plan') }}">
                    @csrf
                    <button type="submit" class="btn-primary btn-grey !text-sm !px-4 !py-2.5 cursor-pointer">Onderwerpen aanvullen</button>
                </form>
                <form method="POST" action="{{ route('blog.schrijf') }}"
                      data-bevestig="De machine schrijft één volledig artikel: outline, secties, FAQ, cover en de review-gate. Dat duurt een paar minuten; laat het tabblad open."
                      data-bevestig-titel="Artikel laten schrijven?" data-bevestig-icoon="fa-pen"
                      data-bevestig-knop="Ja, ga schrijven" data-bevestig-bezig="De machine schrijft nu...">
                    @csrf
                    <button type="submit" class="btn-primary !text-sm !px-4 !py-2.5 cursor-pointer" {{ $blog['llmOk'] ? '' : 'disabled' }}><i class="fa-solid fa-pen" aria-hidden="true"></i> Schrijf volgend artikel</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Review-wachtrij: concepten langs de gate --}}
    <div class="dash-card rise mb-4" style="--d:.18s">
        <p class="font-display text-xl"><i class="fa-solid fa-list-check" aria-hidden="true"></i> Review-wachtrij</p>
        <p class="text-sm font-semibold text-cacao/50 mb-3">Elk concept is al door de AI-gate beoordeeld. Bekijk het voorbeeld, en publiceer of wijs af.</p>
        <div class="divide-y divide-crema-dark">
            @forelse($blog['concepten'] as $concept)
                @php
                    $verdict = $concept->ai_review['verdict'] ?? null;
                    $redenen = $concept->ai_review['reasons'] ?? [];
                    $issues = $concept->validator_metrics['issues'] ?? [];
                @endphp
                <div class="py-4">
                    <div class="flex flex-col lg:flex-row lg:items-start gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold">{{ $concept->title }}</p>
                            <p class="text-xs font-semibold text-cacao/45 mt-0.5">
                                {{ $concept->cluster }}
                                &middot; {{ $concept->validator_metrics['woorden'] ?? '?' }} woorden
                                &middot; geschreven {{ $concept->created_at->translatedFormat('j F') }}
                            </p>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                @if($verdict === 'pass')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-basil/10 text-basil"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Gate: goedgekeurd</span>
                                @elseif($verdict === 'fail')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-tomato/10 text-tomato"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Gate: geblokkeerd</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-crema text-cacao/50"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Gate: nog niet beoordeeld</span>
                                @endif
                                @if($issues)
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gold/15 text-cacao/70">{{ count($issues) }} validator-punt{{ count($issues) === 1 ? '' : 'en' }}</span>
                                @endif
                            </div>
                            @if($verdict === 'fail' && $redenen)
                                <ul class="mt-2 space-y-1">
                                    @foreach($redenen as $reden)
                                        <li class="text-xs font-semibold text-tomato/80 bg-tomato/5 rounded-lg px-2.5 py-1.5">{{ $reden }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if($issues)
                                <ul class="mt-2 space-y-1">
                                    @foreach($issues as $issue)
                                        <li class="text-xs font-semibold text-cacao/55 bg-crema rounded-lg px-2.5 py-1.5">{{ $issue }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2 shrink-0 lg:justify-end lg:max-w-[16rem]">
                            <a href="{{ route('blog.voorbeeld', $concept) }}" target="_blank" rel="noopener" class="btn-primary btn-grey !text-sm !px-4 !py-2"><i class="fa-solid fa-eye" aria-hidden="true"></i> Voorbeeld</a>
                            <form method="POST" action="{{ route('blog.hergate', $concept) }}"
                                  data-bevestig="De AI-gate leest het concept opnieuw en geeft een vers oordeel. Duurt ongeveer een minuut."
                                  data-bevestig-titel="Gate opnieuw draaien?" data-bevestig-icoon="fa-shield-halved"
                                  data-bevestig-knop="Ja, beoordeel opnieuw" data-bevestig-bezig="De gate leest mee...">
                                @csrf
                                <button type="submit" class="btn-primary btn-grey !text-sm !px-4 !py-2 cursor-pointer"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Gate opnieuw</button>
                            </form>
                            <form method="POST" action="{{ route('blog.publiceer', $concept) }}" class="flex items-center gap-2"
                                  data-bevestig="Het artikel komt direct live op mijnpizzeria.nl/blog/{{ $concept->slug }} en in de sitemap."
                                  data-bevestig-titel="Artikel publiceren?" data-bevestig-icoon="fa-rocket"
                                  data-bevestig-knop="Ja, publiceer">
                                @csrf
                                @if($verdict !== 'pass')
                                    <label class="flex items-center gap-1.5 text-xs font-semibold text-cacao/55 cursor-pointer select-none">
                                        <input type="checkbox" name="forceer" value="1" class="accent-[var(--color-tomato)]"> toch publiceren
                                    </label>
                                @endif
                                <button type="submit" class="btn-primary !text-sm !px-4 !py-2 cursor-pointer"><i class="fa-solid fa-rocket" aria-hidden="true"></i> Publiceer</button>
                            </form>
                            <form method="POST" action="{{ route('blog.afwijzen', $concept) }}"
                                  data-bevestig="Het concept wordt weggegooid. Het onderwerp komt terug in de wachtrij en wordt later opnieuw geschreven."
                                  data-bevestig-titel="Concept afwijzen?" data-bevestig-icoon="fa-trash-can"
                                  data-bevestig-knop="Ja, wijs af">
                                @csrf
                                <button type="submit" class="btn-primary btn-grey !text-sm !px-4 !py-2 cursor-pointer !text-tomato"><i class="fa-solid fa-trash-can" aria-hidden="true"></i> Afwijzen</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="py-3 text-sm font-semibold text-cacao/40">Geen concepten in de wachtrij. Laat de machine een artikel schrijven.</p>
            @endforelse
        </div>
    </div>

    {{-- Gepubliceerde artikelen --}}
    <div class="dash-card rise mb-4" style="--d:.21s">
        <p class="font-display text-xl"><i class="fa-solid fa-book-open" aria-hidden="true"></i> Live op de blog</p>
        <p class="text-sm font-semibold text-cacao/50 mb-3">Deze artikelen staan op <a href="/blog" target="_blank" rel="noopener" class="text-tomato hover:underline underline-offset-2">mijnpizzeria.nl/blog</a> en in de sitemap.</p>
        <div class="divide-y divide-crema-dark">
            @forelse($blog['gepubliceerd'] as $artikel)
                <div class="py-3 flex flex-col md:flex-row md:items-center gap-3">
                    <div class="min-w-0 flex-1">
                        <a href="/blog/{{ $artikel->slug }}" target="_blank" rel="noopener" class="font-semibold hover:underline underline-offset-2">{{ $artikel->title }}</a>
                        <p class="text-xs font-semibold text-cacao/45 mt-0.5">
                            {{ $artikel->cluster }}
                            &middot; {{ $artikel->leestijd }} min leestijd
                            &middot; gepubliceerd {{ $artikel->published_at->translatedFormat('j F Y') }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('blog.offline', $artikel) }}" class="shrink-0"
                          data-bevestig="Het artikel verdwijnt van de blog en komt terug in de review-wachtrij."
                          data-bevestig-titel="Offline halen?" data-bevestig-icoon="fa-power-off"
                          data-bevestig-knop="Ja, haal offline">
                        @csrf
                        <button type="submit" class="btn-primary btn-grey !text-sm !px-4 !py-2 cursor-pointer">Offline halen</button>
                    </form>
                </div>
            @empty
                <p class="py-3 text-sm font-semibold text-cacao/40">Nog niets gepubliceerd. Het eerste artikel wacht op jouw akkoord.</p>
            @endforelse
        </div>
    </div>

    {{-- Onderwerpen-wachtrij --}}
    <div class="dash-card rise" style="--d:.24s">
        <p class="font-display text-xl"><i class="fa-solid fa-lightbulb" aria-hidden="true"></i> Onderwerpen</p>
        <p class="text-sm font-semibold text-cacao/50 mb-3">De machine pakt telkens het geplande onderwerp met de hoogste prioriteit (1 = eerst).</p>

        <form method="POST" action="{{ route('blog.topic') }}" class="flex flex-col md:flex-row gap-2 mb-4">
            @csrf
            <input type="text" name="topic" required maxlength="200" placeholder="Nieuw onderwerp, bijv. 'Openingstijden slim instellen'" class="flex-1 rounded-xl border border-crema-dark bg-white px-3.5 py-2.5 text-sm font-bold placeholder:text-cacao/35 focus:outline-none focus:border-tomato">
            <select name="cluster" class="rounded-xl border border-crema-dark bg-white px-3 py-2.5 text-sm font-semibold text-cacao/70 focus:outline-none focus:border-tomato">
                @foreach($blog['clusters'] as $cluster)
                    <option value="{{ $cluster }}">{{ $cluster }}</option>
                @endforeach
            </select>
            <select name="priority" class="rounded-xl border border-crema-dark bg-white px-3 py-2.5 text-sm font-semibold text-cacao/70 focus:outline-none focus:border-tomato">
                @foreach(range(1, 9) as $prio)
                    <option value="{{ $prio }}" {{ $prio === 5 ? 'selected' : '' }}>Prioriteit {{ $prio }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary !text-sm !px-4 !py-2.5 cursor-pointer">Toevoegen</button>
        </form>

        <div class="divide-y divide-crema-dark">
            @forelse($blog['topics'] as $topic)
                <div class="py-2.5 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-crema grid place-items-center text-xs font-semibold text-cacao/55 shrink-0" title="Prioriteit">{{ $topic->priority }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold truncate">{{ $topic->topic }}</p>
                        <p class="text-[11px] font-semibold text-cacao/40">{{ $topic->cluster }}</p>
                    </div>
                    @if($topic->status === 'planned')
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-crema text-cacao/50 shrink-0">gepland</span>
                    @elseif($topic->status === 'drafting')
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gold/15 text-cacao/70 shrink-0">wordt geschreven...</span>
                    @elseif($topic->status === 'drafted')
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gold/15 text-cacao/70 shrink-0">wacht op review</span>
                    @else
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-basil/10 text-basil shrink-0">gepubliceerd</span>
                    @endif
                    @if($topic->status === 'planned')
                        <form method="POST" action="{{ route('blog.topic.weg', $topic) }}" class="shrink-0"
                              data-bevestig="&quot;{{ \Illuminate\Support\Str::limit($topic->topic, 60) }}&quot; verdwijnt uit de wachtrij."
                              data-bevestig-titel="Onderwerp verwijderen?" data-bevestig-icoon="fa-trash-can"
                              data-bevestig-knop="Ja, verwijder">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg grid place-items-center text-cacao/35 hover:text-tomato hover:bg-tomato/5 transition-colors cursor-pointer" aria-label="Verwijderen"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="py-3 text-sm font-semibold text-cacao/40">Geen onderwerpen. Klik op "Onderwerpen aanvullen" voor de startlijst uit de brand-kit.</p>
            @endforelse
        </div>
    </div>
</section>
