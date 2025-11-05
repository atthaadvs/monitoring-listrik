<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ESP32 Sensor API Routes
Route::prefix('sensor')->group(function () {
    Route::post('/data', [SensorController::class, 'store']);
    Route::get('/latest', [SensorController::class, 'latest']);
    Route::get('/config', [SensorController::class, 'config']);
    Route::get('/status', [SensorController::class, 'systemStatus']);
});

// Alternative route for ESP32 (if using /api/sensor-data)
Route::post('/sensor-data', [SensorController::class, 'store']);
Route::get('/sensor-data', [SensorController::class, 'latest']);
