<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'active_name_color')) {
            return;
        }

        DB::transaction(function () {
            // Mark active name_color items (SQLite + MySQL compatible correlated subquery)
            DB::statement("
                UPDATE user_store_items
                SET is_active = 1
                WHERE EXISTS (
                    SELECT 1
                    FROM store_items
                    INNER JOIN users ON users.id = user_store_items.user_id
                    WHERE store_items.id = user_store_items.store_item_id
                      AND store_items.type = 'name_color'
                      AND store_items.value = users.active_name_color
                )
            ");

            // Mark active avatar_frame items (SQLite + MySQL compatible correlated subquery)
            DB::statement("
                UPDATE user_store_items
                SET is_active = 1
                WHERE EXISTS (
                    SELECT 1
                    FROM store_items
                    INNER JOIN users ON users.id = user_store_items.user_id
                    WHERE store_items.id = user_store_items.store_item_id
                      AND store_items.type = 'avatar_frame'
                      AND store_items.value = users.active_avatar_frame
                )
            ");
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['active_name_color', 'active_avatar_frame']);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'active_name_color')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('active_name_color')->nullable()->after('bio');
            $table->string('active_avatar_frame')->nullable()->after('active_name_color');
        });

        // Restore values from user_store_items back to users (SQLite-compatible)
        DB::statement("
            UPDATE users
            SET active_name_color = (
                SELECT si.value
                FROM user_store_items usi
                INNER JOIN store_items si ON si.id = usi.store_item_id
                WHERE usi.user_id = users.id
                  AND si.type = 'name_color'
                  AND usi.is_active = 1
                LIMIT 1
            )
        ");

        DB::statement("
            UPDATE users
            SET active_avatar_frame = (
                SELECT si.value
                FROM user_store_items usi
                INNER JOIN store_items si ON si.id = usi.store_item_id
                WHERE usi.user_id = users.id
                  AND si.type = 'avatar_frame'
                  AND usi.is_active = 1
                LIMIT 1
            )
        ");
    }
};
