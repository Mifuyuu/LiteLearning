<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add classroom_id to assignments, announcements, materials
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
        });

        // Step 2: Migrate data from classroom_contents back to each content table
        DB::statement("
            UPDATE assignments
            SET classroom_id = (
                SELECT cc.classroom_id
                FROM classroom_contents cc
                WHERE cc.contentable_type = 'App\\\\Models\\\\Assignment'
                AND cc.contentable_id = assignments.id
                LIMIT 1
            )
        ");

        DB::statement("
            UPDATE announcements
            SET classroom_id = (
                SELECT cc.classroom_id
                FROM classroom_contents cc
                WHERE cc.contentable_type = 'App\\\\Models\\\\Announcement'
                AND cc.contentable_id = announcements.id
                LIMIT 1
            )
        ");

        DB::statement("
            UPDATE materials
            SET classroom_id = (
                SELECT cc.classroom_id
                FROM classroom_contents cc
                WHERE cc.contentable_type = 'App\\\\Models\\\\Material'
                AND cc.contentable_id = materials.id
                LIMIT 1
            )
        ");

        // Step 3: Drop classroom_contents table
        Schema::dropIfExists('classroom_contents');
    }

    public function down(): void
    {
        // Re-create classroom_contents
        Schema::create('classroom_contents', function (Blueprint $table) {
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->morphs('contentable');
            $table->unsignedInteger('order')->nullable();
            $table->timestamp('pinned_at')->nullable();
            $table->timestamps();

            $table->primary(['classroom_id', 'contentable_type', 'contentable_id']);
            $table->index(['classroom_id', 'pinned_at', 'order']);
        });

        // Migrate data back to classroom_contents
        DB::statement("
            INSERT INTO classroom_contents (classroom_id, contentable_type, contentable_id, created_at, updated_at)
            SELECT classroom_id, 'App\\\\Models\\\\Assignment', id, created_at, updated_at
            FROM assignments WHERE classroom_id IS NOT NULL
        ");

        DB::statement("
            INSERT INTO classroom_contents (classroom_id, contentable_type, contentable_id, created_at, updated_at)
            SELECT classroom_id, 'App\\\\Models\\\\Announcement', id, created_at, updated_at
            FROM announcements WHERE classroom_id IS NOT NULL
        ");

        DB::statement("
            INSERT INTO classroom_contents (classroom_id, contentable_type, contentable_id, created_at, updated_at)
            SELECT classroom_id, 'App\\\\Models\\\\Material', id, created_at, updated_at
            FROM materials WHERE classroom_id IS NOT NULL
        ");

        // Remove classroom_id columns
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });
    }
};
