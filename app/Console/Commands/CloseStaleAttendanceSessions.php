<?php

namespace App\Console\Commands;

use App\Models\AttendanceSession;
use Illuminate\Console\Command;

class CloseStaleAttendanceSessions extends Command
{
    protected $signature = 'attendance:close-stale';

    protected $description = 'Close attendance sessions that have been abandoned without code rotation';

    public function handle(): int
    {
        $cutoff = now()->subSeconds(AttendanceSession::STALE_TIMEOUT_SECONDS);

        $closed = AttendanceSession::where('is_active', true)
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('code_rotated_at')
                    ->orWhere('code_rotated_at', '<=', $cutoff);
            })
            ->update([
                'is_active' => false,
                'current_code' => null,
            ]);

        $this->info("Closed {$closed} stale attendance session(s).");

        return Command::SUCCESS;
    }
}
