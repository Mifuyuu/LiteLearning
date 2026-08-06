<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'setup_completed_at')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('setup_completed_at');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'setup_completed_at')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('setup_completed_at')->nullable();
        });
    }
};
