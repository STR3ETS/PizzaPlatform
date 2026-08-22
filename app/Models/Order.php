<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'nummer', 'klant', 'items', 'totaal', 'status', 'type', 'adres', 'opmerking', 'eta_minuten', 'is_demo'])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'items' => 'array',
            'is_demo' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
