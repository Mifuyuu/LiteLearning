<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ──────────────────────────────────────────────
        // 1. Recreate assignments table with new type CHECK + allow_late_submission
        //    SQLite does not support ALTER COLUMN for CHECK constraints
        // ──────────────────────────────────────────────

        // Create new table with updated schema
        DB::statement('CREATE TABLE "assignments_new" (
            "id" integer primary key autoincrement not null,
            "classroom_id" integer not null,
            "user_id" integer not null,
            "title" varchar not null,
            "description" text,
            "instructions" text,
            "max_score" integer not null default \'100\',
            "due_date" datetime,
            "status" varchar check("status" in(\'draft\', \'published\', \'closed\')) not null default \'draft\',
            "type" varchar check("type" in(\'attendance\', \'file\', \'question\', \'quiz\', \'material\')) not null default \'question\',
            "topic" varchar,
            "allow_late_submission" tinyint(1) not null default \'1\',
            "created_at" datetime,
            "updated_at" datetime,
            foreign key("classroom_id") references "classrooms"("id") on delete cascade,
            foreign key("user_id") references "users"("id") on delete cascade
        )');

        // Copy data, converting old 'assignment' type to 'question'
        DB::statement('INSERT INTO "assignments_new" (
            "id", "classroom_id", "user_id", "title", "description", "instructions",
            "max_score", "due_date", "status",
            "type", "topic", "allow_late_submission", "created_at", "updated_at"
        ) SELECT
            "id", "classroom_id", "user_id", "title", "description", "instructions",
            "max_score", "due_date", "status",
            CASE WHEN "type" = \'assignment\' THEN \'question\' ELSE "type" END,
            "topic", 1, "created_at", "updated_at"
        FROM "assignments"');

        // Swap tables
        DB::statement('DROP TABLE "assignments"');
        DB::statement('ALTER TABLE "assignments_new" RENAME TO "assignments"');

        // ──────────────────────────────────────────────
        // 2. Create attendance_sessions table
        // ──────────────────────────────────────────────

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->string('current_code', 6)->nullable();
            $table->boolean('is_active')->default(false);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('code_rotated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');

        // Reverse: recreate assignments table with original CHECK constraint
        DB::statement('CREATE TABLE "assignments_old" (
            "id" integer primary key autoincrement not null,
            "classroom_id" integer not null,
            "user_id" integer not null,
            "title" varchar not null,
            "description" text,
            "instructions" text,
            "max_score" integer not null default \'100\',
            "due_date" datetime,
            "status" varchar check("status" in(\'draft\', \'published\', \'closed\')) not null default \'draft\',
            "type" varchar check("type" in(\'assignment\', \'quiz\', \'material\')) not null default \'assignment\',
            "topic" varchar,
            "created_at" datetime,
            "updated_at" datetime,
            foreign key("classroom_id") references "classrooms"("id") on delete cascade,
            foreign key("user_id") references "users"("id") on delete cascade
        )');

        DB::statement('INSERT INTO "assignments_old" (
            "id", "classroom_id", "user_id", "title", "description", "instructions",
            "max_score", "due_date", "status",
            "type", "topic", "created_at", "updated_at"
        ) SELECT
            "id", "classroom_id", "user_id", "title", "description", "instructions",
            "max_score", "due_date", "status",
            CASE WHEN "type" IN (\'question\', \'attendance\', \'file\') THEN \'assignment\' ELSE "type" END,
            "topic", "created_at", "updated_at"
        FROM "assignments"');

        DB::statement('DROP TABLE "assignments"');
        DB::statement('ALTER TABLE "assignments_old" RENAME TO "assignments"');
    }
};
