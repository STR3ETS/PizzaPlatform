<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Het abonnement (24,95 per maand) is verplicht om het dashboard te
     * kunnen gebruiken. Nu nog een simpele vlag; zodra Stripe gekoppeld is
     * hangt hier het echte abonnement via Stripe Billing achter.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('abonnement_actief')->default(false);
            $table->timestamp('abonnement_sinds')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['abonnement_actief', 'abonnement_sinds']);
        });
    }
};
