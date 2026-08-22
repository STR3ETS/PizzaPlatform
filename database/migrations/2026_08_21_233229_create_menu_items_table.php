<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('categorie', 50);
            $table->string('naam', 100);
            $table->string('beschrijving', 300)->nullable();
            $table->unsignedInteger('prijs');
            $table->string('icoon', 16)->nullable();
            $table->json('ingredienten')->nullable();
            $table->json('allergenen')->nullable();
            $table->json('opties')->nullable();
            $table->boolean('actief')->default(true);
            $table->unsignedSmallInteger('volgorde')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
