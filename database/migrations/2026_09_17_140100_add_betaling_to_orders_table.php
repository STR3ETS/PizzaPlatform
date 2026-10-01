<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // open = wacht op betaling (nog niet zichtbaar voor de zaak), betaald = rond,
            // verlopen = klant heeft de betaling afgebroken
            $table->string('betaal_status', 20)->default('betaald')->after('status');
            $table->string('stripe_session_id')->nullable()->after('betaal_status');
            $table->string('stripe_payment_intent')->nullable()->after('stripe_session_id');
            $table->timestamp('betaald_om')->nullable()->after('stripe_payment_intent');
            $table->index(['user_id', 'betaal_status']);
        });

        // Alles wat er al stond is in het oude, betaalloze tijdperk geplaatst: die tellen als betaald.
        \Illuminate\Support\Facades\DB::table('orders')->update(['betaal_status' => 'betaald']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'betaal_status']);
            $table->dropColumn(['betaal_status', 'stripe_session_id', 'stripe_payment_intent', 'betaald_om']);
        });
    }
};
