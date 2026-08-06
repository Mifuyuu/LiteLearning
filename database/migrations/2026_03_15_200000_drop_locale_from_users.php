<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'locale')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'locale')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('locale')->default('th')->after('remember_token');
        });
    }
};
