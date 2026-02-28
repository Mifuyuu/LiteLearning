<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_contents', function (Blueprint $table) {
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->morphs('contentable'); // contentable_type + contentable_id
            $table->integer('order')->nullable();
            $table->timestamp('pinned_at')->nullable();
            $table->timestamps();

            // Composite PK — one content item can only appear once in a classroom
            $table->primary(['classroom_id', 'contentable_type', 'contentable_id'], 'classroom_contents_primary');

            // Performance index for stream ordering
            $table->index(['classroom_id', 'pinned_at', 'order'], 'classroom_contents_stream_index');
        });

        // Migrate existing announcements into classroom_contents
        $announcements = DB::table('announcements')->get(['id', 'classroom_id', 'created_at', 'updated_at']);
        foreach ($announcements as $a) {
            DB::table('classroom_contents')->insert([
                'classroom_id'     => $a->classroom_id,
                'contentable_type' => 'App\\Models\\Announcement',
                'contentable_id'   => $a->id,
                'created_at'       => $a->created_at,
                'updated_at'       => $a->updated_at,
            ]);
        }

        // Migrate existing assignments into classroom_contents
        $assignments = DB::table('assignments')->get(['id', 'classroom_id', 'created_at', 'updated_at']);
        foreach ($assignments as $a) {
            DB::table('classroom_contents')->insert([
                'classroom_id'     => $a->classroom_id,
                'contentable_type' => 'App\\Models\\Assignment',
                'contentable_id'   => $a->id,
                'created_at'       => $a->created_at,
                'updated_at'       => $a->updated_at,
            ]);
        }

        // Drop classroom_id from announcements and assignments
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex('announcements_classroom_id_index');
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex('assignments_classroom_id_index');
            $table->dropIndex('assignments_classroom_id_status_index');
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });
    }

    public function down(): void
    {
        // Re-add classroom_id columns
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->constrained()->cascadeOnDelete();
        });

        // Restore classroom_id data from classroom_contents
        $contents = DB::table('classroom_contents')->get();
        foreach ($contents as $c) {
            if ($c->contentable_type === 'App\\Models\\Announcement') {
                DB::table('announcements')
                    ->where('id', $c->contentable_id)
                    ->update(['classroom_id' => $c->classroom_id]);
            } elseif ($c->contentable_type === 'App\\Models\\Assignment') {
                DB::table('assignments')
                    ->where('id', $c->contentable_id)
                    ->update(['classroom_id' => $c->classroom_id]);
            }
        }

        Schema::dropIfExists('classroom_contents');
    }
};
