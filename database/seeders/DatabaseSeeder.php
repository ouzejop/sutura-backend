<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Dr. Ndiaye',
            'email' => 'medecin@sutura.sn',
            'password' => bcrypt('password'),
            'role' => 'medecin',
        ]);

        User::factory()->create([
            'name' => 'Pharmacie Diallo',
            'email' => 'pharmacien@sutura.sn',
            'password' => bcrypt('password'),
            'role' => 'pharmacien',
        ]);

        User::factory()->create([
            'name' => 'Admin Sutura',
            'email' => 'admin@sutura.sn',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
