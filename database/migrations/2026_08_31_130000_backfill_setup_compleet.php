<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Accounts van voor de korte registratie doorliepen de oude, volledige
     * onboarding: die zijn dus al compleet. Nieuwe accounts krijgen de vlag
     * pas na het aanvullen van de vervolg-stappen vanuit het dashboard.
     */
    public function up(): void
    {
        foreach (DB::table('users')->whereNotNull('onboarding')->get(['id', 'onboarding']) as $rij) {
            $ob = json_decode($rij->onboarding, true) ?: [];
            if (empty($ob['setup_compleet'])) {
                $ob['setup_compleet'] = true;
                DB::table('users')->where('id', $rij->id)->update(['onboarding' => json_encode($ob)]);
            }
        }
    }

    public function down(): void
    {
        // Bewust leeg: de vlag terugdraaien zou gebruikers onterecht op slot zetten
    }
};
