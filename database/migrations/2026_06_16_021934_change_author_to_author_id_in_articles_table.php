<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('category_id')->constrained('users')->nullOnDelete();
        });

        // Migrate existing author string to author_id using DB query builder (no Eloquent models)
        $articles = DB::table('articles')->get();
        foreach ($articles as $article) {
            if (!empty($article->author)) {
                $user = DB::table('users')->where('name', $article->author)->first();
                if ($user) {
                    DB::table('articles')->where('id', $article->id)->update(['author_id' => $user->id]);
                } else {
                    Log::warning("Migration: Article ID {$article->id} has author '{$article->author}' which does not exist in users table. Setting author_id to null.");
                    DB::table('articles')->where('id', $article->id)->update(['author_id' => null]);
                }
            }
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('author')->nullable()->after('category_id');
        });

        // Restore authors name from author_id relationship
        $articles = DB::table('articles')->get();
        foreach ($articles as $article) {
            if ($article->author_id) {
                $user = DB::table('users')->where('id', $article->author_id)->first();
                if ($user) {
                    DB::table('articles')->where('id', $article->id)->update(['author' => $user->name]);
                }
            }
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');
        });
    }
};
