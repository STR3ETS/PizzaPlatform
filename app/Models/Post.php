<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Een blogartikel van de marketingsite, geschreven door de content-machine */
#[Fillable([
    'title', 'slug', 'content', 'excerpt', 'meta_title', 'meta_description',
    'og_title', 'og_description', 'target_keyword', 'cluster', 'cover',
    'generation_status', 'published_at', 'briefing', 'outline', 'sections',
    'schema_json', 'validator_metrics', 'ai_review', 'last_refreshed_at',
])]
class Post extends Model
{
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'last_refreshed_at' => 'datetime',
            'briefing' => 'array',
            'outline' => 'array',
            'sections' => 'array',
            'schema_json' => 'array',
            'validator_metrics' => 'array',
            'ai_review' => 'array',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('generation_status', 'published');
    }

    public function getLeestijdAttribute(): int
    {
        return max(1, (int) round(str_word_count(strip_tags((string) $this->content)) / 220));
    }
}
