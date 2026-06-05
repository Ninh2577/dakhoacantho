<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('gender')->nullable(); // male, female, other
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('source')->nullable(); // 'Tư vấn online', 'Giới thiệu', 'Tự đến'
            $table->string('status')->default('new'); // new, contacted, booked, visited, cancelled, archived
            $table->text('notes')->nullable();
            $table->text('internal_note')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
