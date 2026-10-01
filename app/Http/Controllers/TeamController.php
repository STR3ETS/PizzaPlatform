<?php

namespace App\Http\Controllers;

use App\Mail\TeamUitnodigingMail;
use App\Models\Medewerker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Het team van een pizzeria, beheerd vanuit het dashboard. De teamleden zelf
 * werken op /bezorger; hier worden ze alleen uitgenodigd en beheerd.
 */
class TeamController extends Controller
{
    /** Alles wat het dashboard van een teamlid moet weten */
    public static function voorDashboard(Medewerker $m): array
    {
        return [
            'id' => $m->id,
            'naam' => $m->naam,
            'email' => $m->email,
            'telefoon' => $m->telefoon,
            'rol' => $m->rol,
            'rolLabel' => $m->rolLabel(),
            'actief' => $m->actief,
            'uitgenodigd' => $m->isUitgenodigd(),
            'laatstActief' => $m->laatst_actief_op?->toIso8601String(),
        ];
    }

    public function index(Request $request)
    {
        return response()->json([
            'team' => $request->user()->medewerkers()->orderBy('naam')->get()
                ->map(fn ($m) => self::voorDashboard($m))->values(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'naam' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:255'],
            'telefoon' => ['nullable', 'string', 'max:30'],
            'rol' => ['required', 'in:' . implode(',', Medewerker::ROLLEN)],
        ], [
            'naam.required' => 'Vul de naam van je teamlid in.',
            'email.required' => 'Vul een e-mailadres in, daar sturen we de uitnodiging heen.',
            'email.email' => 'Dit e-mailadres ziet er nog niet goed uit.',
            'rol.in' => 'Kies bezorger of keuken.',
        ]);

        $bestaat = $request->user()->medewerkers()
            ->whereRaw('LOWER(email) = ?', [strtolower($data['email'])])->exists();
        if ($bestaat) {
            throw ValidationException::withMessages([
                'email' => 'Dit teamlid staat al in je team.',
            ]);
        }

        $medewerker = $request->user()->medewerkers()->create([
            'naam' => $data['naam'],
            'email' => strtolower(trim($data['email'])),
            'telefoon' => $data['telefoon'] ?: null,
            'rol' => $data['rol'],
            'actief' => true,
            'uitnodiging_token' => Str::random(64),
            'uitgenodigd_op' => now(),
        ]);

        $verstuurd = $this->stuurUitnodiging($medewerker);

        return response()->json([
            'ok' => true,
            'verstuurd' => $verstuurd,
            'medewerker' => self::voorDashboard($medewerker),
        ]);
    }

    /** Uitnodiging opnieuw sturen, met een verse link */
    public function opnieuw(Request $request, Medewerker $medewerker)
    {
        abort_unless($medewerker->user_id === $request->user()->id, 403);

        $medewerker->forceFill([
            'uitnodiging_token' => Str::random(64),
            'uitgenodigd_op' => now(),
        ])->save();

        return response()->json(['ok' => true, 'verstuurd' => $this->stuurUitnodiging($medewerker)]);
    }

    /** Teamlid aan of uit zetten: uit betekent direct niet meer kunnen inloggen */
    public function wissel(Request $request, Medewerker $medewerker)
    {
        abort_unless($medewerker->user_id === $request->user()->id, 403);

        $medewerker->forceFill(['actief' => ! $medewerker->actief])->save();

        return response()->json(['ok' => true, 'medewerker' => self::voorDashboard($medewerker)]);
    }

    public function destroy(Request $request, Medewerker $medewerker)
    {
        abort_unless($medewerker->user_id === $request->user()->id, 403);

        // Bezorgde ritten blijven bestaan; de koppeling wordt losgelaten (nullOnDelete)
        $medewerker->delete();

        return response()->json(['ok' => true]);
    }

    /** Mislukte mail mag het uitnodigen nooit blokkeren: de link is ook opnieuw te sturen */
    private function stuurUitnodiging(Medewerker $medewerker): bool
    {
        try {
            Mail::to($medewerker->email)->send(new TeamUitnodigingMail($medewerker));

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
