<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jouw eigen bestelpagina 🍕')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        /* "static" zodat ook kleuren die alleen in style.css gebruikt worden blijven bestaan */
        @theme static {
            --color-crema: #FFF6E8;
            --color-crema-dark: #F9EBD2;
            --color-tomato: #E63946;
            --color-tomato-dark: #B71F2E;
            --color-basil: #2F8F46;
            --color-basil-dark: #1F6A32;
            --color-gold: #F5B301;
            --color-cacao: #38221A;
            --font-display: "Lilita One", cursive;
            --font-body: "Nunito", sans-serif;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/onboarding/style.css') }}?v={{ filemtime(public_path('assets/onboarding/style.css')) }}">
</head>
<body class="bg-crema font-body text-cacao min-h-screen overflow-x-hidden antialiased">

    <!-- Zwevende deco-emoji's -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden select-none" aria-hidden="true">
        <span class="float-emoji" style="top:12%; left:6%;  --delay:0s;   --size:2.6rem;">🍕</span>
        <span class="float-emoji" style="top:70%; left:10%; --delay:1.2s; --size:2rem;">🍅</span>
        <span class="float-emoji" style="top:22%; left:88%; --delay:.6s;  --size:2.2rem;">🧀</span>
        <span class="float-emoji" style="top:78%; left:85%; --delay:2s;   --size:2.4rem;">🌿</span>
        <span class="float-emoji" style="top:45%; left:94%; --delay:2.8s; --size:1.8rem;">🔥</span>
        <span class="float-emoji" style="top:55%; left:3%;  --delay:1.8s; --size:1.8rem;">🫒</span>
    </div>

    <a href="{{ route('home') }}" class="fixed top-5 left-6 z-40 text-2xl" title="Naar de homepage">🍕</a>

    <main class="relative z-10 min-h-screen flex items-center justify-center px-5 py-14">
        <div class="w-full max-w-md">
            @yield('content')
        </div>
    </main>
</body>
</html>
