{{-- Gastlayout: inloggen, wachtwoord vergeten, wachtwoord herstellen.
     Eén vlak op het warme vel: links het formulier, rechts een foto van eten
     met een waas in de hoofdkleur. Op een telefoon valt de foto weg. --}}
<!DOCTYPE html>
<html lang="nl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shop & Eat')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/stijl/systeem.css') }}?v={{ filemtime(public_path('assets/stijl/systeem.css')) }}">
    <style>
        .gast { min-height: 100dvh; display: grid; place-items: center; padding: var(--gap); }
        .gast-vlak { width: min(100%, 1040px); padding: 0; overflow: hidden; display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
        .gast-form { padding: clamp(28px, 5vw, 56px); display: flex; flex-direction: column; gap: 22px; }
        .gast-form .merk { align-self: flex-start; }
        .gast-kop { font-size: clamp(30px, 3.4vw, 40px); font-weight: 300; letter-spacing: -.025em; line-height: 1.08; margin-top: 6px; }
        .gast-velden { display: flex; flex-direction: column; gap: 18px; }
        .gast-rij { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .vink { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--ink2); cursor: pointer; }
        .vink input { width: 16px; height: 16px; accent-color: var(--accent); margin: 0; }

        /* De foto: onderin dicht in de hoofdkleur zodat de regel erop leesbaar is, bovenin schemert het eten door */
        .gast-foto {
            position: relative;
            min-height: 100%;
            padding: clamp(24px, 3vw, 40px);
            display: flex; flex-direction: column; justify-content: flex-end; gap: 6px;
            color: #fff;
            background-image: linear-gradient(180deg, color-mix(in srgb, var(--accent) 12%, transparent) 0%, color-mix(in srgb, var(--accent) 82%, transparent) 100%), var(--gast-foto);
            background-size: cover;
            background-position: center;
        }
        .gast-foto .claim { font-size: clamp(22px, 2.4vw, 32px); font-weight: 300; letter-spacing: -.02em; line-height: 1.12; max-width: 16ch; }
        .gast-foto .claim b { font-weight: 500; }
        .gast-foto .sub { font-size: 13px; color: rgba(255, 255, 255, .8); max-width: 34ch; }
        @media (max-width: 56rem) {
            .gast-vlak { grid-template-columns: minmax(0, 1fr); }
            .gast-foto { display: none; }
        }
    </style>
</head>
<body>

    <main class="gast">
        <div class="vlak gast-vlak">
            <section class="gast-form">
                <a href="{{ route('home') }}" class="merk" title="Naar de site"><i aria-hidden="true"></i>Shop &amp; Eat</a>
                @yield('content')
            </section>
            <aside class="gast-foto" style="--gast-foto:url('{{ asset('assets/eten/' . trim($__env->yieldContent('foto', 'margherita.jpg'))) }}')" aria-hidden="true">
                {{-- Blokvorm, want een inline @section wordt geëscapet en dan staat de <b> er letterlijk --}}
                <p class="claim">@hasSection('claim')@yield('claim')@else Bestellingen zonder commissie, <b>rechtstreeks bij jou</b>.@endif</p>
                <p class="sub">@yield('claimsub', 'Je eigen bestelpagina, je eigen klanten, je eigen omzet.')</p>
            </aside>
        </div>
    </main>
</body>
</html>
