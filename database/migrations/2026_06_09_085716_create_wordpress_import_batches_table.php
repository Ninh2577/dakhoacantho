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
        Schema::create('wordpress_import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('original_file_name');
            $table->string('old_domain');
            $table->string('media_mode'); // keep_remote, public_wp_uploads, storage_uploads
            $table->string('local_media_base_path')->nullable();
            $table->json('import_post_types');
            $table->json('import_statuses');
            $table->string('duplicate_mode'); // skip, update, unique
            $table->boolean('dry_run')->default(false);
            $table->integer('limit')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed, cancelled
            $table->integer('total_items')->default(0);
            $table->integer('processed_items')->default(0);
            $table->integer('imported_items')->default(0);
            $table->integer('updated_items')->default(0);
            $table->integer('skipped_items')->default(0);
            $table->integer('failed_items')->default(0);
            $table->integer('missing_media_items')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wordpress_import_batches');
    }
};

