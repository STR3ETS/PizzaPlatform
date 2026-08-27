<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Klantaccounts per pizzeria: dezelfde klant kan bij twee zaken een los account hebben.
        Schema::create('klanten', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('naam', 100);
            $table->string('email');
            $table->string('telefoon', 30)->nullable();
            $table->string('wachtwoord');
            $table->json('adres')->nullable();                 // laatst gebruikte bezorggegevens
            $table->unsignedInteger('punten')->default(0);     // spaarpunten op basis van bestelwaarde (later)
            $table->timestamps();
            $table->unique(['user_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klanten');
    }
};
