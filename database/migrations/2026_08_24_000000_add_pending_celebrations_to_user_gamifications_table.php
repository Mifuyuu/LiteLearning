<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_gamifications', function (Blueprint $table) {
            // Level-up/achievement celebrations queued for a user while someone
            // else (e.g. a teacher grading a submission) made the request —
            // shown to the user on their own next page load, then cleared.
            $table->json('pending_celebrations')->nullable()->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('user_gamifications', function (Blueprint $table) {
            $table->dropColumn('pending_celebrations');
        });
    }
};
