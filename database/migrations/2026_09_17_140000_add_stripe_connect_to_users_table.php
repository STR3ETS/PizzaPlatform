<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Stripe Connect: elke pizzeria krijgt een eigen (Express) account, zodat het geld
            // van bestellingen rechtstreeks naar de zaak gaat en nooit via ons loopt.
            $table->string('stripe_account_id')->nullable()->after('stripe_subscription_id');
            $table->boolean('stripe_uitbetalingen')->default(false)->after('stripe_account_id');   // Stripe laat uitbetalen toe
            $table->boolean('stripe_gegevens_nodig')->default(false)->after('stripe_uitbetalingen'); // Stripe wacht nog op gegevens
            $table->timestamp('stripe_gecontroleerd_op')->nullable()->after('stripe_gegevens_nodig');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_account_id', 'stripe_uitbetalingen', 'stripe_gegevens_nodig', 'stripe_gecontroleerd_op']);
        });
    }
};
