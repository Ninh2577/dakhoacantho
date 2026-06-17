<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
            });
        } catch (Exception $e) {
            // Already dropped in previous failed run
        }

        Schema::table('categories', function (Blueprint $table) {
            // Change parent_id to signed bigint first so it can support negative numbers (-1)
            $table->bigInteger('parent_id')->nullable()->default(-1)->change();
            $table->integer('order')->default(0)->after('parent_id');
        });

        // Now that the column is signed, we can safely update NULL values to -1
        DB::table('categories')->whereNull('parent_id')->update(['parent_id' => -1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        DB::table('categories')->where('parent_id', -1)->update(['parent_id' => null]);

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->default(null)->change();
        });

        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
            });
        } catch (Exception $e) {
            // Re-adding safety
        }
    }
};
