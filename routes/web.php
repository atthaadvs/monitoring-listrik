<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Api\SensorController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/sensor-data', [DashboardController::class, 'apiData'])->name('api.sensor-data');
Route::get('/api/chart-data', [DashboardController::class, 'chartData'])->name('api.chart-data');

// POST route untuk menerima data dari ESP32 (tanpa CSRF)
Route::post('/api/sensor-data', [SensorController::class, 'store'])->name('api.sensor-data.store')->withoutMiddleware(['web']);

// Alternative route untuk ESP32 yang menggunakan /api/sensor
Route::post('/api/sensor', [SensorController::class, 'store'])->name('api.sensor.store')->withoutMiddleware(['web']);

// Test route untuk debug
Route::get('/test-data', function() {
    $count = App\Models\SensorData::count();
    $latest = App\Models\SensorData::latest('recorded_at')->first();
    return response()->json([
        'total_records' => $count,
        'latest_data' => $latest
    ]);
});

// Routes untuk History
Route::get('/history', [HistoryController::class, 'index'])->name('history');
Route::get('/history/download/{date}', [HistoryController::class, 'downloadPdf'])->name('history.download');

// Testing routes for notifications
Route::get('/test-email', function() {
    $sensorData = App\Models\SensorData::latest()->first() ?? new App\Models\SensorData([
        'temperature' => 35,
        'humidity' => 80,
        'power_status' => false,
        'voltage' => 0,
        'current' => 0,
        'power' => 0,
        'recorded_at' => now()
    ]);
    
    $notificationService = new App\Services\NotificationService();
    $notificationService->checkSensorConditions($sensorData);
    
    return 'Test email sent!';
});

Route::get('/test-monthly-report', function() {
    $notificationService = new App\Services\NotificationService();
    $result = $notificationService->generateMonthlyReport(10, 2025);
    
    return $result ? 'Monthly report generated!' : 'No data available';
});
Route::get('/api/history', [HistoryController::class, 'apiHistory']);

