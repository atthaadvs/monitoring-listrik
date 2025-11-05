<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SensorData;
use Carbon\Carbon;

echo "=== ESP32 STATUS CHECK ===\n";

$latest = SensorData::latest()->first();
$now = Carbon::now('Asia/Jakarta');
$latestJakarta = $latest->recorded_at->setTimezone('Asia/Jakarta');

echo "Current time: " . $now->format('Y-m-d H:i:s') . "\n";
echo "Latest data: " . $latestJakarta->format('Y-m-d H:i:s') . "\n";
echo "Temperature: " . $latest->temperature . "°C\n";
echo "Humidity: " . $latest->humidity . "%\n";

$minutesAgo = $latest->recorded_at->diffInMinutes(now());
echo "Data age: " . $minutesAgo . " minutes ago\n";

if ($minutesAgo > 5) {
    echo "STATUS: ESP32 seems OFFLINE (no data for >" . $minutesAgo . " minutes)\n";
} else {
    echo "STATUS: ESP32 is ONLINE (data is recent)\n";
}

echo "\nToday's clean data count: " . SensorData::whereDate('recorded_at', '2025-10-08')->count() . " records\n";
