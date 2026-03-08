<?php

namespace App\Console\Commands;

use App\Models\EmailOtpVerification;
use Illuminate\Console\Command;

class PruneExpiredOtps extends Command
{
    protected $signature = 'otp:prune';

    protected $description = 'ลบ OTP ที่หมดอายุแล้วออกจากฐานข้อมูล';

    public function handle(): int
    {
        $deleted = EmailOtpVerification::where('expires_at', '<', now())->delete();

        $this->info("ลบ OTP ที่หมดอายุแล้ว {$deleted} รายการ");

        return Command::SUCCESS;
    }
}
