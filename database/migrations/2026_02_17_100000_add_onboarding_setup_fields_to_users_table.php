<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('school_name')->nullable()->after('role');
            $table->string('study_year')->nullable()->after('school_name');
            $table->date('birth_date')->nullable()->after('study_year');
            $table->timestamp('tos_accepted_at')->nullable()->after('birth_date');
            $table->timestamp('setup_completed_at')->nullable()->after('tos_accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'school_name',
                'study_year',
                'birth_date',
                'tos_accepted_at',
                'setup_completed_at',
            ]);
        });
    }
};
