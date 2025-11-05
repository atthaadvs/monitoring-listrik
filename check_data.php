<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SensorData;
use Carbon\Carbon;

echo "=== CHECKING DATA FOR 2025-10-08 ===\n";
echo "Current Jakarta time: " . Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s') . "\n\n";

echo "Latest 10 records:\n";
$latest = SensorData::whereDate('recorded_at', '2025-10-08')
    ->orderBy('recorded_at', 'desc')
    ->take(10)
    ->get(['recorded_at', 'temperature', 'humidity']);

foreach ($latest as $record) {
    $jakartaTime = $record->recorded_at->setTimezone('Asia/Jakarta');
    echo "DB: " . $record->recorded_at . " -> Jakarta: " . $jakartaTime->format('Y-m-d H:i:s') . " Temp: " . $record->temperature . "°C\n";
}

echo "\nData count per hour today:\n";
$hourlyCount = SensorData::whereDate('recorded_at', '2025-10-08')
    ->selectRaw('strftime("%H", recorded_at) as hour, COUNT(*) as count')
    ->groupBy('hour')
    ->orderBy('hour')
    ->get();

foreach ($hourlyCount as $data) {
    echo "Jam " . sprintf('%02d', $data->hour) . ":xx = " . $data->count . " records\n";
}
