<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Bewust leeg: accounts ontstaan via de onboarding, en op de server
     * draait Composer zonder dev-dependencies (dus zonder Faker).
     */
    public function run(): void
    {
        //
    }
}
