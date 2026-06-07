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
        Schema::table('user_gamifications', function (Blueprint $table) {
            $table->index(['level', 'xp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_gamifications', function (Blueprint $table) {
            $table->dropIndex(['level', 'xp']);
        });
    }
};
