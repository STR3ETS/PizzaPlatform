<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Een bon in de wachtrij van de bonprinter. De printer haalt 'm op (opgehaald), meldt
 * daarna of het lukte (geprint of mislukt). Blijft een opgehaalde bon te lang zonder
 * bericht, dan gaat hij terug in de wachtrij, tot drie pogingen.
 */
#[Fillable(['user_id', 'order_id', 'soort', 'inhoud', 'status', 'pogingen', 'fout', 'opgehaald_om', 'geprint_om'])]
class PrintJob extends Model
{
    protected function casts(): array
    {
        return [
            'inhoud' => 'array',
            'opgehaald_om' => 'datetime',
            'geprint_om' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** Het id dat de printer terugstuurt: 1 tot 30 tekens, alleen letters, cijfers, _ - . */
    public function printjobid(): string
    {
        return 'bon-' . $this->id;
    }
}
