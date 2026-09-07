<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Klantbehoud: beheernotities met een opvolgdatum (mini-CRM) en een
     * mail-logboek zodat automatische lifecycle-mails nooit dubbel gaan.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('beheer_notitie')->nullable();
            $table->timestamp('opvolgen_vanaf')->nullable();
        });

        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('soort');
            $table->timestamps();
            $table->unique(['user_id', 'soort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['beheer_notitie', 'opvolgen_vanaf']);
        });
    }
};
