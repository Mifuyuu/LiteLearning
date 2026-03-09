<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamp('setup_completed_at')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('bio')->nullable();
            $table->string('active_name_color')->nullable();
            $table->string('active_avatar_frame')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string('locale')->default('en');

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('theme_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7)->default('#6B3FBF');
            $table->boolean('is_active')->default(true);

            $table->unsignedTinyInteger('planet_number')->default(1);
            $table->timestamps();
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('section')->nullable();
            $table->text('description')->nullable();
            $table->string('code')->unique();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->string('slug')->default('');
            $table->foreignId('theme_category_id')->nullable()->constrained('theme_categories')->nullOnDelete();
        });

        Schema::create('classroom_user', function (Blueprint $table) {
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['student', 'co-teacher'])->default('student');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            $table->primary(['classroom_id', 'user_id']);
        });

        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 16)->unique()->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('max_score')->default(100);
            $table->unsignedInteger('exp_reward')->default(0);
            $table->unsignedInteger('coin_reward')->default(0);
            $table->dateTime('due_date')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft')->index();
            $table->string('type')->default('question');
            $table->string('topic')->nullable();
            $table->boolean('allow_late_submission')->default(true);
            $table->timestamps();
            $table->index('due_date');
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug', 16)->unique();
            $table->text('description')->nullable();
            $table->foreignId('topic_id')->nullable()->constrained('topics')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content')->nullable();
            $table->enum('status', ['assigned', 'turned_in', 'graded', 'returned'])->default('assigned')->index();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('turned_in_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->string('current_code', 6)->nullable();
            $table->boolean('is_active')->default(false);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('code_rotated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge_image')->nullable();
            $table->integer('coin_reward')->default(0);
            $table->integer('xp_reward')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_achievements', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();
            $table->primary(['user_id', 'achievement_id']);
        });

        Schema::create('user_gamifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('coins')->default(0);
            $table->integer('xp')->default(0);
            $table->integer('level')->default(1);
            $table->timestamps();
        });

        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('amount');
            $table->string('type')->default('earn');
            $table->string('source')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamp('happened_at')->nullable();
            $table->timestamps();
            $table->index(['reference_type', 'reference_id']);
            $table->index('user_id');
            $table->index('type');
            $table->index(['user_id', 'happened_at']);
        });

        Schema::create('store_items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['name_color', 'avatar_frame']);
            $table->string('value');
            $table->integer('price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_store_items', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_item_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['user_id', 'store_item_id']);
        });

        Schema::create('email_otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('otp');
            $table->json('user_data');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('bug_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['bug', 'suggestion', 'other'])->default('bug');
            $table->string('title');
            $table->text('message');
            $table->enum('status', ['pending', 'resolved'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_reports');
        Schema::dropIfExists('email_otp_verifications');
        Schema::dropIfExists('user_store_items');
        Schema::dropIfExists('store_items');
        Schema::dropIfExists('coin_transactions');
        Schema::dropIfExists('user_gamifications');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('classroom_contents');
        Schema::dropIfExists('classroom_sidebar_preferences');
        Schema::dropIfExists('classroom_user');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('theme_categories');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
