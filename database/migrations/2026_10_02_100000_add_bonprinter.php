<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * De bonprinter van de zaak. Per zaak een geheime token en de instellingen, plus een
 * wachtrij met bonnen. Die worden opgehaald door de printer zelf (Epson Server Direct
 * Print, /printer/{token}) of door het hulpprogramma in de zaak (bon:agent, dat ze als
 * ESC/POS naar elke netwerkprinter op poort 9100 stuurt).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('printer_token', 40)->nullable()->unique()->after('stripe_gecontroleerd_op');
            $table->boolean('printer_aan')->default(false)->after('printer_token');
            $table->json('printer_opties')->nullable()->after('printer_aan');       // keukenbon, klantbon, kopieen, breedte
            $table->string('printer_naam', 60)->nullable()->after('printer_opties'); // de naam die de printer meestuurt
            $table->timestamp('printer_gezien_om')->nullable()->after('printer_naam'); // laatste keer dat de printer langskwam
        });

        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('soort', 20);                       // keuken, klant, test
            $table->json('inhoud');                            // de bon als stappen (zie App\Support\Bon), printer-onafhankelijk
            $table->string('status', 20)->default('wacht');    // wacht, opgehaald, geprint, mislukt
            $table->unsignedTinyInteger('pogingen')->default(0);
            $table->string('fout', 60)->nullable();            // de foutcode van de printer, als het misging
            $table->timestamp('opgehaald_om')->nullable();
            $table->timestamp('geprint_om')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['printer_token', 'printer_aan', 'printer_opties', 'printer_naam', 'printer_gezien_om']);
        });
    }
};
