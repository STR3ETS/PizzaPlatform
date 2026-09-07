<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Welke automatische mail is wanneer naar wie gegaan; voorkomt dubbele mails */
#[Fillable(['user_id', 'soort'])]
class MailLog extends Model
{
    /** Is deze mailsoort al eens naar deze gebruiker gegaan? */
    public static function alVerstuurd(int $userId, string $soort): bool
    {
        return static::where('user_id', $userId)->where('soort', $soort)->exists();
    }
}
