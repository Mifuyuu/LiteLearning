<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('slug', 12)->default('')->after('name');
        });

        // Generate slugs for existing classrooms using query builder (not Eloquent)
        $classrooms = DB::table('classrooms')->get();
        foreach ($classrooms as $classroom) {
            do {
                $slug = strtolower(Str::random(12));
            } while (DB::table('classrooms')->where('slug', $slug)->exists());

            DB::table('classrooms')
                ->where('id', $classroom->id)
                ->update(['slug' => $slug]);
        }

        // Now add unique index
        Schema::table('classrooms', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
