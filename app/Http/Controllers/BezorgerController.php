<?php

namespace App\Http\Controllers;

use App\Models\Medewerker;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Het teamscherm: een eigen, mobiel scherm voor bezorgers en keukenhulp.
 * Geen dashboard-rechten, alleen de bestellingen van hun eigen zaak.
 */
class BezorgerController extends Controller
{
    /** Statussen die de keuken zelf mag doorzetten (accepteren blijft bij de zaak, dat vraagt een tijdsschatting) */
    private const KEUKEN_STAPPEN = ['geaccepteerd' => 'bereiden', 'bereiden' => 'oven'];

    /* ── Inloggen en activeren ───────────────────────────────────── */

    public function showLogin(Request $request)
    {
        if ($request->session()->has('medewerker_id')) {
            return redirect()->route('bezorger.index');
        }

        return view('bezorger.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'wachtwoord' => ['required', 'string'],
        ], [
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Dit e-mailadres ziet er nog niet goed uit.',
            'wachtwoord.required' => 'Vul je wachtwoord in.',
        ]);

        $medewerker = Medewerker::with('user')
            ->whereRaw('LOWER(email) = ?', [strtolower(trim($data['email']))])
            ->whereNotNull('wachtwoord')
            ->get()
            ->first(fn ($m) => Hash::check($data['wachtwoord'], $m->wachtwoord));

        if (! $medewerker) {
            throw ValidationException::withMessages([
                'email' => 'Deze combinatie kennen we niet. Probeer het nog eens.',
            ]);
        }
        if (! $medewerker->actief) {
            throw ValidationException::withMessages([
                'email' => 'Je account staat op dit moment uit. Vraag je pizzeria om het weer aan te zetten.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('medewerker_id', $medewerker->id);
        $medewerker->forceFill(['laatst_actief_op' => now()])->save();

        return redirect()->route('bezorger.index');
    }

    public function showActiveren(string $token)
    {
        $medewerker = Medewerker::with('user')->where('uitnodiging_token', $token)->first();

        // Een uitnodiging blijft twee weken geldig; daarna vraag je de zaak om een nieuwe
        if (! $medewerker || ! $medewerker->actief || $medewerker->uitgenodigd_op?->lt(now()->subDays(14))) {
            return view('bezorger.activeren', ['medewerker' => null, 'token' => $token]);
        }

        return view('bezorger.activeren', ['medewerker' => $medewerker, 'token' => $token]);
    }

    public function activeren(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'wachtwoord' => ['required', 'string', 'min:8'],
            'wachtwoord_herhaal' => ['required', 'same:wachtwoord'],
        ], [
            'wachtwoord.required' => 'Kies een wachtwoord.',
            'wachtwoord.min' => 'Je wachtwoord moet minimaal 8 tekens zijn.',
            'wachtwoord_herhaal.same' => 'De wachtwoorden zijn niet hetzelfde.',
        ]);

        $medewerker = Medewerker::where('uitnodiging_token', $data['token'])->first();
        if (! $medewerker || ! $medewerker->actief || $medewerker->uitgenodigd_op?->lt(now()->subDays(14))) {
            throw ValidationException::withMessages([
                'wachtwoord' => 'Deze uitnodiging is verlopen. Vraag je pizzeria om een nieuwe.',
            ]);
        }

        $medewerker->forceFill([
            'wachtwoord' => $data['wachtwoord'],       // wordt gehasht door de cast
            'uitnodiging_token' => null,
            'laatst_actief_op' => now(),
        ])->save();

        $request->session()->regenerate();
        $request->session()->put('medewerker_id', $medewerker->id);

        return redirect()->route('bezorger.index');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('medewerker_id');
        $request->session()->regenerateToken();

        return redirect()->route('bezorger.login');
    }

    /* ── Het scherm zelf ─────────────────────────────────────────── */

    public function index(Request $request)
    {
        $medewerker = $request->attributes->get('medewerker');

        return view('bezorger.index', [
            'medewerker' => $medewerker,
            'zaak' => $medewerker->user->onboarding['name'] ?? 'je pizzeria',
            'orders' => $this->orders($medewerker),
        ]);
    }

    /** Verversen vanuit het scherm: alle lopende bestellingen van de zaak */
    public function data(Request $request)
    {
        $medewerker = $request->attributes->get('medewerker');

        // Niet bij elke poll schrijven; een keer per vijf minuten is genoeg voor "laatst actief"
        if ($medewerker->laatst_actief_op === null || $medewerker->laatst_actief_op->lt(now()->subMinutes(5))) {
            $medewerker->forceFill(['laatst_actief_op' => now()])->save();
        }

        return response()->json([
            'orders' => $this->orders($medewerker),
            'vandaag' => $medewerker->ritten()->whereDate('bezorgd_om', today())->count(),
        ]);
    }

    /* ── Acties ──────────────────────────────────────────────────── */

