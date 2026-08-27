<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('punten')->default(0)->after('totaal');            // gespaard met deze bestelling
            $table->unsignedInteger('punten_gebruikt')->default(0)->after('punten');   // ingewisseld voor korting
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['punten', 'punten_gebruikt']);
        });
    }
};
