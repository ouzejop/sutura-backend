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
        Schema::table('consultations', function (Blueprint $table) {
            $table->foreignUuid('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, active, completed
            $table->text('symptoms')->nullable();
            $table->string('urgency')->default('Basse'); // Basse, Moyenne, Haute
            $table->integer('escalation_level')->default(1);
            $table->string('category')->nullable();
            $table->text('ai_summary')->nullable();
            $table->timestamp('started_at')->nullable();
        });

        Schema::table('consultation_messages', function (Blueprint $table) {
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            $table->string('sender_type'); // 'patient', 'doctor', 'system'
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->text('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultation_messages', function (Blueprint $table) {
            $table->dropForeign(['consultation_id']);
            $table->dropColumn(['consultation_id', 'sender_type', 'sender_id', 'content']);
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['doctor_id']);
            $table->dropColumn([
                'patient_id', 'doctor_id', 'status', 'symptoms', 'urgency', 
                'escalation_level', 'category', 'ai_summary', 'started_at'
            ]);
        });
    }
};
