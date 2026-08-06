<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('classwork_items')) {
            return;
        }

        Schema::create('classwork_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['assignment', 'material']);
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 255);
            $table->string('slug', 32)->unique();
            $table->longText('description')->nullable();
            $table->timestamps();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('classwork_item_id')->nullable()->after('id')->constrained('classwork_items')->cascadeOnDelete();
        });
        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('classwork_item_id')->nullable()->after('id')->constrained('classwork_items')->cascadeOnDelete();
        });

        DB::statement("
            INSERT INTO classwork_items (type, classroom_id, user_id, topic_id, title, slug, description, created_at, updated_at)
            SELECT
                'assignment',
                a.classroom_id,
                a.user_id,
                t.id,
                a.title,
                a.slug,
                a.description,
                a.created_at,
                a.updated_at
            FROM assignments a
            LEFT JOIN topics t ON t.classroom_id = a.classroom_id AND t.name = a.topic
        ");

        DB::statement("
            UPDATE assignments
            SET classwork_item_id = (
                SELECT id FROM classwork_items
                WHERE classwork_items.slug = assignments.slug
                  AND classwork_items.type = 'assignment'
                LIMIT 1
            )
        ");

        DB::statement("
            INSERT INTO classwork_items (type, classroom_id, user_id, topic_id, title, slug, description, created_at, updated_at)
            SELECT
                'material',
                m.classroom_id,
                m.user_id,
                m.topic_id,
                m.title,
                m.slug,
                m.description,
                m.created_at,
                m.updated_at
            FROM materials m
        ");

        DB::statement("
            UPDATE materials
            SET classwork_item_id = (
                SELECT id FROM classwork_items
                WHERE classwork_items.slug = materials.slug
                  AND classwork_items.type = 'material'
                LIMIT 1
            )
        ");

        Schema::table('assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('classwork_item_id')->nullable(false)->change();
            $table->unique('classwork_item_id');
        });
        Schema::table('materials', function (Blueprint $table) {
            $table->unsignedBigInteger('classwork_item_id')->nullable(false)->change();
            $table->unique('classwork_item_id');
        });

        // Drop indexes referencing columns before dropping the columns (required for SQLite)
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });
        Schema::table('materials', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('classroom_id');
            $table->dropColumn(['title', 'slug', 'description', 'topic']);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('classroom_id');
            $table->dropConstrainedForeignId('topic_id');
            $table->dropColumn(['title', 'slug', 'description']);
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('assignments', 'classwork_item_id')) {
            return;
        }

        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('classwork_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255)->nullable()->after('classroom_id');
            $table->string('slug', 32)->nullable()->unique()->after('title');
            $table->longText('description')->nullable()->after('slug');
            $table->string('topic', 255)->nullable()->after('description');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('classwork_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255)->nullable()->after('classroom_id');
            $table->string('slug', 32)->nullable()->unique()->after('title');
            $table->longText('description')->nullable()->after('slug');
            $table->foreignId('topic_id')->nullable()->after('description')->constrained()->nullOnDelete();
        });

        DB::statement('
            UPDATE assignments
            SET user_id = (
                    SELECT user_id FROM classwork_items
                    WHERE classwork_items.id = assignments.classwork_item_id
                    LIMIT 1
                ),
                classroom_id = (
                    SELECT classroom_id FROM classwork_items
                    WHERE classwork_items.id = assignments.classwork_item_id
                    LIMIT 1
                ),
                title = (
                    SELECT title FROM classwork_items
                    WHERE classwork_items.id = assignments.classwork_item_id
                    LIMIT 1
                ),
                slug = (
                    SELECT slug FROM classwork_items
                    WHERE classwork_items.id = assignments.classwork_item_id
                    LIMIT 1
                ),
                description = (
                    SELECT description FROM classwork_items
                    WHERE classwork_items.id = assignments.classwork_item_id
                    LIMIT 1
                )
        ');

        DB::statement('
            UPDATE assignments
            SET topic = (
                SELECT topics.name
                FROM classwork_items
                LEFT JOIN topics ON topics.id = classwork_items.topic_id
                WHERE classwork_items.id = assignments.classwork_item_id
                LIMIT 1
            )
        ');

        DB::statement('
            UPDATE materials
            SET user_id = (
                    SELECT user_id FROM classwork_items
                    WHERE classwork_items.id = materials.classwork_item_id
                    LIMIT 1
                ),
                classroom_id = (
                    SELECT classroom_id FROM classwork_items
                    WHERE classwork_items.id = materials.classwork_item_id
                    LIMIT 1
                ),
                title = (
                    SELECT title FROM classwork_items
                    WHERE classwork_items.id = materials.classwork_item_id
                    LIMIT 1
                ),
                slug = (
                    SELECT slug FROM classwork_items
                    WHERE classwork_items.id = materials.classwork_item_id
                    LIMIT 1
                ),
                description = (
                    SELECT description FROM classwork_items
                    WHERE classwork_items.id = materials.classwork_item_id
                    LIMIT 1
                ),
                topic_id = (
                    SELECT topic_id FROM classwork_items
                    WHERE classwork_items.id = materials.classwork_item_id
                    LIMIT 1
                )
        ');

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropUnique(['classwork_item_id']);
            $table->dropConstrainedForeignId('classwork_item_id');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropUnique(['classwork_item_id']);
            $table->dropConstrainedForeignId('classwork_item_id');
        });

        Schema::dropIfExists('classwork_items');
    }
};
