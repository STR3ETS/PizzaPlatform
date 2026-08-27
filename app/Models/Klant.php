<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'naam', 'email', 'telefoon', 'wachtwoord', 'adres', 'adressen', 'punten'])]
#[Hidden(['wachtwoord'])]
class Klant extends Model
{
    protected $table = 'klanten';

    protected function casts(): array
    {
        return [
            'adres' => 'array',
            'adressen' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
