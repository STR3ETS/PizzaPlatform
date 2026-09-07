<?php

namespace App\Console\Commands;

use App\Mail\BeheerDigestMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/** Dagelijkse samenvatting naar het beheer; alleen als er iets te melden is */
class StuurBeheerDigest extends Command
{
    protected $signature = 'mails:digest';

    protected $description = 'Verstuur de dagelijkse beheer-samenvatting';

    public function handle(): int
    {
        if (! config('mail.beheer')) {
            $this->warn('Geen BEHEER_MAIL ingesteld.');

            return self::SUCCESS;
        }

        $zaken = User::where('is_admin', false)->get();
        $naam = fn (User $u) => ($u->onboarding['name'] ?? $u->name) . (($u->onboarding['city'] ?? '') !== '' ? ' (' . $u->onboarding['city'] . ')' : '');

        $besteldRecent = Order::where('is_demo', false)->where('created_at', '>=', now()->subDays(14))
            ->distinct()->pluck('user_id')->flip();

        $digest = [
            'aanmeldingen' => $zaken->filter(fn ($u) => $u->created_at->isAfter(now()->subDay()))
                ->map($naam)->values()->all(),
            'proefLooptAf' => $zaken->filter(fn ($u) => $u->inProefperiode() && $u->proef_tot->isBefore(now()->addDays(7)))
                ->map(fn ($u) => $naam($u) . ', nog ' . max(1, (int) ceil(now()->diffInDays($u->proef_tot, false))) . ' dagen')->values()->all(),
            'netVerlopen' => $zaken->filter(fn ($u) => ! $u->abonnement_actief && $u->proef_tot && $u->proef_tot->isBetween(now()->subDay(), now()))
                ->map($naam)->values()->all(),
            'stil' => $zaken->filter(fn ($u) => $u->abonnement_actief && ! isset($besteldRecent[$u->id]))
                ->map($naam)->values()->all(),
        ];

        if (collect($digest)->every(fn ($lijst) => $lijst === [])) {
            $this->info('Niets te melden vandaag, geen digest verstuurd.');

            return self::SUCCESS;
        }

        Mail::to(config('mail.beheer'))->send(new BeheerDigestMail($digest));
        $this->info('Digest verstuurd naar ' . config('mail.beheer'));

        return self::SUCCESS;
    }
}
