<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Een bon als lijst van stappen (kop, regel, lijn, QR, ...), los van de printer. Daaruit
 * maken we ePOS-Print XML (Epson) of ESC/POS-bytes (vrijwel elke andere bonprinter).
 * Teksten gaan door Str::ascii, want de printers kennen zonder codetabel-gedoe alleen
 * de gewone letters; prijzen krijgen EUR in plaats van €.
 */
final class Bon
{
    private array $stappen = [];

    public function __construct(private int $breedte = 48) {}

    public function kop(string $naam): void
    {
        $this->stappen[] = ['kop', Str::limit(Str::ascii($naam), intdiv($this->breedte, 2), '')];
    }

    public function groot(string $tekst): void
    {
        $this->stappen[] = ['groot', Str::ascii($tekst)];
    }

    public function vet(string $tekst): void
    {
        $this->stappen[] = ['vet', Str::ascii($tekst)];
    }

    public function regel(string $tekst): void
    {
        $this->stappen[] = ['regel', Str::ascii($tekst)];
    }

    public function midden(string $tekst): void
    {
        $this->stappen[] = ['midden', Str::ascii($tekst)];
    }

    /** Lange tekst netjes afgebroken op de breedte van de bon */
    public function tekst(string $tekst): void
    {
        foreach (explode("\n", wordwrap(Str::ascii($tekst), $this->breedte, "\n", true)) as $r) {
            $this->stappen[] = ['regel', $r];
        }
    }

    /** Omschrijving links, bedrag rechts, op dezelfde regel */
    public function prijsregel(string $omschrijving, int $cent, bool $vet = false): void
    {
        $bedrag = 'EUR ' . number_format($cent / 100, 2, ',', '.');
        $links = Str::limit(Str::ascii($omschrijving), $this->breedte - strlen($bedrag) - 1, '');
        $this->stappen[] = [$vet ? 'vet' : 'regel', str_pad($links, $this->breedte - strlen($bedrag)) . $bedrag];
    }

    public function lijn(): void
    {
        $this->stappen[] = ['regel', str_repeat('-', $this->breedte)];
    }

    public function voer(int $regels = 1): void
    {
        $this->stappen[] = ['voer', $regels];
    }

    public function qr(string $url): void
    {
        $this->stappen[] = ['qr', $url];
    }

    public function toArray(): array
    {
        return ['breedte' => $this->breedte, 'stappen' => $this->stappen];
    }

    /* ── Naar de printer ──────────────────────────────────────────────── */

    /** Epson: ePOS-Print XML */
    public static function epos(array $inhoud): string
    {
        $t = fn (string $s) => '<text>' . htmlspecialchars($s, ENT_XML1) . '&#10;</text>';
        $uit = '';
        foreach ($inhoud['stappen'] ?? [] as [$soort, $waarde]) {
            $uit .= match ($soort) {
                'kop' => '<text align="center"/><text width="2" height="2"/><text em="true"/>' . $t($waarde) . '<text em="false"/><text width="1" height="1"/><feed line="1"/><text align="left"/>',
                'groot' => '<text width="2" height="2"/>' . $t($waarde) . '<text width="1" height="1"/>',
                'vet' => '<text em="true"/>' . $t($waarde) . '<text em="false"/>',
                'midden' => '<text align="center"/>' . $t($waarde) . '<text align="left"/>',
                'voer' => '<feed line="' . (int) $waarde . '"/>',
                'qr' => '<text align="center"/><symbol type="qrcode_model_2" level="level_m" width="5" height="0" size="0">' . htmlspecialchars($waarde, ENT_XML1) . '</symbol><text align="left"/>',
                default => $t($waarde),
            };
        }

        return '<epos-print xmlns="http://www.epson-pos.com/schemas/2011/03/epos-print"><text lang="en"/><text smooth="true"/><text font="font_a"/>'
            . $uit . '<feed line="3"/><cut type="feed"/></epos-print>';
    }

    /** Alle andere bonprinters: ESC/POS-bytes voor poort 9100 */
    public static function escpos(array $inhoud): string
    {
        $uit = "\x1B@";                                    // reset
        $uit .= "\x1Bt\x00";                               // codetabel 437, we sturen toch alleen ascii
        foreach ($inhoud['stappen'] ?? [] as [$soort, $waarde]) {
            $uit .= match ($soort) {
                'kop' => "\x1Ba\x01\x1D!\x11\x1BE\x01" . $waarde . "\n\x1BE\x00\x1D!\x00\n\x1Ba\x00",
                'groot' => "\x1D!\x11" . $waarde . "\n\x1D!\x00",
                'vet' => "\x1BE\x01" . $waarde . "\n\x1BE\x00",
                'midden' => "\x1Ba\x01" . $waarde . "\n\x1Ba\x00",
                'voer' => "\x1Bd" . chr(max(1, min(10, (int) $waarde))),
                'qr' => self::escposQr($waarde),
                default => $waarde . "\n",
            };
        }

        return $uit . "\x1Bd\x03" . "\x1DV\x42\x00";      // 3 regels doorvoeren en snijden
    }

    /** QR-code via GS ( k: model 2, grootte 5, foutcorrectie M, gecentreerd */
    private static function escposQr(string $data): string
    {
        $len = strlen($data) + 3;

        return "\x1Ba\x01"
            . "\x1D(k\x04\x00\x31\x41\x32\x00"            // model 2
            . "\x1D(k\x03\x00\x31\x43\x05"                // grootte
            . "\x1D(k\x03\x00\x31\x45\x31"                // foutcorrectie M
            . "\x1D(k" . chr($len % 256) . chr(intdiv($len, 256)) . "\x31\x50\x30" . $data   // data opslaan
            . "\x1D(k\x03\x00\x31\x51\x30"                // printen
            . "\n\x1Ba\x00";
    }
}
