<?php

namespace App\Http\Controllers;

use App\Models\PrintJob;
use App\Models\User;
use App\Support\Bon;
use App\Support\Bonprinter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * De bonprinter: het adres waar de printer zelf zijn bonnen ophaalt (Epson Server Direct
 * Print), en de knoppen in het dashboard om 'm in te stellen en te testen.
 */
class PrinterController extends Controller
{
    /**
     * Hier komt de printer langs, elke paar seconden, met ConnectionType=GetRequest.
     * Antwoord: de wachtende bonnen als XML, of een lege body als er niets is.
     * Na het printen meldt hij het resultaat met ConnectionType=SetResponse.
     */
    public function poll(Request $request, string $token): Response
    {
        $user = User::where('printer_token', $token)->first();
        if (! $user) {
            return response('', 404);
        }

        // De naam die in de printer is ingesteld (nieuwere printers sturen Name mee, oudere alleen ID)
        $naam = mb_substr((string) $request->input('Name', ''), 0, 60) ?: ($user->printer_naam ?: mb_substr((string) $request->input('ID', ''), 0, 60));
        $user->forceFill(['printer_gezien_om' => now(), 'printer_naam' => $naam ?: null])->save();

        $soort = (string) $request->input('ConnectionType');
        if ($soort === 'GetRequest') {
            return response(Bonprinter::antwoord($user), 200)->header('Content-Type', 'text/xml; charset=utf-8');
        }
        if ($soort === 'SetResponse') {
            Bonprinter::verwerkResultaat($user, (string) $request->input('ResponseFile', ''));
        }

        return response('', 200)->header('Content-Type', 'text/xml; charset=utf-8');
    }

    /**
     * Het hulpprogramma (bon:agent) haalt hier de eerstvolgende bon op als ESC/POS-bytes.
     * 204 als er niets te printen is.
     */
    public function volgende(Request $request, string $token): Response
    {
        $user = User::where('printer_token', $token)->first();
        if (! $user) {
            return response('', 404);
        }
        $user->forceFill([
            'printer_gezien_om' => now(),
            'printer_naam' => mb_substr((string) $request->query('naam', ''), 0, 60) ?: $user->printer_naam,
        ])->save();

        $job = Bonprinter::volgende($user);
        if (! $job) {
            return response('', 204);
        }

        return response(Bon::escpos($job->inhoud), 200)
            ->header('Content-Type', 'application/octet-stream')
            ->header('X-Bon-Id', (string) $job->id)
            ->header('X-Bon-Soort', $job->soort);
    }

    /** Het hulpprogramma meldt of de bon eruit kwam */
    public function klaar(Request $request, string $token): Response
    {
        $user = User::where('printer_token', $token)->first();
        if (! $user) {
            return response('', 404);
        }
        $job = PrintJob::where('user_id', $user->id)->find((int) $request->input('id'));
        if ($job) {
            Bonprinter::resultaat($job, (bool) $request->input('ok'), (string) $request->input('fout', ''));
        }

        return response('', 200);
    }

    /* ── Dashboard ────────────────────────────────────────────────────── */

    /** De stand van zaken voor het instellingenpaneel */
    public function status(Request $request)
    {
        return response()->json($this->stand($request->user()));
    }

    public function instellingen(Request $request)
    {
        $data = $request->validate([
            'aan' => ['required', 'boolean'],
            'keukenbon' => ['required', 'boolean'],
            'klantbon' => ['required', 'boolean'],
            'kopieen' => ['required', 'integer', 'min:1', 'max:3'],
            'breedte' => ['required', 'integer', 'in:32,42,48'],
        ]);
        $user = $request->user();
        Bonprinter::token($user);
        $user->forceFill([
            'printer_aan' => $data['aan'],
            'printer_opties' => ['keukenbon' => $data['keukenbon'], 'klantbon' => $data['klantbon'], 'kopieen' => $data['kopieen'], 'breedte' => $data['breedte']],
        ])->save();

        return response()->json(['ok' => true] + $this->stand($user));
    }

    public function test(Request $request)
    {
        $user = $request->user();
        Bonprinter::token($user);
        Bonprinter::testbon($user);

        return response()->json(['ok' => true] + $this->stand($user));
    }

    /** Een nieuwe token, als de oude bijvoorbeeld in verkeerde handen is */
    public function nieuweToken(Request $request)
    {
        $user = $request->user();
        $user->forceFill(['printer_token' => null])->save();
        Bonprinter::token($user->fresh());

        return response()->json(['ok' => true] + $this->stand($user->fresh()));
    }

    private function stand(User $user): array
    {
        $gezien = $user->printer_gezien_om;

        return [
            'url' => Bonprinter::url($user),
            'aan' => (bool) $user->printer_aan,
            'opties' => Bonprinter::opties($user),
            'naam' => $user->printer_naam,
            'gezien_om' => $gezien?->toIso8601String(),
            'online' => $gezien ? $gezien->gt(now()->subMinutes(2)) : false,
            'wachtend' => PrintJob::where('user_id', $user->id)->whereIn('status', ['wacht', 'opgehaald'])->count(),
            'laatste' => PrintJob::where('user_id', $user->id)->latest('id')->first()?->only(['soort', 'status', 'fout', 'created_at']),
        ];
    }
}
