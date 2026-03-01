<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support ENUM changes, so we recreate the column
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('type_new')->default('question')->after('type');
        });

        // Copy data
        DB::table('assignments')->update(['type_new' => DB::raw('type')]);

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->string('type')->default('question')->after('status');
        });

        // Copy back
        DB::table('assignments')->update(['type' => DB::raw('type_new')]);

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('type_new');
        });

        // Convert any existing quiz records to question
        DB::table('assignments')->where('type', 'quiz')->update(['type' => 'question']);

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
