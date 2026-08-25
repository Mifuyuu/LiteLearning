<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune')->daily();
Schedule::command('otp:prune')->hourly();
Schedule::command('classwork:publish-scheduled')->everyMinute();
Schedule::command('assignment:send-due-reminders')->hourly();
Schedule::command('attendance:close-stale')->everyFiveMinutes();
