<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$connection = DB::connection();
echo "DRIVER: " . $connection->getDriverName() . "\n";
echo "DB: " . $connection->getDatabaseName() . "\n";
if ($connection->getDriverName() == 'mysql') {
    echo "HOST: " . $connection->getConfig('host') . "\n";
    echo "PORT: " . $connection->getConfig('port') . "\n";
}
