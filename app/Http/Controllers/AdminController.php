<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private const THEMA_NAMEN = ['template1' => 'Presto', 'template2' => 'Notte', 'template3' => 'Forza', 'template4' => 'Giro'];

    private const STATUS_LABELS = [
        'nieuw' => 'Nieuw', 'geaccepteerd' => 'Bevestigd', 'bereiden' => 'Wordt bereid',
        'oven' => 'Wordt bereid', 'onderweg' => 'Onderweg', 'bezorgd' => 'Bezorgd',
    ];

    private function eis(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }

    /** Naam van de gekozen kleurstijl, uit dezelfde bron als de templates */
    private function kleurNaam(?string $hex): ?string
    {
        foreach (BestelController::KLEUREN as $kleur) {
            if (strcasecmp($kleur['hex'], (string) $hex) === 0) {
                return $kleur['naam'];
            }
        }

        return null;
    }

    public function index(Request $request)
    {
        $this->eis($request);

        $orderStats = Order::where('is_demo', false)
            ->selectRaw('user_id, COUNT(*) as aantal, SUM(totaal) as omzet, MAX(created_at) as laatste')
            ->groupBy('user_id')->get()->keyBy('user_id');
        $menuTellingen = MenuItem::selectRaw('user_id, COUNT(*) as aantal')
            ->groupBy('user_id')->pluck('aantal', 'user_id');
        $besteldRecent = Order::where('is_demo', false)->where('created_at', '>=', now()->subDays(14))
            ->distinct()->pluck('user_id')->flip();

        // Sorteren doen we in PHP: de onboarding-JSON (logo's, foto's) is zo groot
        // dat MySQL's sorteerbuffer stukloopt op ORDER BY over volledige rijen
        $pizzerias = User::where('is_admin', false)->get()->sortByDesc('created_at')->values()->map(function ($u) use ($orderStats, $menuTellingen, $besteldRecent) {
            $ob = $u->onboarding ?? [];
            $stats = $orderStats[$u->id] ?? null;

            return [
                'id' => $u->id,
                'naam' => $ob['name'] ?? $u->name,
                'persoon' => $u->name,
                'email' => $u->email,
                'telefoon' => ($ob['phone'] ?? '') !== '' ? $ob['phone'] : null,
                'plaats' => $ob['city'] ?? null,
                'logo' => $ob['logo'] ?? null,
                'slug' => $u->slug,
                'online' => (bool) $u->is_online,
                'abonnement' => $u->abonnement_actief ? 'actief' : ($u->inProefperiode() ? 'proef' : 'verlopen'),
                'proef_tot' => $u->proef_tot,
                'setup' => (bool) ($ob['setup_compleet'] ?? false),
                'recent14' => isset($besteldRecent[$u->id]),
                'notitie' => $u->beheer_notitie,
                'opvolgen' => $u->opvolgen_vanaf,
                'template' => self::THEMA_NAMEN[$ob['theme'] ?? ''] ?? 'Presto',
                'gerechten' => (int) ($menuTellingen[$u->id] ?? 0),
                'bestellingen' => (int) ($stats->aantal ?? 0),
                'omzet' => (int) ($stats->omzet ?? 0),
                'laatste' => $stats?->laatste,
                'aangemeld' => $u->created_at,
            ];
        });

        $recent = Order::where('is_demo', false)->with('user')->latest()->take(12)->get();

        // Financieel: abonnementsinkomsten, conversie en de platformomzet
        $actief = $pizzerias->where('abonnement', 'actief');
        $verlopen = $pizzerias->where('abonnement', 'verlopen');
        $beslist = $actief->count() + $verlopen->count();
        $omzet30 = (int) Order::where('is_demo', false)->where('created_at', '>=', now()->subDays(30))->sum('totaal');

        // Omzet per week (laatste 8 weken) voor het staafdiagram
        $weekStart = now()->startOfWeek()->subWeeks(7);
        $perWeek = Order::where('is_demo', false)->where('created_at', '>=', $weekStart)
            ->get(['created_at', 'totaal'])
            ->groupBy(fn ($o) => $o->created_at->format('oW'));
        $weekOmzet = collect(range(0, 7))->map(function ($i) use ($weekStart, $perWeek) {
            $week = $weekStart->copy()->addWeeks($i);

            return [
                'label' => 'wk ' . $week->isoWeek(),
                'bedrag' => (int) ($perWeek[$week->format('oW')] ?? collect())->sum('totaal'),
            ];
        });

        $financieel = [
            'mrr' => $actief->count() * 2495,
            'jaar' => $actief->count() * 2495 * 12,
            'actief' => $actief->count(),
            'proef' => $pizzerias->where('abonnement', 'proef')->count(),
            'verlopen' => $verlopen->count(),
            'conversie' => $beslist > 0 ? (int) round($actief->count() / $beslist * 100) : null,
            'activatie' => $pizzerias->count() > 0 ? (int) round($pizzerias->where('setup', true)->count() / $pizzerias->count() * 100) : null,
            'gmv' => (int) $orderStats->sum('omzet'),
            'gmv30' => $omzet30,
            'gemBestelling' => $orderStats->sum('aantal') > 0 ? (int) round($orderStats->sum('omzet') / $orderStats->sum('aantal')) : 0,
            'weekOmzet' => $weekOmzet,
        ];

        // Sales: wie loopt bijna uit de proef, wie is afgehaakt en welke abonnee
        // is stilgevallen. Gesnoozde zaken (opvolgdatum in de toekomst) blijven
        // uit de lijsten tot hun datum.
        $nietGesnoozed = fn ($p) => ! $p['opvolgen'] || $p['opvolgen']->isPast();
        $bijnaAf = $pizzerias->where('abonnement', 'proef')
            ->filter(fn ($p) => $p['proef_tot'] && $p['proef_tot']->isBefore(now()->addDays(7)))
            ->filter($nietGesnoozed)
            ->sortBy(fn ($p) => $p['proef_tot'])->values();
        $afgehaakt = $verlopen->filter($nietGesnoozed)
            ->sortByDesc(fn ($p) => $p['proef_tot'] ?? now()->subYear())->values();
        $stil = $actief->filter(fn ($p) => ! $p['recent14'])->filter($nietGesnoozed)->values();

        // Activatie-funnel en aanmeldtrend: waar verliezen we mensen
        $funnel = [
            ['Aangemeld', $pizzerias->count()],
            ['Zaak compleet', $pizzerias->where('setup', true)->count()],
            ['Eerste bestelling', $pizzerias->where('bestellingen', '>', 0)->count()],
            ['Abonnement', $actief->count()],
        ];
        $aanmeldWeek = collect(range(0, 7))->map(function ($i) use ($weekStart, $pizzerias) {
            $week = $weekStart->copy()->addWeeks($i);

            return [
                'label' => 'wk ' . $week->isoWeek(),
                'aantal' => $pizzerias->filter(fn ($p) => $p['aangemeld']->format('oW') === $week->format('oW'))->count(),
            ];
        });

        return view('admin.index', [
            'pizzerias' => $pizzerias,
            'recent' => $recent,
            'statusLabels' => self::STATUS_LABELS,
            'financieel' => $financieel,
            'bijnaAf' => $bijnaAf,
            'afgehaakt' => $afgehaakt,
            'stil' => $stil,
            'funnel' => $funnel,
            'aanmeldWeek' => $aanmeldWeek,
            'feedbackLijst' => \App\Models\Feedback::with('user')->latest()->take(50)->get(),
            'blog' => [
                'topics' => \App\Models\ContentTopic::orderByRaw("field(status, 'drafting', 'planned', 'drafted', 'published')")->orderBy('priority')->orderBy('id')->get(),
                'concepten' => \App\Models\Post::where('generation_status', 'needs_review')->orderBy('created_at')->get(),
                'gepubliceerd' => \App\Models\Post::published()->orderByDesc('published_at')->get(),
                'cap' => (int) config('seo-content.review_queue_cap'),
                'clusters' => array_keys(config('seo-content.clusters')),
                'llmOk' => app(\App\Services\Blog\LlmClient::class)->isGeconfigureerd(),
            ],
            'totalen' => [
                'pizzerias' => $pizzerias->count(),
                'bestellingen' => (int) $orderStats->sum('aantal'),
                'omzet' => (int) $orderStats->sum('omzet'),
                'vandaag' => Order::where('is_demo', false)->whereDate('created_at', today())->count(),
                'online' => $pizzerias->where('online', true)->count(),
            ],
        ]);
    }

    /**
     * Het zaakscherm: fullscreen wallboard voor aan de muur bij beheer en sales.
     * Toegang via een ondertekende link (geen login, dus geen verlopende sessie);
     * de pagina ververst zichzelf en heeft nooit scroll of kliks nodig.
     */
    public function scherm(Request $request)
    {
        $orderStats = Order::where('is_demo', false)
            ->selectRaw('user_id, COUNT(*) as aantal, SUM(totaal) as omzet, MAX(created_at) as laatste')
            ->groupBy('user_id')->get()->keyBy('user_id');
        $besteldRecent = Order::where('is_demo', false)->where('created_at', '>=', now()->subDays(14))
            ->distinct()->pluck('user_id')->flip();

        $zaken = User::where('is_admin', false)->get()->map(function ($u) use ($orderStats, $besteldRecent) {
            $ob = $u->onboarding ?? [];

            return [
                'naam' => $ob['name'] ?? $u->name,
                'plaats' => $ob['city'] ?? null,
                'telefoon' => ($ob['phone'] ?? '') !== '' ? $ob['phone'] : null,
                'online' => (bool) $u->is_online,
                'abonnement' => $u->abonnement_actief ? 'actief' : ($u->inProefperiode() ? 'proef' : 'verlopen'),
                'proef_tot' => $u->proef_tot,
                'recent14' => isset($besteldRecent[$u->id]),
                'laatste' => ($orderStats[$u->id] ?? null)?->laatste,
            ];
        });

        $actief = $zaken->where('abonnement', 'actief');
        $weekStart = now()->startOfWeek()->subWeeks(7);
        $perWeek = Order::where('is_demo', false)->where('created_at', '>=', $weekStart)
            ->get(['created_at', 'totaal'])->groupBy(fn ($o) => $o->created_at->format('oW'));

        return view('admin.scherm', [
            'cijfers' => [
                'zaken' => $zaken->count(),
                'abonnementen' => $actief->count(),
                'proef' => $zaken->where('abonnement', 'proef')->count(),
                'mrr' => $actief->count() * 2495,
                'vandaag' => Order::where('is_demo', false)->whereDate('created_at', today())->count(),
                'omzetVandaag' => (int) Order::where('is_demo', false)->whereDate('created_at', today())->sum('totaal'),
                'online' => $zaken->where('online', true)->count(),
            ],
            'bijnaAf' => $zaken->where('abonnement', 'proef')
                ->filter(fn ($z) => $z['proef_tot'] && $z['proef_tot']->isBefore(now()->addDays(7)))
                ->sortBy(fn ($z) => $z['proef_tot'])->take(5)->values(),
            'afgehaakt' => $zaken->where('abonnement', 'verlopen')
                ->sortByDesc(fn ($z) => $z['proef_tot'] ?? now()->subYear())->take(4)->values(),
            'stil' => $actief->filter(fn ($z) => ! $z['recent14'])->take(4)->values(),
            'feedback' => \App\Models\Feedback::with('user')->latest()->take(4)->get(),
            'recent' => Order::where('is_demo', false)->with('user')->latest()->take(6)->get(),
            'weekOmzet' => collect(range(0, 7))->map(fn ($i) => [
                'label' => 'wk ' . $weekStart->copy()->addWeeks($i)->isoWeek(),
                'bedrag' => (int) ($perWeek[$weekStart->copy()->addWeeks($i)->format('oW')] ?? collect())->sum('totaal'),
            ]),
        ]);
    }

    /** Beheernotitie en opvolgdatum opslaan (mini-CRM) */
    public function notitie(Request $request, User $user)
    {
        $this->eis($request);
        abort_if($user->is_admin, 404);

        $data = $request->validate([
            'notitie' => ['nullable', 'string', 'max:2000'],
            'opvolgen' => ['nullable', 'date'],
        ]);
        $user->forceFill([
            'beheer_notitie' => ($data['notitie'] ?? '') !== '' ? $data['notitie'] : null,
            'opvolgen_vanaf' => $data['opvolgen'] ?? null,
        ])->save();

        return back();
    }

    /** De proefperiode twee weken verlengen: het winback-wapen */
    public function verlengProef(Request $request, User $user)
    {
        $this->eis($request);
        abort_if($user->is_admin, 404);

        $basis = $user->proef_tot && $user->proef_tot->isFuture() ? $user->proef_tot : now();
        $user->forceFill(['proef_tot' => $basis->copy()->addDays(14)])->save();

        // De proef- en winbackmails mogen daarna opnieuw lopen
        \App\Models\MailLog::where('user_id', $user->id)
            ->whereIn('soort', ['proef-bijna-af', 'proef-laatste-dag', 'winback-1', 'winback-2'])
            ->delete();

        return back();
    }

    /** Meekijken met een zaak door als die zaak in te loggen */
    public function inloggenAls(Request $request, User $user)
    {
        $this->eis($request);
        abort_if($user->is_admin, 404);

        \Illuminate\Support\Facades\Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function pizzeria(Request $request, User $user)
    {
        $this->eis($request);
        abort_if($user->is_admin, 404);

        $ob = $user->onboarding ?? [];
        $orders = $user->orders()->where('is_demo', false);
        $aantal = (clone $orders)->count();
        $omzet = (int) (clone $orders)->sum('totaal');

        return view('admin.pizzeria', [
            'pizzeria' => $user,
            'ob' => $ob,
            'template' => self::THEMA_NAMEN[$ob['theme'] ?? ''] ?? 'Presto',
            'kleurNaam' => $this->kleurNaam($ob['color'] ?? null),
            'statusLabels' => self::STATUS_LABELS,
            'abonnement' => $user->abonnement_actief ? 'actief' : ($user->inProefperiode() ? 'proef' : 'verlopen'),
            'stats' => [
                'gerechten' => $user->menuItems()->count(),
                'bestellingen' => $aantal,
                'omzet' => $omzet,
                'omzet7' => (int) (clone $orders)->where('created_at', '>=', now()->subDays(7))->sum('totaal'),
                'omzet30' => (int) (clone $orders)->where('created_at', '>=', now()->subDays(30))->sum('totaal'),
                'gemBestelling' => $aantal > 0 ? (int) round($omzet / $aantal) : 0,
                'klanten' => $user->klanten()->count(),
                'spaarpunten' => (int) $user->klanten()->sum('punten'),
                'laatste' => (clone $orders)->latest()->value('created_at'),
            ],
            'menu' => $user->menuItems()->orderBy('categorie')->orderBy('volgorde')->get()->groupBy('categorie'),
            'orders' => (clone $orders)->latest()->take(25)->get(),
            'feedback' => \App\Models\Feedback::where('user_id', $user->id)->latest()->take(10)->get(),
        ]);
    }
}
