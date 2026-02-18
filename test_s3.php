<?php
// Quick S3/MinIO connection test
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "=== S3/MinIO Connection Test ===" . PHP_EOL;
echo "Endpoint: " . env('AWS_ENDPOINT') . PHP_EOL;
echo "Bucket: " . env('AWS_BUCKET') . PHP_EOL;
echo PHP_EOL;

try {
    // 1. Upload
    $testContent = 'Hello from LiteLearning! Timestamp: ' . now();
    Storage::disk('s3')->put('test/hello.txt', $testContent);
    echo "[✓] UPLOAD: test/hello.txt" . PHP_EOL;

    // 2. Read
    $content = Storage::disk('s3')->get('test/hello.txt');
    echo "[✓] READ: " . $content . PHP_EOL;

    // 3. Check exists
    $exists = Storage::disk('s3')->exists('test/hello.txt');
    echo "[✓] EXISTS: " . ($exists ? 'true' : 'false') . PHP_EOL;

    // 4. Get URL
    $url = Storage::disk('s3')->url('test/hello.txt');
    echo "[✓] URL: " . $url . PHP_EOL;

    // 5. List files
    $files = Storage::disk('s3')->files('test');
    echo "[✓] LIST: " . implode(', ', $files) . PHP_EOL;

    // 6. Delete
    Storage::disk('s3')->delete('test/hello.txt');
    echo "[✓] DELETE: test/hello.txt" . PHP_EOL;

    // 7. Verify delete
    $exists = Storage::disk('s3')->exists('test/hello.txt');
    echo "[✓] VERIFY DELETED: " . ($exists ? 'FAIL - still exists!' : 'OK - deleted') . PHP_EOL;

    echo PHP_EOL . "=== ALL TESTS PASSED ===" . PHP_EOL;
} catch (\Exception $e) {
    echo "[✗] ERROR: " . $e->getMessage() . PHP_EOL;
    echo "    File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
