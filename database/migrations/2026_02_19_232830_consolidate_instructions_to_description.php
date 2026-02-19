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
        // Copy instructions to description where description is null/empty
        DB::table('assignments')
            ->whereNull('description')
            ->orWhere('description', '')
            ->whereNotNull('instructions')
            ->where('instructions', '!=', '')
            ->update(['description' => DB::raw('instructions')]);

        // Drop the instructions column
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->text('instructions')->nullable()->after('description');
        });
    }
};
