<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the JSON attachments column from content tables.
     * Files have already been migrated to the polymorphic attachments table (Step A).
     */
    public function up(): void
    {
        foreach (['assignments', 'announcements', 'materials'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropColumn('attachments');
            });
        }
    }

    /**
     * Restore the attachments JSON column on rollback.
     */
    public function down(): void
    {
        foreach (['assignments', 'announcements', 'materials'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->json('attachments')->nullable()->after('description');
            });
        }
    }
};
