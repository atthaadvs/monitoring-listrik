<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SensorData;
use Carbon\Carbon;

echo "=== CLEANING INVALID DATA ===\n";

// Delete data before 13:00 today (invalid data)
$invalidData = SensorData::whereDate('recorded_at', '2025-10-08')
    ->whereTime('recorded_at', '<', '13:00:00')
    ->get();

echo "Found " . $invalidData->count() . " invalid records before 13:00 today\n";

if ($invalidData->count() > 0) {
    echo "Deleting invalid data...\n";
    SensorData::whereDate('recorded_at', '2025-10-08')
        ->whereTime('recorded_at', '<', '13:00:00')
        ->delete();
    echo "Deleted invalid data successfully!\n";
}

echo "\nRemaining data for today:\n";
$remaining = SensorData::whereDate('recorded_at', '2025-10-08')
    ->selectRaw('strftime("%H", recorded_at) as hour, COUNT(*) as count')
    ->groupBy('hour')
    ->orderBy('hour')
    ->get();

foreach ($remaining as $data) {
    echo "Jam " . sprintf('%02d', $data->hour) . ":xx = " . $data->count . " records\n";
}
