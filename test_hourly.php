<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SensorData;
use Carbon\Carbon;

echo "=== TESTING HOURLY DATA SUMMARY ===\n";

$date = '2025-10-08';

// Simulate the hourly grouping logic
$data = SensorData::whereDate('recorded_at', $date)
    ->orderBy('recorded_at', 'asc')
    ->get()
    ->filter(function($item) use ($date) {
        $jakartaTime = $item->recorded_at->setTimezone('Asia/Jakarta');
        return $jakartaTime->format('Y-m-d') === $date;
    })
    ->groupBy(function($item) {
        $jakartaTime = $item->recorded_at->setTimezone('Asia/Jakarta');
        return $jakartaTime->format('H');
    })
    ->map(function($hourlyData, $hour) {
        return [
            'hour' => sprintf('%02d:00', $hour),
            'temperature' => round($hourlyData->avg('temperature'), 1),
            'humidity' => round($hourlyData->avg('humidity'), 1),
            'power_status' => $hourlyData->last()->power_status,
            'voltage' => round($hourlyData->avg('voltage'), 1),
            'current' => round($hourlyData->avg('current'), 2),
            'power' => round($hourlyData->avg('power'), 1),
            'data_count' => $hourlyData->count()
        ];
    })
    ->sortKeys();

echo "Total jam dengan data: " . $data->count() . " jam\n";
echo "Maksimal dalam 1 hari: 24 jam\n\n";

echo "Data per jam yang akan muncul di PDF:\n";
foreach ($data as $record) {
    echo "Jam " . $record['hour'] . " = " . $record['data_count'] . " readings (Avg Temp: " . $record['temperature'] . "°C, Power: " . ($record['power_status'] ? 'ON' : 'OFF') . ")\n";
}

echo "\nPenjelasan:\n";
echo "- Jika alat hidup 24 jam penuh = akan ada 24 entry (00:00 - 23:00)\n";
echo "- Jika alat hanya hidup jam 13:xx = hanya ada 1 entry (13:00)\n";
echo "- Setiap jam adalah rata-rata dari semua pembacaan dalam jam tersebut\n";

echo "\nContoh jika alat hidup sepanjang hari:\n";
echo "00:00 = Avg dari semua data jam 00:xx\n";
echo "01:00 = Avg dari semua data jam 01:xx\n";
echo "02:00 = Avg dari semua data jam 02:xx\n";
echo "...\n";
echo "13:00 = Avg dari semua data jam 13:xx ✅ (saat ini hanya ini yang ada)\n";
echo "14:00 = Avg dari semua data jam 14:xx\n";
echo "...\n";
echo "23:00 = Avg dari semua data jam 23:xx\n";
echo "\nTotal = 24 data per hari (1 data per jam)\n";
