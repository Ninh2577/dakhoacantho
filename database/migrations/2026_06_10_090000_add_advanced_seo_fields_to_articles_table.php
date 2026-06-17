<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * All columns added safely with IF NOT EXISTS checks.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // excerpt — short summary for SEO and excerpts
            if (! Schema::hasColumn('articles', 'excerpt')) {
                $table->text('excerpt')->nullable()->after('content');
            }

            // published_at — precise publish timestamp
            if (! Schema::hasColumn('articles', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_published');
            }

            // schema_type — controls JSON-LD @type for this article
            if (! Schema::hasColumn('articles', 'schema_type')) {
                $table->string('schema_type', 50)->default('Article')->after('published_at');
            }

            // schema_json — optional custom schema override
            if (! Schema::hasColumn('articles', 'schema_json')) {
                $table->json('schema_json')->nullable()->after('schema_type');
            }
        });
    }

    /**
     * Reverse the migrations safely.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $cols = ['excerpt', 'published_at', 'schema_type', 'schema_json'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('articles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
