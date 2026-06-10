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
        Schema::create('wordpress_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('wordpress_import_batches')->cascadeOnDelete();
            $table->string('source_post_id')->nullable()->index();
            $table->string('source_post_type')->nullable();
            $table->string('source_slug')->nullable()->index();
            $table->text('source_title')->nullable();
            $table->string('target_type')->nullable();
            $table->integer('target_id')->nullable();
            $table->string('action')->index(); // imported, updated, skipped, failed, dry_run, missing_media
            $table->string('status')->index(); // success, warning, error
            $table->text('message')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wordpress_import_logs');
    }
};

