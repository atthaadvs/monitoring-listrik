<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SensorData;
use Carbon\Carbon;

echo "=== TESTING INDIVIDUAL DATA FOR PDF ===\n";

$date = '2025-10-08';

// Simulate the new logic from HistoryController
$data = SensorData::whereDate('recorded_at', $date)
    ->orderBy('recorded_at', 'asc')
    ->get()
    ->filter(function($item) use ($date) {
        $jakartaTime = $item->recorded_at->setTimezone('Asia/Jakarta');
        return $jakartaTime->format('Y-m-d') === $date;
    })
    ->map(function($record) {
        $jakartaTime = $record->recorded_at->setTimezone('Asia/Jakarta');
        
        return [
            'hour' => $jakartaTime->format('H:i'),
            'temperature' => round($record->temperature, 1),
            'humidity' => round($record->humidity, 1),
            'power_status' => $record->power_status,
            'voltage' => round($record->voltage, 1),
            'current' => round($record->current, 2),
            'power' => round($record->power, 1),
        ];
    });

echo "Total data for PDF: " . $data->count() . "\n\n";

echo "Individual records that will appear in PDF:\n";
foreach ($data as $index => $record) {
    echo ($index + 1) . ". " . $record['hour'] . " - Temp: " . $record['temperature'] . "°C, Humidity: " . $record['humidity'] . "%, Power: " . ($record['power_status'] ? 'ON' : 'OFF') . "\n";
}
