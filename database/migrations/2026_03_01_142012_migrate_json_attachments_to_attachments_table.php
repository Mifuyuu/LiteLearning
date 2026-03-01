<?php

use App\Models\Attachment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrate JSON attachments columns on assignments, announcements, and materials
     * into rows in the polymorphic attachments table.
     *
     * This is a data-only migration — it does NOT drop any columns (Step A).
     * Skips rows that already have a corresponding attachments record to be idempotent.
     */
    public function up(): void
    {
        $tables = [
            'assignments'  => 'App\\Models\\Assignment',
            'announcements' => 'App\\Models\\Announcement',
            'materials'    => 'App\\Models\\Material',
        ];

        foreach ($tables as $table => $morphType) {
            DB::table($table)
                ->whereNotNull('attachments')
                ->orderBy('id')
                ->each(function (object $row) use ($morphType): void {
                    $items = json_decode($row->attachments, true);

                    if (! is_array($items) || empty($items)) {
                        return;
                    }

                    foreach ($items as $item) {
                        $filePath = $item['path'] ?? null;

                        if (! $filePath) {
                            continue;
                        }

                        // Idempotent: skip if already migrated
                        $exists = DB::table('attachments')
                            ->where('attachable_type', $morphType)
                            ->where('attachable_id', $row->id)
                            ->where('file_path', $filePath)
                            ->exists();

                        if ($exists) {
                            continue;
                        }

                        DB::table('attachments')->insert([
                            'attachable_type' => $morphType,
                            'attachable_id'   => $row->id,
                            'file_name'       => $item['name'] ?? basename($filePath),
                            'file_path'       => $filePath,
                            'file_type'       => $item['mime'] ?? 'application/octet-stream',
                            'file_size'       => $item['size'] ?? 0,
                            'uploaded_by'     => $row->user_id,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    }
                });
        }
    }

    /**
     * Rollback: remove attachment rows that were migrated from JSON columns.
     * Reconstructed by matching attachable_type/id and file_path against current JSON data.
     */
    public function down(): void
    {
        $tables = [
            'assignments'  => 'App\\Models\\Assignment',
            'announcements' => 'App\\Models\\Announcement',
            'materials'    => 'App\\Models\\Material',
        ];

        foreach ($tables as $table => $morphType) {
            DB::table($table)
                ->whereNotNull('attachments')
                ->orderBy('id')
                ->each(function (object $row) use ($morphType): void {
                    $items = json_decode($row->attachments, true);

                    if (! is_array($items) || empty($items)) {
                        return;
                    }

                    $paths = array_filter(array_column($items, 'path'));

                    if (empty($paths)) {
                        return;
                    }

                    DB::table('attachments')
                        ->where('attachable_type', $morphType)
                        ->where('attachable_id', $row->id)
                        ->whereIn('file_path', $paths)
                        ->delete();
                });
        }
    }
};
