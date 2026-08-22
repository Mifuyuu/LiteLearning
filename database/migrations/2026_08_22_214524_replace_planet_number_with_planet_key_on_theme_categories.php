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
        // No-op on a fresh DB: the base migration already creates `planet_key` directly.
        // Only applies to an existing DB still on the old `planet_number` schema.
        if (! Schema::hasColumn('theme_categories', 'planet_number')) {
            return;
        }

        // Old numbered planet set (1-23) is fully replaced by the new named set (20 planets);
        // there's no valid mapping between them, so existing rows are cleared and reseeded.
        DB::table('theme_categories')->delete();

        Schema::table('theme_categories', function (Blueprint $table) {
            $table->string('planet_key', 30)->default('earth')->after('is_active');
            $table->dropColumn('planet_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('theme_categories', 'planet_key')) {
            return;
        }

        Schema::table('theme_categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('planet_number')->default(1)->after('is_active');
            $table->dropColumn('planet_key');
        });
    }
};
