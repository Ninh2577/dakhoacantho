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
        Schema::table('file_scan_results', function (Blueprint $table) {
            $table->string('check_key', 100)->nullable()->index();
            $table->string('check_group', 100)->nullable()->index();
            $table->string('status', 50)->nullable()->index();
            $table->string('target', 255)->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamp('reviewed_at')->nullable()->index();
            $table->timestamp('ignored_at')->nullable()->index();
            $table->text('ignored_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_scan_results', function (Blueprint $table) {
            $table->dropColumn([
                'check_key',
                'check_group',
                'status',
                'target',
                'recommendation',
                'reviewed_at',
                'ignored_at',
                'ignored_reason',
            ]);
        });
    }
};
