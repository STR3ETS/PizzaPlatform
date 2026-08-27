<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('klanten', function (Blueprint $table) {
            $table->json('adressen')->nullable()->after('adres');   // het adresboek van de klant
        });
    }

    public function down(): void
    {
        Schema::table('klanten', function (Blueprint $table) {
            $table->dropColumn('adressen');
        });
    }
};
