<?php

namespace App\Console\Commands;

use App\Models\ContentTopic;
use Illuminate\Console\Command;

class BlogPlan extends Command
{
    protected $signature = 'blog:plan';

    protected $description = '(Her)vul de onderwerpen-wachtrij met de brede, gededupliceerde seed-onderwerpen uit de brand-kit-clusters';

    public function handle(): int
    {
        $bestaand = ContentTopic::all()
            ->map(fn ($t) => $this->normaliseer($t->topic))
            ->flip();

        $nieuw = 0;
        foreach (config('seo-content.clusters') as $cluster => $topics) {
            foreach ($topics as $prioriteit => $topic) {
                $sleutel = $this->normaliseer($topic);
                if (isset($bestaand[$sleutel])) {
                    continue;
                }
                ContentTopic::create([
                    'topic' => $topic,
                    'cluster' => $cluster,
                    'intent' => str_contains(mb_strtolower($topic), 'kost') ? 'commercieel' : 'informationeel',
                    'priority' => min(9, $prioriteit + 2),
                ]);
                $bestaand[$sleutel] = true;
                $nieuw++;
            }
        }

        $this->info("Onderwerpen-wachtrij bijgewerkt: {$nieuw} nieuwe onderwerpen, " . ContentTopic::where('status', 'planned')->count() . ' gepland in totaal.');

        return self::SUCCESS;
    }

    private function normaliseer(string $topic): string
    {
        return preg_replace('/[^a-z0-9]+/', '', mb_strtolower($topic));
    }
}
