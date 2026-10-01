<?php

namespace App\Support;

/**
 * Kleurhulpjes voor het designsysteem.
 */
final class Kleur
{
    /**
     * Witte of donkere tekst op een gegeven achtergrondkleur.
     *
     * Wit wint zolang het contrast met wit minstens 3:1 is (WCAG voor grote
     * en vette tekst, zoals op knoppen). Daaronder, bij geel of lichtgroen
     * bijvoorbeeld, wordt het de donkere inkt van het systeem.
     */
    public static function inkOp(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = preg_replace('/(.)/', '$1$1', $hex);
        }
        if (! preg_match('/^[0-9a-f]{6}$/i', $hex)) {
            return '#ffffff';
        }

        [$r, $g, $b] = array_map(fn (string $deel) => hexdec($deel) / 255, str_split($hex, 2));
        $lineair = fn (float $c) => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        $luminantie = 0.2126 * $lineair($r) + 0.7152 * $lineair($g) + 0.0722 * $lineair($b);

        return (1.05 / ($luminantie + 0.05)) >= 3.0 ? '#ffffff' : '#1b1714';
    }
}
