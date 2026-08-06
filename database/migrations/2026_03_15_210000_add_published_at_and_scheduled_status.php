<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('classwork_items', 'published_at')) {
            Schema::table('classwork_items', function (Blueprint $table) {
                $table->timestamp('published_at')->nullable()->after('description');
            });
        }

        Schema::table('assignments', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('classwork_items', 'published_at')) {
            Schema::table('classwork_items', function (Blueprint $table) {
                $table->dropColumn('published_at');
            });
        }

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('assignments', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published', 'closed'])->default('draft')->change();
            });
        }
    }
};
