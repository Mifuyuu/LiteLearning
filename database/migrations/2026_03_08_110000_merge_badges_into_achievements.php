<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // เพิ่ม badge_image ใน achievements (รวม badge system เข้า achievement system)
        Schema::table('achievements', function (Blueprint $table) {
            $table->string('badge_image')->nullable()->after('icon');
        });

        // ลบตาราง user_badges ก่อน (FK dependency)
        Schema::dropIfExists('user_badges');

        // ลบตาราง badges
        Schema::dropIfExists('badges');
    }

    public function down(): void
    {
        // สร้าง badges กลับ
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->string('target_role')->default('student');
            $table->timestamps();
        });

        // สร้าง user_badges กลับ
        Schema::create('user_badges', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at')->nullable();
            $table->timestamps();
            $table->primary(['user_id', 'badge_id']);
        });

        // ลบ badge_image ออกจาก achievements
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropColumn('badge_image');
        });
    }
};
