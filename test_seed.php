<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    Illuminate\Support\Facades\Artisan::call('db:seed', ['-v' => true]);
    echo Illuminate\Support\Facades\Artisan::output();
} catch (\Throwable $e) {
    echo $e->getMessage() . "\n";
}
