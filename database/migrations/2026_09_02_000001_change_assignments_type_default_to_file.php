<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function ($table) {
            $table->string('type')->default('file')->change();
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function ($table) {
            $table->string('type')->default('question')->change();
        });
    }
};
