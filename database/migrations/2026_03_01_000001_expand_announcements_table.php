<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title')->nullable();
                $table->text('content');
                $table->json('attachments')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('announcements', function (Blueprint $table) {
                if (! Schema::hasColumn('announcements', 'title')) {
                    $table->string('title')->nullable()->after('user_id');
                }
                if (! Schema::hasColumn('announcements', 'attachments')) {
                    $table->json('attachments')->nullable()->after('content');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
