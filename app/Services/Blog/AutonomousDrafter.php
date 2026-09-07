<?php

namespace App\Services\Blog;

use App\Models\ContentTopic;
use App\Models\Post;
use Illuminate\Support\Str;
use Throwable;

/**
 * Draft één topic volledig: idempotent claimen -> pipeline -> needs_review-draft.
 * De REM: er wordt nooit gedraft als de review-wachtrij al vol zit.
 */
class AutonomousDrafter
{
    public function __construct(
        private ContentPipeline $pipeline,
        private ContentReviewer $reviewer,
        private CoverService $covers,
    ) {
    }

    public function reviewWachtrijVol(): bool
    {
        return Post::where('generation_status', 'needs_review')->count() >= (int) config('seo-content.review_queue_cap');
    }

    /** @return array{status: string, post?: Post, melding: string} */
    public function draftVolgende(): array
    {
        if ($this->reviewWachtrijVol()) {
            return ['status' => 'overgeslagen', 'melding' => 'Review-wachtrij zit vol (cap ' . config('seo-content.review_queue_cap') . '); eerst beoordelen, dan pas verder schrijven.'];
        }

        $topic = ContentTopic::claimVolgende();
        if (! $topic) {
            return ['status' => 'leeg', 'melding' => 'Geen geplande onderwerpen in de wachtrij. Vul eerst de onderwerpenlijst aan.'];
        }

        try {
            $post = $this->draft($topic);
            $topic->update(['status' => 'drafted', 'post_id' => $post->id]);

            return ['status' => 'gedraft', 'post' => $post, 'melding' => 'Concept klaar voor review: ' . $post->title];
        } catch (Throwable $e) {
            $topic->update(['status' => 'planned']); // claim netjes teruggeven
            throw $e;
        }
    }

    private function draft(ContentTopic $topic): Post
    {
        $doelWoorden = (int) config('seo-content.writing.doel_woorden');
        $briefing = $this->pipeline->makeBriefing($topic->topic, $topic->cluster);
        $outline = $this->pipeline->aiOutline($briefing, $doelWoorden);

        $secties = [];
        $koppen = [];
        foreach (($outline['secties'] ?? []) as $sectie) {
            $secties[] = $this->pipeline->writeSection($briefing, $sectie, implode(' | ', $koppen));
            $koppen[] = $sectie['kop'] ?? '';
        }

        $faq = [];
        foreach (array_slice($outline['faq'] ?? [], 0, 4) as $item) {
            $faq[] = [
                'vraag' => $item['vraag'] ?? '',
                'antwoord' => $this->pipeline->beantwoordFaq($briefing, $item['vraag'] ?? ''),
            ];
        }

        $intro = $this->pipeline->schrijfIntro($briefing, $outline);
        $html = $this->pipeline->assembleWithCtas($outline['h1'] ?? $topic->topic, $intro, $secties, $faq);

        $metaDescription = (string) ($outline['meta_description'] ?? '');
        $metrics = $this->pipeline->runValidation($html, $topic->topic, $metaDescription);
        if ($metrics['issues']) {
            $html = $this->pipeline->aiRefineHtml($html, $metrics['issues']);
            $metrics = $this->pipeline->runValidation($html, $topic->topic, $metaDescription);
        }

        $titel = (string) ($outline['h1'] ?? $topic->topic);
        $slug = $this->uniekeSlug($titel);
        $cover = $this->covers->kies($slug, $topic->cluster, Post::count(), $titel);

        $post = Post::create([
            'title' => $titel,
            'slug' => $slug,
            'content' => $html,
            'excerpt' => Str::limit(trim(strip_tags(Str::before($html, '<h2'))), 180),
            'meta_title' => (string) ($outline['meta_title'] ?? Str::limit($titel, 58, '')),
            'meta_description' => $metaDescription,
            'og_title' => (string) ($outline['meta_title'] ?? $titel),
            'og_description' => $metaDescription,
            'target_keyword' => $topic->topic,
            'cluster' => $topic->cluster,
            'cover' => $cover['pad'],
            'generation_status' => 'needs_review',
            'briefing' => ['keyword' => $briefing['keyword'], 'cluster' => $briefing['cluster'], 'cover_credit' => $cover['credit'], 'cover_bron' => $cover['bron']],
            'outline' => $outline,
            'sections' => ['koppen' => $koppen],
            'validator_metrics' => $metrics,
        ]);

        /* De gate draait direct mee zodat de wachtrij het verdict al toont */
        $post->update(['ai_review' => $this->reviewer->review($post)]);

        return $post;
    }

    private function uniekeSlug(string $titel): string
    {
        $basis = Str::slug(Str::limit($titel, 70, ''));
        $slug = $basis;
        $i = 2;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $basis . '-' . $i++;
        }

        return $slug;
    }
}
