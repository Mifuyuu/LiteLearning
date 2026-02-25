<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create attendance_sessions table
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->string('current_code', 6)->nullable();
            $table->boolean('is_active')->default(false);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('code_rotated_at')->nullable();
            $table->timestamps();
        });

        // 2. Update assignments table
        // For MySQL/MariaDB, we can just use ALTER TABLE
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                // Change 'assignment' enum to 'question' if needed, though Laravel Schema doesn't easily change enums without raw SQL
                // So we'll add the new column and convert data
                if (!Schema::hasColumn('assignments', 'allow_late_submission')) {
                    $table->boolean('allow_late_submission')->default(true)->after('topic');
                }
            });

            // Convert 'assignment' type to 'question' and update the enum list if possible
            // In MariaDB/MySQL, we can just change the column definition
            if (config('database.default') !== 'sqlite') {
                DB::statement("ALTER TABLE assignments MODIFY COLUMN type ENUM('attendance', 'file', 'question', 'quiz', 'material') NOT NULL DEFAULT 'question'");
                DB::table('assignments')->where('type', 'assignment')->update(['type' => 'question']);
            } else {
                // SQLite logic (original intention)
                // Since this migration failed on MySQL, we focus on making it work for MySQL.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');

        if (Schema::hasTable('assignments')) {
            if (config('database.default') !== 'sqlite') {
                DB::table('assignments')->whereIn('type', ['question', 'attendance', 'file'])->update(['type' => 'assignment']);
                DB::statement("ALTER TABLE assignments MODIFY COLUMN type ENUM('assignment', 'quiz', 'material') NOT NULL DEFAULT 'assignment'");
                Schema::table('assignments', function (Blueprint $table) {
                    $table->dropColumn('allow_late_submission');
                });
            }
        }
    }
};
