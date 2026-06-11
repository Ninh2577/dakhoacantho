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
            $indexes = Schema::getIndexListing('articles');
            
            if (!in_array('articles_slug_index', $indexes)) {
                $table->index('slug', 'articles_slug_index');
            }
            if (!in_array('articles_is_published_index', $indexes)) {
                $table->index('is_published', 'articles_is_published_index');
            }
            if (!in_array('articles_published_at_index', $indexes)) {
                $table->index('published_at', 'articles_published_at_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $indexes = Schema::getIndexListing('articles');
            
            if (in_array('articles_slug_index', $indexes)) {
                $table->dropIndex('articles_slug_index');
            }
            if (in_array('articles_is_published_index', $indexes)) {
                $table->dropIndex('articles_is_published_index');
            }
            if (in_array('articles_published_at_index', $indexes)) {
                $table->dropIndex('articles_published_at_index');
            }
        });
    }
};
