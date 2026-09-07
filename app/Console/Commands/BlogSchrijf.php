<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\Blog\AutonomousDrafter;
use App\Services\Blog\LlmClient;
use Illuminate\Console\Command;
use Throwable;

class BlogSchrijf extends Command
{
    protected $signature = 'blog:schrijf {--limit=1}';

    protected $description = 'Schrijf blogconcepten tot de limiet, met de rem: nooit voorbij de review-wachtrij-cap';

    public function handle(AutonomousDrafter $drafter, LlmClient $llm): int
    {
        if (! $llm->isGeconfigureerd()) {
            $this->error('Geen LLM-key geconfigureerd (SEO_LLM_KEY in .env). Schrijven vereist een sterk model.');

            return self::FAILURE;
        }

        $cap = (int) config('seo-content.review_queue_cap');
        $inWachtrij = Post::where('generation_status', 'needs_review')->count();
        $ruimte = max(0, $cap - $inWachtrij);
        $doel = min((int) $this->option('limit'), $ruimte);

        if ($doel === 0) {
            $this->warn("Review-wachtrij zit vol ({$inWachtrij}/{$cap}). Eerst beoordelen in het beheer, dan pas verder schrijven.");

            return self::SUCCESS;
        }

        for ($i = 0; $i < $doel; $i++) {
            try {
                $resultaat = $drafter->draftVolgende();
            } catch (Throwable $e) {
                $this->error('Schrijven mislukt: ' . $e->getMessage());

                return self::FAILURE;
            }

            $this->line($resultaat['melding']);
            if ($resultaat['status'] !== 'gedraft') {
                break;
            }
        }

        return self::SUCCESS;
    }
}
