<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('user_achievements', 'is_displayed')) {
            return;
        }

        Schema::table('user_achievements', function (Blueprint $table) {
            $table->boolean('is_displayed')->default(false)->after('unlocked_at');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('user_achievements', 'is_displayed')) {
            return;
        }

        Schema::table('user_achievements', function (Blueprint $table) {
            $table->dropColumn('is_displayed');
        });
    }
};
