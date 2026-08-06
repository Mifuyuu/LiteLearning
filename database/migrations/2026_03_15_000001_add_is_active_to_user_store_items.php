<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('user_store_items', 'is_active')) {
            return;
        }

        Schema::table('user_store_items', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('store_item_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('user_store_items', 'is_active')) {
            return;
        }

        Schema::table('user_store_items', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
