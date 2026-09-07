<?php

namespace App\Console\Commands;

use App\Mail\LifecycleMail;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * De automatische klantreis-mails. Draait dagelijks via de scheduler; het
 * mail-logboek zorgt dat elke soort maar één keer per gebruiker gaat.
 */
class StuurLifecycleMails extends Command
{
    protected $signature = 'mails:lifecycle';

    protected $description = 'Verstuur de automatische lifecycle-mails (activatie, proef loopt af, winback)';

    public function handle(): int
    {
        $verstuurd = 0;

        User::where('is_admin', false)->get()->each(function (User $user) use (&$verstuurd) {
            // Demo- en testaccounts slaan we over
            if (str_ends_with($user->email, '@demo.mijnpizzeria.nl') || $user->email === 'demo@pizzeria.nl') {
                return;
            }

            $ob = $user->onboarding ?? [];
            $soort = null;

            if (! $user->abonnement_actief && $user->proef_tot?->isPast()) {
                // Proef verlopen zonder abonnement: winback op dag 3 en dag 14
                if ($user->proef_tot->diffInDays(now()) >= 14) {
                    $soort = 'winback-2';
                } elseif ($user->proef_tot->diffInDays(now()) >= 3) {
                    $soort = 'winback-1';
                }
            } elseif ($user->inProefperiode()) {
                if ($user->proef_tot->isBefore(now()->addDay())) {
                    $soort = 'proef-laatste-dag';
                } elseif ($user->proef_tot->isBefore(now()->addDays(7))) {
                    $soort = 'proef-bijna-af';
                } elseif (empty($ob['setup_compleet']) && $user->created_at->diffInDays(now()) >= 3) {
                    $soort = 'zaak-afmaken';
                }
            }

            if ($soort === null || MailLog::alVerstuurd($user->id, $soort)) {
                return;
            }

            try {
                Mail::to($user->email)->send(new LifecycleMail($user, $soort));
                MailLog::create(['user_id' => $user->id, 'soort' => $soort]);
                $this->line($user->email . ' kreeg: ' . $soort);
                $verstuurd++;
            } catch (\Throwable $e) {
                report($e);
                $this->warn($user->email . ' mislukt: ' . $e->getMessage());
            }
        });

        $this->info($verstuurd . ' lifecycle-mail(s) verstuurd.');

        return self::SUCCESS;
    }
}
