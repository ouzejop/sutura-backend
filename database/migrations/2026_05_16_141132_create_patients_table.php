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
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('pseudo')->unique();
            $table->string('avatar')->nullable();
            $table->integer('age');
            $table->enum('sex', ['male', 'female']);
            $table->text('phone')->nullable(); // Chiffré
            $table->text('emergency_contact')->nullable(); // Chiffré
            $table->text('real_name')->nullable(); // Chiffré
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
