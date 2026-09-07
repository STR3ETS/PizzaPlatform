<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Een gepland blogonderwerp in de wachtrij van de content-machine */
#[Fillable(['topic', 'cluster', 'intent', 'priority', 'status', 'post_id'])]
class ContentTopic extends Model
{
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Idempotent claimen: zet planned -> drafting in exact één UPDATE.
     * Alleen als die query 1 rij raakte, mag de aanroeper draften.
     */
    public static function claimVolgende(): ?self
    {
        $kandidaat = self::where('status', 'planned')->orderBy('priority')->orderBy('id')->first();
        if (! $kandidaat) {
            return null;
        }

        $geclaimd = self::whereKey($kandidaat->id)->where('status', 'planned')->update(['status' => 'drafting']);

        return $geclaimd === 1 ? $kandidaat->refresh() : null;
    }
}
