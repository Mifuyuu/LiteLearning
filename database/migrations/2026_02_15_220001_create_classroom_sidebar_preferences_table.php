<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_sidebar_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('position')->nullable();
            $table->timestamp('pinned_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'classroom_id']);
            $table->index(['user_id', 'is_pinned', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_sidebar_preferences');
    }
};
