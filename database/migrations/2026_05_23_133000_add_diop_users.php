<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        User::firstOrCreate(
            ['email' => 'diopousmane779@gmail.com'],
            [
                'name' => 'Dr Ousmane Diop',
                'password' => Hash::make('Padiop00'),
                'role' => 'medecin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'diopousmane7799@gmail.com'],
            [
                'name' => 'Pharmacien Ousmane Diop',
                'password' => Hash::make('Padiop00'),
                'role' => 'pharmacien',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::whereIn('email', [
            'diopousmane779@gmail.com',
            'diopousmane7799@gmail.com'
        ])->delete();
    }
};
