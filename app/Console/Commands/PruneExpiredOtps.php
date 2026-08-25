<?php

namespace App\Console\Commands;

use App\Models\EmailOtpVerification;
use Illuminate\Console\Command;

class PruneExpiredOtps extends Command
{
    protected $signature = 'otp:prune';

    protected $description = 'Delete expired OTPs from the database';

    public function handle(): int
    {
        $deleted = EmailOtpVerification::where('expires_at', '<', now())->delete();

        $this->info("Deleted {$deleted} expired OTPs.");

        return Command::SUCCESS;
    }
}
