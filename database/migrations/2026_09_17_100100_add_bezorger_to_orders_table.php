<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Welke bezorger deze rit heeft opgepakt, en wanneer hij afgevinkt is
            $table->foreignId('bezorger_id')->nullable()->after('klant_id')->constrained('medewerkers')->nullOnDelete();
            $table->timestamp('bezorgd_om')->nullable()->after('eta_minuten');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bezorger_id');
            $table->dropColumn('bezorgd_om');
        });
    }
};
