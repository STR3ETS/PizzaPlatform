<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Zet een complete demo-omgeving neer: de beheerder, de showcase-zaak
     * (Pizzeria Sole), twaalf pizzeria's in alle levensfases met bestellingen,
     * klanten en feedback, plus een gevulde blog. Zonder Faker, draait dus
     * ook op de server zonder dev-dependencies.
     */
    public function run(): void
    {
        // De beheerder houdt zijn bestaande wachtwoord (hash overgenomen uit de vorige database)
        User::create([
            'name' => 'Beheerder',
            'email' => 'admin@platform.test',
            'password' => '$2y$12$CaWX5qoadw22tp59AgjxV.uMmiBJHPlRmhOu4pDskfQvAOng21ZB6',
        ])->forceFill([
            'is_admin' => true,
            'intro_seen' => true,
        ])->save();

        // Het vaste demo-beheeraccount om aan geïnteresseerden te laten zien
        User::create([
            'name' => 'Demo Beheer',
            'email' => 'demo-beheer@mijnpizzeria.nl',
            'password' => Hash::make('Demo1234!'),
        ])->forceFill([
            'is_admin' => true,
            'intro_seen' => true,
        ])->save();

        $this->call(PizzeriaSeeder::class);
        $this->call(BlogSeeder::class);
    }
}
