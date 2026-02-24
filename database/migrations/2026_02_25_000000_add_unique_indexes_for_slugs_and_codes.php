<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Comprehensive index optimization migration.
 *
 * Adds performance indexes on FK columns and frequently-filtered columns
 * that are missing from the current schema. The unique indexes on
 * assignments.slug, classrooms.slug, and classrooms.code already exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── assignments ──────────────────────────────────────────────────────
        Schema::table('assignments', function (Blueprint $table) {
            // Classroom page loads all assignments for the classroom
            $table->index('classroom_id', 'assignments_classroom_id_index');

            // Dashboard: upcoming published assignments filtered by status
            $table->index(['classroom_id', 'status'], 'assignments_classroom_id_status_index');

            // Due-date ordering on dashboard upcoming list
            $table->index('due_date', 'assignments_due_date_index');
        });

        // ── submissions ───────────────────────────────────────────────────────
        Schema::table('submissions', function (Blueprint $table) {
            // Student dashboard / profile: all submissions for a user
            $table->index('user_id', 'submissions_user_id_index');

            // "Pending grading" count filtered by status
            $table->index('status', 'submissions_status_index');
        });

        // ── announcements ─────────────────────────────────────────────────────
        Schema::table('announcements', function (Blueprint $table) {
            // Classroom stream: all announcements for a classroom ordered by latest
            $table->index('classroom_id', 'announcements_classroom_id_index');
        });

        // ── coin_transactions ─────────────────────────────────────────────────
        Schema::table('coin_transactions', function (Blueprint $table) {
            // Transaction history per user
            $table->index('user_id', 'coin_transactions_user_id_index');

            // Filter by type (earn/spend) on profile page
            $table->index('type', 'coin_transactions_type_index');
        });

        // ── users ─────────────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            // Admin dashboard: filter and count users by role
            $table->index('role', 'users_role_index');
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex('assignments_classroom_id_index');
            $table->dropIndex('assignments_classroom_id_status_index');
            $table->dropIndex('assignments_due_date_index');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex('submissions_user_id_index');
            $table->dropIndex('submissions_status_index');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex('announcements_classroom_id_index');
        });

        Schema::table('coin_transactions', function (Blueprint $table) {
            $table->dropIndex('coin_transactions_user_id_index');
            $table->dropIndex('coin_transactions_type_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
        });
    }
};
