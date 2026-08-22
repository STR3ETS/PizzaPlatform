<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'categorie', 'naam', 'beschrijving', 'prijs', 'icoon', 'ingredienten', 'allergenen', 'opties', 'actief', 'volgorde'])]
class MenuItem extends Model
{
    protected function casts(): array
    {
        return [
            'ingredienten' => 'array',
            'allergenen' => 'array',
            'opties' => 'array',
            'actief' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
