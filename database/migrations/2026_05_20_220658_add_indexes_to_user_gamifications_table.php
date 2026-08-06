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
        try {
            Schema::table('user_gamifications', function (Blueprint $table) {
                $table->index(['level', 'xp']);
            });
        } catch (\Exception) {
            // Index already exists (fresh install has it from base migration)
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('user_gamifications', function (Blueprint $table) {
                $table->dropIndex(['level', 'xp']);
            });
        } catch (\Exception) {
            // Index already dropped or never existed
        }
    }
};
