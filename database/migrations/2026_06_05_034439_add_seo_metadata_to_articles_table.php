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
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'focus_keyword')) {
                $table->string('focus_keyword')->nullable();
            }
            if (!Schema::hasColumn('articles', 'seo_slug')) {
                $table->string('seo_slug')->nullable();
            }
            if (!Schema::hasColumn('articles', 'canonical_url')) {
                $table->string('canonical_url')->nullable();
            }
            if (!Schema::hasColumn('articles', 'robots_index')) {
                $table->boolean('robots_index')->default(true);
            }
            if (!Schema::hasColumn('articles', 'robots_follow')) {
                $table->boolean('robots_follow')->default(true);
            }
            if (!Schema::hasColumn('articles', 'og_title')) {
                $table->string('og_title')->nullable();
            }
            if (!Schema::hasColumn('articles', 'og_description')) {
                $table->text('og_description')->nullable();
            }
            if (!Schema::hasColumn('articles', 'og_image')) {
                $table->string('og_image')->nullable();
            }
            if (!Schema::hasColumn('articles', 'twitter_title')) {
                $table->string('twitter_title')->nullable();
            }
            if (!Schema::hasColumn('articles', 'twitter_description')) {
                $table->text('twitter_description')->nullable();
            }
            if (!Schema::hasColumn('articles', 'twitter_image')) {
                $table->string('twitter_image')->nullable();
            }
            if (!Schema::hasColumn('articles', 'seo_score')) {
                $table->unsignedTinyInteger('seo_score')->default(0);
            }
            if (!Schema::hasColumn('articles', 'seo_checks')) {
                $table->json('seo_checks')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $cols = [
                'focus_keyword', 'seo_slug', 'canonical_url', 'robots_index', 'robots_follow',
                'og_title', 'og_description', 'og_image', 'twitter_title', 'twitter_description',
                'twitter_image', 'seo_score', 'seo_checks'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('articles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
