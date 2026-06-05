<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('symptoms');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()->after('notes');
            // patient_id added after patients table exists
            $table->unsignedBigInteger('patient_id')->nullable()->after('assigned_to');
            $table->timestamp('converted_to_patient_at')->nullable()->after('patient_id');
        });

        // Add FK constraint for patient_id after patients table exists
        Schema::table('consultations', function (Blueprint $table) {
            $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['patient_id']);
            $table->dropColumn(['notes', 'assigned_to', 'patient_id', 'converted_to_patient_at']);
        });
    }
};
