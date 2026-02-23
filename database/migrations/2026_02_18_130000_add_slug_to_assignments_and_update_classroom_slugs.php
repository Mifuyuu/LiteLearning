<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add slug column to assignments
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('slug', 16)->nullable()->unique()->after('id');
        });

        // 2. Backfill slugs for existing assignments
        $assignments = DB::table('assignments')->get();
        foreach ($assignments as $assignment) {
            do {
                $slug = self::generateSlug();
            } while (DB::table('assignments')->where('slug', $slug)->exists());

            DB::table('assignments')->where('id', $assignment->id)->update(['slug' => $slug]);
        }

        // 3. Make slug non-nullable
        // SQLite doesn't support ALTER COLUMN, so we leave it nullable
        // The model's booted() will ensure new records always have a slug

        // 4. Backfill classroom slugs to 16-char A-Za-z0-9
        $classrooms = DB::table('classrooms')->get();
        foreach ($classrooms as $classroom) {
            do {
                $slug = self::generateSlug();
            } while (DB::table('classrooms')->where('slug', $slug)->exists());

            DB::table('classrooms')->where('id', $classroom->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }

    private static function generateSlug(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $slug = '';
        for ($i = 0; $i < 16; $i++) {
            $slug .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $slug;
    }
};
