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
        Schema::table('classroom_theme_categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('planet_number')->default(1)->after('sort_order');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('theme_category_id')->nullable()->constrained('classroom_theme_categories')->nullOnDelete()->after('theme_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['theme_category_id']);
            $table->dropColumn('theme_category_id');
        });

        Schema::table('classroom_theme_categories', function (Blueprint $table) {
            $table->dropColumn('planet_number');
        });
    }
};
