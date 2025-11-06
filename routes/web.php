<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Auth\AdminAuthController;

// Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Protected admin routes
    Route::middleware('admin.auth')->group(function () {
        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('admin.profile');
        Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');
    });
});

// Protected Dashboard Routes
Route::middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/sensor-data', [DashboardController::class, 'apiData'])->name('api.sensor-data');
    Route::get('/api/chart-data', [DashboardController::class, 'chartData'])->name('api.chart-data');
    
    // Routes untuk History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/download/{date}', [HistoryController::class, 'downloadPdf'])->name('history.download');
    Route::get('/api/history', [HistoryController::class, 'apiHistory']);
});

// POST routes untuk menerima data dari ESP32 (tanpa CSRF dan tanpa auth)
Route::post('/api/sensor-data', [SensorController::class, 'store'])->name('api.sensor-data.store')->withoutMiddleware(['web']);
Route::post('/api/sensor', [SensorController::class, 'store'])->name('api.sensor.store')->withoutMiddleware(['web']);

// Test route untuk debug (protected)
Route::middleware('admin.auth')->get('/test-data', function() {
    $count = App\Models\SensorData::count();
    $latest = App\Models\SensorData::latest('recorded_at')->first();
    return response()->json([
        'total_records' => $count,
        'latest_data' => $latest
    ]);
});

// Testing routes for notifications (protected)
Route::middleware('admin.auth')->group(function () {
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
});

// Root route - redirect to login if not authenticated
Route::get('/', function () {
    if (!Auth::guard('admin')->check()) {
        return redirect()->route('admin.login');
    }
    return redirect()->route('dashboard');
});

