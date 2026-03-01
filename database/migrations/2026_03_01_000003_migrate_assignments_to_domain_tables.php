<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Phase 1c — Migrate announcement/material/topic data out of assignments.
 *
 * Steps:
 * 1. Migrate assignment type='announcement' → announcements table
 * 2. Migrate assignment type='material' → materials table
 * 3. Update classroom_contents contentable_type for migrated rows
 * 4. Migrate polymorphic references (comments, attachments, coin_transactions)
 * 5. Delete assignment type='topic' + their classroom_contents entries
 * 6. Remove migrated assignment rows
 * 7. Update assignments.type column to only allow gradeable types
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Migrate announcements ───────────────────────────
        $announcementAssignments = DB::table('assignments')
            ->where('type', 'announcement')
            ->get();

        foreach ($announcementAssignments as $a) {
            $announcementId = DB::table('announcements')->insertGetId([
                'user_id' => $a->user_id,
                'title' => $a->title,
                'content' => $a->description,
                'attachments' => $a->attachments,
                'created_at' => $a->created_at,
                'updated_at' => $a->updated_at,
            ]);

            // Update the classroom_contents pivot to point to the new announcement
            DB::table('classroom_contents')
                ->where('contentable_type', 'App\\Models\\Assignment')
                ->where('contentable_id', $a->id)
                ->update([
                    'contentable_type' => 'App\\Models\\Announcement',
                    'contentable_id' => $announcementId,
                ]);

            // Migrate polymorphic comments
            DB::table('comments')
                ->where('commentable_type', 'App\\Models\\Assignment')
                ->where('commentable_id', $a->id)
                ->update([
                    'commentable_type' => 'App\\Models\\Announcement',
                    'commentable_id' => $announcementId,
                ]);

            // Migrate polymorphic attachments
            DB::table('attachments')
                ->where('attachable_type', 'App\\Models\\Assignment')
                ->where('attachable_id', $a->id)
                ->update([
                    'attachable_type' => 'App\\Models\\Announcement',
                    'attachable_id' => $announcementId,
                ]);

            // Migrate coin_transactions references
            DB::table('coin_transactions')
                ->where('reference_type', 'App\\Models\\Assignment')
                ->where('reference_id', $a->id)
                ->update([
                    'reference_type' => 'App\\Models\\Announcement',
                    'reference_id' => $announcementId,
                ]);
        }

        // ── 2. Migrate materials ───────────────────────────────
        $materialAssignments = DB::table('assignments')
            ->where('type', 'material')
            ->get();

        foreach ($materialAssignments as $m) {
            $slug = $this->generateUniqueSlug();
            $materialId = DB::table('materials')->insertGetId([
                'user_id' => $m->user_id,
                'title' => $m->title ?? 'Untitled Material',
                'slug' => $slug,
                'description' => $m->description,
                'attachments' => $m->attachments,
                'created_at' => $m->created_at,
                'updated_at' => $m->updated_at,
            ]);

            // Update the classroom_contents pivot
            DB::table('classroom_contents')
                ->where('contentable_type', 'App\\Models\\Assignment')
                ->where('contentable_id', $m->id)
                ->update([
                    'contentable_type' => 'App\\Models\\Material',
                    'contentable_id' => $materialId,
                ]);

            // Migrate polymorphic comments
            DB::table('comments')
                ->where('commentable_type', 'App\\Models\\Assignment')
                ->where('commentable_id', $m->id)
                ->update([
                    'commentable_type' => 'App\\Models\\Material',
                    'commentable_id' => $materialId,
                ]);

            // Migrate polymorphic attachments
            DB::table('attachments')
                ->where('attachable_type', 'App\\Models\\Assignment')
                ->where('attachable_id', $m->id)
                ->update([
                    'attachable_type' => 'App\\Models\\Material',
                    'attachable_id' => $materialId,
                ]);

            // Migrate coin_transactions references
            DB::table('coin_transactions')
                ->where('reference_type', 'App\\Models\\Assignment')
                ->where('reference_id', $m->id)
                ->update([
                    'reference_type' => 'App\\Models\\Material',
                    'reference_id' => $materialId,
                ]);
        }

        // ── 3. Delete topic assignments ────────────────────────
        $topicAssignmentIds = DB::table('assignments')
            ->where('type', 'topic')
            ->pluck('id');

        if ($topicAssignmentIds->isNotEmpty()) {
            // Remove classroom_contents entries
            DB::table('classroom_contents')
                ->where('contentable_type', 'App\\Models\\Assignment')
                ->whereIn('contentable_id', $topicAssignmentIds)
                ->delete();

            // Remove any comments
            DB::table('comments')
                ->where('commentable_type', 'App\\Models\\Assignment')
                ->whereIn('commentable_id', $topicAssignmentIds)
                ->delete();

            // Remove any attachments
            DB::table('attachments')
                ->where('attachable_type', 'App\\Models\\Assignment')
                ->whereIn('attachable_id', $topicAssignmentIds)
                ->delete();

            // Remove any coin_transactions
            DB::table('coin_transactions')
                ->where('reference_type', 'App\\Models\\Assignment')
                ->whereIn('reference_id', $topicAssignmentIds)
                ->delete();
        }

        // ── 4. Delete migrated/topic assignment rows ───────────
        DB::table('assignments')
            ->whereIn('type', ['announcement', 'material', 'topic'])
            ->delete();

        // ── 5. Update type column ──────────────────────────────
        // Convert to string so we're not constrained by enum on MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `assignments` MODIFY `type` VARCHAR(255) NOT NULL DEFAULT 'question'");
        }
        // SQLite already uses string columns — no change needed
    }

    public function down(): void
    {
        // This migration is not safely reversible as it moves data between tables.
        // A full rollback would require re-migrating data back from announcements/materials
        // to assignments, which risks data loss from any new announcements/materials
        // created after this migration ran.
    }

    private function generateUniqueSlug(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        do {
            $slug = '';
            for ($i = 0; $i < 16; $i++) {
                $slug .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (DB::table('materials')->where('slug', $slug)->exists());

        return $slug;
    }
};
