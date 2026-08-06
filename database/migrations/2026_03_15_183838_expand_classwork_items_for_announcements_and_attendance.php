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
        if (!Schema::hasColumn('announcements', 'user_id')) {
            return;
        }

        // 1. Alter classwork_items.type enum to include 'announcement' and 'attendance'
        if (DB::getDriverName() === 'sqlite') {
            // SQLite enum() generates a CHECK constraint that can't be altered.
            // Recreate the table without the restrictive CHECK so new types are accepted.
            // Use legacy_alter_table=ON so that RENAME does NOT rewrite FK references in
            // other tables (assignments, materials) — they must keep pointing to "classwork_items".
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('PRAGMA legacy_alter_table = ON');
            DB::statement('ALTER TABLE classwork_items RENAME TO classwork_items_old');
            DB::statement('
                CREATE TABLE classwork_items (
                    "id" integer primary key autoincrement not null,
                    "type" varchar not null,
                    "classroom_id" integer not null,
                    "user_id" integer not null,
                    "topic_id" integer,
                    "title" varchar not null,
                    "slug" varchar not null,
                    "description" text,
                    "created_at" datetime,
                    "updated_at" datetime,
                    foreign key("classroom_id") references "classrooms"("id") on delete cascade,
                    foreign key("user_id") references "users"("id") on delete cascade,
                    foreign key("topic_id") references "topics"("id") on delete set null
                )
            ');
            DB::statement('INSERT INTO classwork_items SELECT * FROM classwork_items_old');
            DB::statement('DROP TABLE classwork_items_old');
            // Re-create the unique index on slug
            DB::statement('CREATE UNIQUE INDEX classwork_items_slug_unique ON classwork_items ("slug")');
            DB::statement('PRAGMA legacy_alter_table = OFF');
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement("ALTER TABLE classwork_items MODIFY COLUMN type ENUM('assignment','material','announcement','attendance') NOT NULL");
        }

        // 2. Add classwork_item_id FK to announcements (nullable first)
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('classwork_item_id')->nullable()->after('id')->constrained('classwork_items')->cascadeOnDelete();
        });

        // 3. Add classwork_item_id FK to attendance_sessions (nullable first)
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->foreignId('classwork_item_id')->nullable()->after('id')->constrained('classwork_items')->cascadeOnDelete();
        });

        // 4. Backfill classwork_items rows for existing announcements
        DB::statement("
            INSERT INTO classwork_items (type, classroom_id, user_id, topic_id, title, slug, description, created_at, updated_at)
            SELECT
                'announcement',
                a.classroom_id,
                a.user_id,
                NULL,
                COALESCE(a.title, ''),
                COALESCE(a.slug, ''),
                a.content,
                a.created_at,
                a.updated_at
            FROM announcements a
        ");

        // 5. Set announcements.classwork_item_id via correlated subquery
        DB::statement("
            UPDATE announcements
            SET classwork_item_id = (
                SELECT id FROM classwork_items
                WHERE classwork_items.slug = announcements.slug
                  AND classwork_items.type = 'announcement'
                LIMIT 1
            )
        ");

        // 6. Backfill classwork_items rows for existing attendance_sessions
        DB::statement("
            INSERT INTO classwork_items (type, classroom_id, user_id, topic_id, title, slug, description, created_at, updated_at)
            SELECT
                'attendance',
                ci.classroom_id,
                ci.user_id,
                ci.topic_id,
                ci.title,
                COALESCE(att.slug, ''),
                ci.description,
                att.created_at,
                att.updated_at
            FROM attendance_sessions att
            JOIN assignments a ON a.id = att.assignment_id
            JOIN classwork_items ci ON ci.id = a.classwork_item_id
        ");

        // 7. Set attendance_sessions.classwork_item_id by matching the backfilled slug
        DB::statement("
            UPDATE attendance_sessions
            SET classwork_item_id = (
                SELECT id FROM classwork_items
                WHERE classwork_items.slug = attendance_sessions.slug
                  AND classwork_items.type = 'attendance'
                LIMIT 1
            )
        ");

        // 8. Make classwork_item_id NOT NULL and add unique constraint
        Schema::table('announcements', function (Blueprint $table) {
            $table->unsignedBigInteger('classwork_item_id')->nullable(false)->change();
            $table->unique('classwork_item_id');
        });
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('classwork_item_id')->nullable(false)->change();
            $table->unique('classwork_item_id');
        });

        // 9. Drop old unique indexes BEFORE dropping columns (SQLite requires this)
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        // 10. Drop old columns from announcements (keep content — it stays)
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('classroom_id');
            $table->dropColumn(['title', 'slug']);
        });

        // 11. Drop old columns from attendance_sessions
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assignment_id');
            $table->dropColumn(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('announcements', 'classwork_item_id')) {
            return;
        }

        // 1. Re-add columns to announcements
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('classwork_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable()->after('classroom_id');
            $table->string('slug', 16)->nullable()->after('title');
        });

        // 2. Backfill announcements columns from classwork_items
        DB::statement('
            UPDATE announcements
            SET user_id = (SELECT user_id FROM classwork_items WHERE classwork_items.id = announcements.classwork_item_id LIMIT 1),
                classroom_id = (SELECT classroom_id FROM classwork_items WHERE classwork_items.id = announcements.classwork_item_id LIMIT 1),
                title = (SELECT title FROM classwork_items WHERE classwork_items.id = announcements.classwork_item_id LIMIT 1),
                slug = (SELECT slug FROM classwork_items WHERE classwork_items.id = announcements.classwork_item_id LIMIT 1)
        ');

        // 3. Re-add unique index on announcements.slug
        Schema::table('announcements', function (Blueprint $table) {
            $table->unique('slug');
        });

        // 4. Re-add columns to attendance_sessions
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->foreignId('assignment_id')->nullable()->after('classwork_item_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 16)->nullable()->after('assignment_id');
        });

        // 5. Backfill attendance_sessions.slug from classwork_items
        DB::statement('
            UPDATE attendance_sessions
            SET slug = (SELECT slug FROM classwork_items WHERE classwork_items.id = attendance_sessions.classwork_item_id LIMIT 1)
        ');

        // 6. Re-add unique index on attendance_sessions.slug
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->unique('slug');
        });

        // 7. Drop classwork_item_id from both tables
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropUnique(['classwork_item_id']);
            $table->dropConstrainedForeignId('classwork_item_id');
        });
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropUnique(['classwork_item_id']);
            $table->dropConstrainedForeignId('classwork_item_id');
        });

        // 8. Revert classwork_items.type enum (MySQL only)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE classwork_items MODIFY COLUMN type ENUM('assignment','material') NOT NULL");
        }

        // 9. Delete the backfilled classwork_items rows
        DB::statement("DELETE FROM classwork_items WHERE type IN ('announcement', 'attendance')");
    }
};
