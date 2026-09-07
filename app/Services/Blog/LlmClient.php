<?php

namespace App\Services\Blog;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Dunne, provider-agnostische LLM-client (Anthropic of OpenAI).
 * Alle content-stappen draaien bewust op één STERK model uit de brand-kit.
 */
class LlmClient
{
    public function isGeconfigureerd(): bool
    {
        return config('seo-content.llm.key') !== '';
    }

    public function tekst(string $systeem, string $prompt, ?int $maxTokens = null): string
    {
        $cfg = config('seo-content.llm');
        if ($cfg['key'] === '') {
            throw new RuntimeException('Geen LLM-key geconfigureerd (SEO_LLM_KEY in .env).');
        }
        $maxTokens = $maxTokens ?: $cfg['max_tokens'];

        if ($cfg['provider'] === 'openai') {
            $resp = Http::timeout(180)->withToken($cfg['key'])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $cfg['model'],
                    'max_tokens' => $maxTokens,
                    'messages' => [
                        ['role' => 'system', 'content' => $systeem],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ])->throw()->json();

            return (string) ($resp['choices'][0]['message']['content'] ?? '');
        }

        $resp = Http::timeout(180)
            ->withHeaders(['x-api-key' => $cfg['key'], 'anthropic-version' => '2023-06-01'])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $cfg['model'],
                'max_tokens' => $maxTokens,
                'system' => $systeem,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ])->throw()->json();

        /* Denk-modellen sturen eerst een thinking-blok; pak alle tekstblokken */
        return collect($resp['content'] ?? [])
            ->where('type', 'text')
            ->pluck('text')
            ->implode('');
    }

    /** Vraagt om JSON en parseert het eerste JSON-object/array uit het antwoord. */
    public function json(string $systeem, string $prompt, ?int $maxTokens = null): array
    {
        return $this->parseJson($this->tekst($systeem . "\nAntwoord UITSLUITEND met geldige JSON, zonder toelichting of markdown-fences.", $prompt, $maxTokens));
    }

    /**
     * Beoordeelt een JPEG (bijv. een kandidaat-coverfoto) en geeft JSON terug.
     * Alleen voor Anthropic; bij andere providers wordt de check overgeslagen.
     */
    public function beoordeelAfbeelding(string $jpegBinair, string $systeem, string $prompt, int $maxTokens = 1500): array
    {
        $cfg = config('seo-content.llm');
        if ($cfg['key'] === '' || $cfg['provider'] !== 'anthropic') {
            return ['geschikt' => true];
        }

        $resp = Http::timeout(90)
            ->withHeaders(['x-api-key' => $cfg['key'], 'anthropic-version' => '2023-06-01'])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $cfg['model'],
                'max_tokens' => $maxTokens,
                'system' => $systeem . "\nAntwoord UITSLUITEND met geldige JSON.",
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => 'image/jpeg', 'data' => base64_encode($jpegBinair)]],
                        ['type' => 'text', 'text' => $prompt],
                    ],
                ]],
            ])->throw()->json();

        $ruw = collect($resp['content'] ?? [])->where('type', 'text')->pluck('text')->implode('');

        return $this->parseJson($ruw);
    }

    private function parseJson(string $ruw): array
    {
        $ruw = trim($ruw);
        $ruw = preg_replace('/^```(?:json)?|```$/m', '', $ruw);

        $start = strpos($ruw, '{');
        $startLijst = strpos($ruw, '[');
        if ($startLijst !== false && ($start === false || $startLijst < $start)) {
            $start = $startLijst;
        }
        if ($start === false) {
            throw new RuntimeException('LLM gaf geen JSON terug. Begin van antwoord: "' . mb_substr($ruw, 0, 300) . '"');
        }

        $data = json_decode(substr($ruw, $start), true);
        if (! is_array($data)) {
            throw new RuntimeException('LLM-JSON kon niet geparseerd worden. Begin van antwoord: "' . mb_substr($ruw, 0, 300) . '"');
        }

        return $data;
    }
}
