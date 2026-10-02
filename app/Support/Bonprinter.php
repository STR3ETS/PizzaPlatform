<?php

namespace App\Support;

use App\Models\Order;
use App\Models\PrintJob;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * De bonprinter van de zaak.
 *
 * Een bon wordt één keer opgebouwd in een neutraal formaat (zie Bon) en in de wachtrij
 * gezet. Daarna zijn er twee manieren om 'm bij de printer te krijgen:
 *
 *  1. Epson Server Direct Print: een Epson met netwerk (TM-m30III en zo) haalt zelf elke
 *     paar seconden zijn bonnen op bij /printer/{token}, als ePOS-Print XML.
 *  2. Het hulpprogramma (php artisan bon:agent): draait in de zaak op een pc of Pi,
 *     haalt bonnen op bij /printer/{token}/volgende als ESC/POS en stuurt ze naar elke
 *     netwerkprinter op poort 9100 (de meeste bonprinters, ook de goedkope).
 *
 * De token in het adres is het wachtwoord van de zaak; er is geen login.
 */
final class Bonprinter
{
    /** De standaardinstellingen van een zaak die de printer aanzet */
    public const OPTIES = ['keukenbon' => true, 'klantbon' => false, 'kopieen' => 1, 'breedte' => 48];

    /** Na zoveel minuten zonder bericht van de printer gaat een opgehaalde bon terug in de wachtrij */
    public const HERKANS_NA_MINUTEN = 3;

    public const MAX_POGINGEN = 3;

    /** De geheime token van de zaak; maakt er een als die er nog niet is */
    public static function token(User $user): string
    {
        if (! $user->printer_token) {
            $user->forceFill(['printer_token' => Str::lower(Str::random(32))])->save();
        }

        return $user->printer_token;
    }

    /** Het adres dat in de printer of in het hulpprogramma wordt ingesteld */
    public static function url(User $user): string
    {
        return rtrim(config('app.url'), '/') . '/printer/' . self::token($user);
    }

    public static function opties(User $user): array
    {
        return array_merge(self::OPTIES, $user->printer_opties ?? []);
    }

    /** Zet de bonnen voor een betaalde bestelling in de wachtrij, volgens de instellingen van de zaak */
    public static function plan(User $user, Order $order): void
    {
        if (! $user->printer_aan) {
            return;
        }
        $opties = self::opties($user);
        for ($i = 0; $i < max(1, min(3, (int) $opties['kopieen'])); $i++) {
            if ($opties['keukenbon']) {
                self::zetKlaar($user, 'keuken', self::keukenbon($user, $order, (int) $opties['breedte']), $order);
            }
        }
        if ($opties['klantbon']) {
            self::zetKlaar($user, 'klant', self::klantbon($user, $order, (int) $opties['breedte']), $order);
        }
    }

    public static function testbon(User $user): PrintJob
    {
        return self::zetKlaar($user, 'test', self::proefbon($user, (int) self::opties($user)['breedte']));
    }

    private static function zetKlaar(User $user, string $soort, Bon $bon, ?Order $order = null): PrintJob
    {
        return PrintJob::create([
            'user_id' => $user->id,
            'order_id' => $order?->id,
            'soort' => $soort,
            'inhoud' => $bon->toArray(),
            'status' => 'wacht',
        ]);
    }

    /* ── Ophalen ──────────────────────────────────────────────────────── */

    /** Bonnen die te lang op een bericht wachten krijgen een nieuwe kans, tot het maximum */
    private static function herkansen(User $user): void
    {
        PrintJob::where('user_id', $user->id)
            ->where('status', 'opgehaald')
            ->where('opgehaald_om', '<', now()->subMinutes(self::HERKANS_NA_MINUTEN))
            ->get()
            ->each(fn (PrintJob $job) => $job->pogingen >= self::MAX_POGINGEN
                ? $job->update(['status' => 'mislukt', 'fout' => 'GEEN_BERICHT'])
                : $job->update(['status' => 'wacht']));
    }

