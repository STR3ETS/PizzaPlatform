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

        $pizzerias = User::where('is_admin', false)->orderByDesc('created_at')->get()->map(function ($u) use ($orderStats, $menuTellingen) {
            $ob = $u->onboarding ?? [];
            $stats = $orderStats[$u->id] ?? null;

            return [
                'id' => $u->id,
                'naam' => $ob['name'] ?? $u->name,
                'plaats' => $ob['city'] ?? null,
                'logo' => $ob['logo'] ?? null,
                'slug' => $u->slug,
                'online' => (bool) $u->is_online,
                'template' => self::THEMA_NAMEN[$ob['theme'] ?? ''] ?? 'Presto',
                'gerechten' => (int) ($menuTellingen[$u->id] ?? 0),
                'bestellingen' => (int) ($stats->aantal ?? 0),
                'omzet' => (int) ($stats->omzet ?? 0),
                'laatste' => $stats?->laatste,
                'aangemeld' => $u->created_at,
            ];
        });

        $recent = Order::where('is_demo', false)->with('user')->latest()->take(12)->get();

        return view('admin.index', [
            'pizzerias' => $pizzerias,
            'recent' => $recent,
            'statusLabels' => self::STATUS_LABELS,
            'totalen' => [
                'pizzerias' => $pizzerias->count(),
                'bestellingen' => (int) $orderStats->sum('aantal'),
                'omzet' => (int) $orderStats->sum('omzet'),
                'vandaag' => Order::where('is_demo', false)->whereDate('created_at', today())->count(),
            ],
        ]);
    }

    public function pizzeria(Request $request, User $user)
    {
        $this->eis($request);
        abort_if($user->is_admin, 404);

        $ob = $user->onboarding ?? [];
        $orders = $user->orders()->where('is_demo', false);

        return view('admin.pizzeria', [
            'pizzeria' => $user,
            'ob' => $ob,
            'template' => self::THEMA_NAMEN[$ob['theme'] ?? ''] ?? 'Presto',
            'kleurNaam' => $this->kleurNaam($ob['color'] ?? null),
            'statusLabels' => self::STATUS_LABELS,
            'stats' => [
                'gerechten' => $user->menuItems()->count(),
                'bestellingen' => (clone $orders)->count(),
                'omzet' => (int) (clone $orders)->sum('totaal'),
                'omzet7' => (int) (clone $orders)->where('created_at', '>=', now()->subDays(7))->sum('totaal'),
            ],
            'orders' => (clone $orders)->latest()->take(25)->get(),
        ]);
    }
}
