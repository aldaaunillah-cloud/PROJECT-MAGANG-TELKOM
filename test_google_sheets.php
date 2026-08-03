<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $service = new App\Services\GoogleSheetService();
    $data = $service->getData();
    echo 'SUCCESS. Count: ' . count($data) . PHP_EOL;
    if (count($data) > 0) {
        echo "First row sample: " . json_encode(array_slice($data, 0, 1)) . PHP_EOL;
    }
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
