<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Het hulpprogramma voor de bonprinter: draait in de zaak (op een pc of een Raspberry Pi
 * naast de printer), haalt de bonnen op bij Shop & Eat en stuurt ze als ESC/POS naar de
 * printer op poort 9100. Nodig voor elke bonprinter die niet zelf bij ons kan aankloppen.
 *
 *   php artisan bon:agent --url=https://shopandeat.eu/printer/<token> --printer=192.168.1.205
 */
class BonAgent extends Command
{
    protected $signature = 'bon:agent
        {--url= : Het printeradres uit het dashboard (Instellingen > Bonprinter)}
        {--printer= : Het IP-adres van de bonprinter in de zaak}
        {--poort=9100 : De poort van de printer}
        {--interval=3 : Om de hoeveel seconden we bij Shop & Eat kijken}
        {--naam=keuken : De naam waaronder de printer in het dashboard verschijnt}';

    protected $description = 'Haalt bonnen op bij Shop & Eat en stuurt ze naar een netwerkprinter (ESC/POS, poort 9100)';

    public function handle(): int
    {
        $url = rtrim((string) $this->option('url'), '/');
        $printer = (string) $this->option('printer');
        $poort = (int) $this->option('poort');
        $interval = max(1, (int) $this->option('interval'));
        if ($url === '' || $printer === '') {
            $this->error('Geef --url (het adres uit het dashboard) en --printer (het IP van de printer) mee.');

            return self::FAILURE;
        }

        $this->info("Bonprinter-hulpprogramma gestart. Printer {$printer}:{$poort}, elke {$interval}s kijken bij {$url}");
        $this->line('Stoppen met Ctrl+C.');
        $fouten = 0;

        while (true) {
            try {
                $antwoord = Http::timeout(15)->withHeaders(['Accept' => 'application/octet-stream'])
                    ->get($url . '/volgende', ['naam' => $this->option('naam')]);
            } catch (\Throwable $e) {
                $this->warn(now()->format('H:i:s') . '  Shop & Eat niet bereikbaar: ' . $e->getMessage());
                sleep(min(30, $interval * ++$fouten));

                continue;
            }
            $fouten = 0;

            if ($antwoord->status() === 204 || $antwoord->body() === '') {
                sleep($interval);

                continue;
            }
            if (! $antwoord->ok()) {
                $this->warn(now()->format('H:i:s') . '  Onverwacht antwoord ' . $antwoord->status() . ' (klopt de url met de token?)');
                sleep(min(30, $interval * 5));

                continue;
            }

            $id = (int) $antwoord->header('X-Bon-Id');
            $soort = (string) $antwoord->header('X-Bon-Soort');
            [$gelukt, $fout] = $this->stuurNaarPrinter($printer, $poort, $antwoord->body());
            $this->line(now()->format('H:i:s') . "  Bon {$id} ({$soort}): " . ($gelukt ? 'geprint' : 'mislukt, ' . $fout));

            try {
                Http::timeout(15)->asForm()->post($url . '/klaar', ['id' => $id, 'ok' => $gelukt ? 1 : 0, 'fout' => $fout]);
            } catch (\Throwable $e) {
                $this->warn('  Resultaat kon niet gemeld worden: ' . $e->getMessage());
            }
            if (! $gelukt) {
                sleep(min(30, $interval * 3));
            }
        }
    }

    /** Ruwe bytes naar de printer; de meeste printers sluiten zelf af zodra de bon binnen is */
    private function stuurNaarPrinter(string $printer, int $poort, string $bytes): array
    {
        $socket = @fsockopen($printer, $poort, $errno, $errstr, 5);
        if (! $socket) {
            return [false, 'PRINTER_ONBEREIKBAAR'];
        }
        stream_set_timeout($socket, 10);
        $verzonden = 0;
        while ($verzonden < strlen($bytes)) {
            $n = @fwrite($socket, substr($bytes, $verzonden));
            if ($n === false || $n === 0) {
                fclose($socket);

                return [false, 'VERBINDING_VERBROKEN'];
            }
            $verzonden += $n;
        }
        fclose($socket);

        return [true, null];
    }
}