    /** Een rit oppakken: hij staat dan op jouw naam en gaat op onderweg */
    public function oppakken(Request $request, Order $order)
    {
        $medewerker = $request->attributes->get('medewerker');
        $this->magBij($medewerker, $order);

        if (! $medewerker->isBezorger()) {
            return response()->json(['message' => 'Alleen bezorgers kunnen een rit oppakken.'], 403);
        }
        if ($order->type !== 'bezorgen') {
            return response()->json(['message' => 'Deze bestelling wordt afgehaald.'], 422);
        }
        if ($order->bezorger_id !== null && $order->bezorger_id !== $medewerker->id) {
            return response()->json(['message' => 'Een collega was je net voor.'], 409);
        }
        if (! in_array($order->status, ['oven', 'onderweg'], true)) {
            return response()->json(['message' => 'Deze bestelling staat nog niet klaar.'], 422);
        }

        $order->update(['bezorger_id' => $medewerker->id, 'status' => 'onderweg']);

        return response()->json(['ok' => true, 'orders' => $this->orders($medewerker)]);
    }

    /** Toch niet: de rit gaat terug op de stapel voor een collega */
    public function vrijgeven(Request $request, Order $order)
    {
        $medewerker = $request->attributes->get('medewerker');
        $this->magBij($medewerker, $order);

        if ($order->bezorger_id !== $medewerker->id || $order->status !== 'onderweg') {
            return response()->json(['message' => 'Deze rit staat niet meer op jouw naam.'], 422);
        }

        $order->update(['bezorger_id' => null, 'status' => 'oven']);

        return response()->json(['ok' => true, 'orders' => $this->orders($medewerker)]);
    }

    /** Afgevinkt onderweg: bezorgd */
    public function bezorgd(Request $request, Order $order)
    {
        $medewerker = $request->attributes->get('medewerker');
        $this->magBij($medewerker, $order);

        if ($order->bezorger_id !== $medewerker->id) {
            return response()->json(['message' => 'Deze rit staat niet op jouw naam.'], 403);
        }
        if ($order->status === 'bezorgd') {
            return response()->json(['ok' => true, 'orders' => $this->orders($medewerker)]);
        }

        $order->update(['status' => 'bezorgd', 'bezorgd_om' => now()]);

        return response()->json(['ok' => true, 'orders' => $this->orders($medewerker)]);
    }

    /** Keukenhulp zet een bestelling een stap verder (bereiden, oven) */
    public function keukenStap(Request $request, Order $order)
    {
        $medewerker = $request->attributes->get('medewerker');
        $this->magBij($medewerker, $order);

        if ($medewerker->rol !== 'keuken') {
            return response()->json(['message' => 'Alleen de keuken kan dit doorzetten.'], 403);
        }
        $volgende = self::KEUKEN_STAPPEN[$order->status] ?? null;
        if ($volgende === null) {
            return response()->json(['message' => 'Deze stap kan de zaak zelf zetten.'], 422);
        }

        $order->update(['status' => $volgende]);

        return response()->json(['ok' => true, 'orders' => $this->orders($medewerker)]);
    }

    /* ── Hulp ────────────────────────────────────────────────────── */

    /** Teamleden komen alleen bij bestellingen van hun eigen zaak */
    private function magBij(Medewerker $medewerker, Order $order): void
    {
        abort_unless($order->user_id === $medewerker->user_id, 403);
    }

    /**
     * Alles wat nog loopt, plus wat dit teamlid vandaag zelf heeft afgerond.
     * Het scherm zelf bepaalt welke lijst waar terechtkomt.
     */
    private function orders(Medewerker $medewerker): array
    {
        return Order::where('user_id', $medewerker->user_id)
            ->where(function ($q) use ($medewerker) {
                $q->where('status', '!=', 'bezorgd')
                    ->orWhere(fn ($e) => $e->where('bezorger_id', $medewerker->id)->whereDate('bezorgd_om', today()));
            })
            ->with('bezorger:id,naam')
            ->orderBy('created_at')
            ->get()
            ->map(fn (Order $o) => [
                'id' => $o->id,
                'nummer' => $o->nummer,
                'klant' => $o->klant,
                'adres' => $o->adres,
                'type' => $o->type,
                'status' => $o->status,
                'totaal' => $o->totaal,
                'opmerking' => $o->opmerking,
                'eta_minuten' => $o->eta_minuten,
                'items' => collect($o->items ?? [])->map(fn ($i) => [
                    'aantal' => $i['aantal'] ?? 1,
                    'naam' => $i['naam'] ?? '',
                ])->values(),
                'bezorgerId' => $o->bezorger_id,
                'bezorgerNaam' => $o->bezorger?->naam,
                'vanMij' => $o->bezorger_id === $medewerker->id,
                'created_at' => $o->created_at?->toIso8601String(),
                'bezorgd_om' => $o->bezorgd_om?->toIso8601String(),
            ])->values()->all();
    }
}
