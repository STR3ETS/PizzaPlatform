<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Een ingevuld feedbackformulier, gekoppeld aan de zaak die het invulde */
#[Fillable(['user_id', 'context', 'antwoord', 'toelichting'])]
class Feedback extends Model
{
    protected $table = 'feedback';

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
