<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop stale columns from users table:
     * - coins, xp, level → moved to user_gamifications table
     * - theme → never referenced in application code
     *
     * Each column is checked individually for safety — some environments
     * may have already dropped these columns via a different migration path.
     */
    public function up(): void
    {
        $columnsToDrop = collect(['coins', 'xp', 'level', 'theme'])
            ->filter(fn (string $col) => Schema::hasColumn('users', $col))
            ->values()
            ->all();

        if (!empty($columnsToDrop)) {
            Schema::table('users', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'coins')) {
                $table->integer('coins')->default(0);
            }
            if (!Schema::hasColumn('users', 'xp')) {
                $table->integer('xp')->default(0);
            }
            if (!Schema::hasColumn('users', 'level')) {
                $table->integer('level')->default(1);
            }
            if (!Schema::hasColumn('users', 'theme')) {
                $table->string('theme')->default('system');
            }
        });
    }
};
