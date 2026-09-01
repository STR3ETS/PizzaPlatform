<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Play before you pay: 30 dagen gratis proberen, daarna is het abonnement
     * verplicht. Bestaande accounts zonder abonnement krijgen ook een verse
     * proefmaand, zodat niemand ineens buitengesloten wordt.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('proef_tot')->nullable();
        });

        DB::table('users')->where('abonnement_actief', false)->update(['proef_tot' => now()->addDays(30)]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('proef_tot');
        });
    }
};
