<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->string('target_role')->default('student')->after('is_active');
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->string('target_role')->default('student')->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropColumn('target_role');
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn('target_role');
        });
    }
};