    private static function markeerOpgehaald(PrintJob $job): void
    {
        $job->update(['status' => 'opgehaald', 'opgehaald_om' => now(), 'pogingen' => $job->pogingen + 1]);
    }

    /**
     * Epson Server Direct Print: de wachtende bonnen (hoogstens vijf per keer) als
     * PrintRequestInfo-XML, of een lege body als er niets is.
     */
    public static function antwoord(User $user): string
    {
        self::herkansen($user);
        $jobs = PrintJob::where('user_id', $user->id)->where('status', 'wacht')->orderBy('id')->limit(5)->get();
        if ($jobs->isEmpty()) {
            return '';
        }
        $delen = $jobs->map(function (PrintJob $job) {
            self::markeerOpgehaald($job);

            return '<ePOSPrint><Parameter><devid>local_printer</devid><timeout>10000</timeout><printjobid>' . $job->printjobid() . '</printjobid></Parameter>'
                . '<PrintData>' . Bon::epos($job->inhoud) . '</PrintData></ePOSPrint>';
        })->implode('');

        return '<?xml version="1.0" encoding="utf-8"?><PrintRequestInfo Version="2.00">' . $delen . '</PrintRequestInfo>';
    }

    /** Het hulpprogramma: de eerstvolgende bon, of null als er niets is */
    public static function volgende(User $user): ?PrintJob
    {
        self::herkansen($user);
        $job = PrintJob::where('user_id', $user->id)->where('status', 'wacht')->orderBy('id')->first();
        if ($job) {
            self::markeerOpgehaald($job);
        }

        return $job;
    }

    /** Het hulpprogramma of de Epson meldt het resultaat van één bon */
    public static function resultaat(PrintJob $job, bool $gelukt, ?string $fout = null): void
    {
        $job->update($gelukt
            ? ['status' => 'geprint', 'geprint_om' => now(), 'fout' => null]
            : ['status' => $job->pogingen >= self::MAX_POGINGEN ? 'mislukt' : 'wacht', 'fout' => Str::limit($fout ?: 'ONBEKEND', 60, '')]);
    }

    /** De Epson meldt het resultaat als PrintResponseInfo-XML; zonder printjobid nemen we de oudste opgehaalde bon */
    public static function verwerkResultaat(User $user, string $xml): void
    {
        $doc = @simplexml_load_string($xml);
        if (! $doc) {
            return;
        }
        foreach ($doc->children() as $response) {
            if ($response->getName() !== 'response') {
                continue;
            }
            $jobId = (string) $response['printjobid'];
            $job = $jobId !== '' && str_starts_with($jobId, 'bon-')
                ? PrintJob::where('user_id', $user->id)->find((int) substr($jobId, 4))
                : PrintJob::where('user_id', $user->id)->where('status', 'opgehaald')->orderBy('opgehaald_om')->first();
            if ($job) {
                self::resultaat($job, (string) $response['success'] === 'true', (string) $response['code']);
            }
        }
    }

    /* ── De bonnen zelf ───────────────────────────────────────────────── */

    /** Keukenbon: groot nummer, wat en hoeveel, bijzonderheden; geen prijzen nodig in de keuken */
    public static function keukenbon(User $user, Order $order, int $breedte = 48): Bon
    {
        $b = new Bon($breedte);
        $b->kop(self::zaaknaam($user));
        $b->groot('#' . $order->nummer . '  ' . ($order->type === 'bezorgen' ? 'BEZORGEN' : 'AFHALEN'));
        $b->regel(self::tijd($order) . ($order->eta_minuten ? '   klaar om ' . now()->addMinutes((int) $order->eta_minuten)->format('H:i') : ''));
        $b->regel('Voor ' . $order->klant);
        $b->lijn();
        foreach ($order->items as $item) {
            if (($item['naam'] ?? '') === 'Bezorgkosten') {
                continue;
            }
            $b->vet(($item['aantal'] ?? 1) . 'x ' . ($item['naam'] ?? ''));
            foreach ($item['opties'] ?? [] as $optie) {
                $b->regel('   + ' . ($optie['naam'] ?? ''));
            }
        }
        if ($order->opmerking) {
            $b->lijn();
            $b->vet('Opmerking:');
            $b->tekst($order->opmerking);
        }
        if ($order->type === 'bezorgen' && $order->adres) {
            $b->lijn();
            $b->vet('Bezorgadres:');
            $b->tekst($order->adres);
        }
        $b->lijn();
        $b->regel(self::betaaldRegel($order));

        return $b;
    }

