<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Zonder Faker (draait ook op de server zonder dev-dependencies).
     */
    public function run(): void
    {
        $this->call(PizzeriaSeeder::class);
    }
}
