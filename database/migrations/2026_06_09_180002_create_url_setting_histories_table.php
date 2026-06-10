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
        Schema::create('url_setting_histories', function (Blueprint $table) {
            $table->id();
            $table->string('old_article_pattern')->nullable();
            $table->string('new_article_pattern');
            $table->string('old_category_pattern')->nullable();
            $table->string('new_category_pattern');
            $table->integer('conflict_count')->default(0);
            $table->integer('redirect_count')->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('total_items')->default(0);
            $table->integer('processed_items')->default(0);
            $table->integer('updated_items')->default(0);
            $table->integer('failed_items')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_setting_histories');
    }
};
