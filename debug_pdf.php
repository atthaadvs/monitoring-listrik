<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SensorData;
use Carbon\Carbon;

echo "=== DEBUGGING PDF DATA (FIXED) ===\n";

$date = '2025-10-08';

echo "Target date: " . $date . "\n\n";

// Get all data using the new logic
$allData = SensorData::whereDate('recorded_at', $date)
    ->orderBy('recorded_at', 'asc')
    ->get(['recorded_at', 'temperature', 'humidity'])
    ->filter(function($item) use ($date) {
        // Only include records that are actually from the target date in Jakarta timezone
        $jakartaTime = $item->recorded_at->setTimezone('Asia/Jakarta');
        return $jakartaTime->format('Y-m-d') === $date;
    });

echo "Total valid records found: " . $allData->count() . "\n\n";

echo "Valid data (Jakarta timezone):\n";
foreach ($allData as $record) {
    $jakartaTime = $record->recorded_at->setTimezone('Asia/Jakarta');
    echo "- " . $jakartaTime->format('Y-m-d H:i:s') . " Temp: " . $record->temperature . "°C\n";
}

echo "\nGrouped by hour (only valid hours):\n";
$grouped = $allData->groupBy(function($item) {
    $jakartaTime = $item->recorded_at->setTimezone('Asia/Jakarta');
    return $jakartaTime->format('H');
});

foreach ($grouped as $hour => $hourlyData) {
    echo "Jam " . sprintf('%02d', $hour) . ":00 = " . $hourlyData->count() . " records\n";
}
