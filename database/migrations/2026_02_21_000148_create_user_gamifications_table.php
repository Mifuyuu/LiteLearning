<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_gamifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('coins')->default(0);
            $table->integer('xp')->default(0);
            $table->integer('level')->default(1);
            $table->timestamps();

            // A user should only have one gamification record
            $table->unique('user_id');
        });

        // Move existing data from users to user_gamifications
        $usersWithGamification = DB::table('users')
            ->where('coins', '>', 0)
            ->orWhere('xp', '>', 0)
            ->orWhere('level', '>', 1)
            ->get(['id', 'coins', 'xp', 'level', 'created_at', 'updated_at']);

        $gamificationData = [];
        foreach ($usersWithGamification as $user) {
            $gamificationData[] = [
                'user_id' => $user->id,
                'coins' => $user->coins,
                'xp' => $user->xp,
                'level' => $user->level,
                'created_at' => $user->updated_at,
                'updated_at' => $user->updated_at,
            ];
        }

        if (!empty($gamificationData)) {
            DB::table('user_gamifications')->insert($gamificationData);
        }

        // Drop columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['coins', 'xp', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->integer('coins')->default(0);
            $table->integer('xp')->default(0);
            $table->integer('level')->default(1);
        });

        // Restore data
        $gamifications = DB::table('user_gamifications')->get();
        foreach ($gamifications as $gamification) {
            DB::table('users')->where('id', $gamification->user_id)->update([
                'coins' => $gamification->coins,
                'xp' => $gamification->xp,
                'level' => $gamification->level,
            ]);
        }

        Schema::dropIfExists('user_gamifications');
    }
};