    /** Klantbon: alles met prijzen, totaal, spaarpunten en een QR naar de bestelstatus */
    public static function klantbon(User $user, Order $order, int $breedte = 48): Bon
    {
        $b = new Bon($breedte);
        $b->kop(self::zaaknaam($user));
        $adres = trim(($user->onboarding['street'] ?? '') . ', ' . ($user->onboarding['zip'] ?? '') . ' ' . ($user->onboarding['city'] ?? ''), ', ');
        if ($adres !== '') {
            $b->midden($adres);
        }
        $b->lijn();
        $b->regel('Bestelling #' . $order->nummer . '   ' . self::tijd($order));
        $b->regel(($order->type === 'bezorgen' ? 'Bezorgen' : 'Afhalen') . '   ' . $order->klant);
        $b->lijn();
        foreach ($order->items as $item) {
            $aantal = (int) ($item['aantal'] ?? 1);
            $b->prijsregel($aantal . 'x ' . ($item['naam'] ?? ''), (int) ($item['prijs'] ?? 0) * $aantal);
            foreach ($item['opties'] ?? [] as $optie) {
                // Een gratis wijziging ("zonder olijven") krijgt geen bedrag achter zich
                $prijs = (int) ($optie['prijs'] ?? 0) * $aantal;
                $prijs > 0
                    ? $b->prijsregel('   + ' . ($optie['naam'] ?? ''), $prijs)
                    : $b->regel('   + ' . ($optie['naam'] ?? ''));
            }
        }
        $b->lijn();
        $b->prijsregel('Totaal', (int) $order->totaal, true);
        $b->regel(self::betaaldRegel($order));
        if ($order->punten > 0) {
            $b->regel('Gespaard: ' . $order->punten . ' punten');
        }
        $b->voer();
        if ($order->token) {
            $b->midden('Volg je bestelling:');
            $b->qr(rtrim(config('app.url'), '/') . '/bestellen/' . $user->slug . '/bestelling/' . $order->token);
        }
        $b->midden('Bedankt en eet smakelijk!');

        return $b;
    }

    public static function proefbon(User $user, int $breedte = 48): Bon
    {
        $b = new Bon($breedte);
        $b->kop(self::zaaknaam($user));
        $b->groot('TESTBON');
        $b->regel(now()->format('d-m-Y H:i'));
        $b->lijn();
        $b->tekst('Je bonprinter is gekoppeld aan Shop & Eat. Vanaf nu komt elke betaalde bestelling hier uit.');
        $b->lijn();
        $b->vet('2x Margherita');
        $b->regel('   + extra kaas');
        $b->vet('1x Cola');
        $b->lijn();
        $b->midden('Alles werkt.');

        return $b;
    }

    private static function zaaknaam(User $user): string
    {
        return $user->onboarding['name'] ?? $user->name ?? 'Shop & Eat';
    }

    private static function tijd(Order $order): string
    {
        return ($order->betaald_om ?? $order->created_at ?? now())->timezone(config('app.timezone'))->format('d-m H:i');
    }

    private static function betaaldRegel(Order $order): string
    {
        return $order->betaal_status === 'betaald' && $order->stripe_payment_intent
            ? 'Online betaald'
            : ($order->betaal_status === 'betaald' ? 'Betaald' : 'Nog te betalen: EUR ' . number_format($order->totaal / 100, 2, ',', '.'));
    }
}
