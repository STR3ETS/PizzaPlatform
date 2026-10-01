<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Teamleden per pizzeria: bezorgers en keukenhulp. Ze loggen op hun eigen
        // scherm in (/bezorger), niet op het dashboard van de zaak zelf.
        Schema::create('medewerkers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('naam', 80);
            $table->string('email');
            $table->string('telefoon', 30)->nullable();
            $table->string('wachtwoord')->nullable();            // pas gevuld zodra de uitnodiging geactiveerd is
            $table->string('rol', 20)->default('bezorger');      // bezorger of keuken
            $table->boolean('actief')->default(true);
            $table->string('uitnodiging_token', 64)->nullable()->unique();
            $table->timestamp('uitgenodigd_op')->nullable();
            $table->timestamp('laatst_actief_op')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medewerkers');
    }
};
