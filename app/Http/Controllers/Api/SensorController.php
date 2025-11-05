<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SensorData;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SensorController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Store sensor data from ESP32
     * Real-time data for monitoring, database save every hour
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'temperature' => 'required|numeric|between:-40,100',
                'humidity' => 'required|numeric|between:0,100',
                'power_status' => 'boolean',
                'voltage' => 'nullable|numeric|between:0,300',
                'location' => 'nullable|string|max:255',
                'power' => 'nullable|numeric|between:0,10000',
            ]);

            // 🔄 REAL-TIME: Store in cache for instant monitoring
            $currentData = [
                'temperature' => $validated['temperature'],
                'humidity' => $validated['humidity'],
                'power_status' => $validated['power_status'] ?? true,
                'voltage' => $validated['voltage'] ?? null,
                'location' => $validated['location'] ?? 'Server Room BMKG',
                'power' => $validated['power'] ?? null,
                'last_updated' => now()->toISOString(),
            ];

            // Store in cache for real-time dashboard (expires in 2 minutes)
            Cache::put('latest_sensor_data', $currentData, 120);

            // 🗄️ DATABASE: Save only every hour (check if it's time)
            $this->saveToDatabase($currentData);

            // 🚨 CHECK AND SEND NOTIFICATIONS (using real-time data)
            $this->notificationService->checkSensorConditions((object) $currentData);

            return response()->json([
                'success' => true,
                'message' => 'Data sensor diterima untuk monitoring real-time',
                'data' => $currentData,
                'saved_to_db' => $this->wasJustSavedToDatabase()
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save data to database only every hour
     */
    private function saveToDatabase($data)
    {
        $lastSaveTime = Cache::get('last_db_save_time', 0);
        $currentTime = now()->timestamp;
        
        // Save every hour (3600 seconds)
        if (($currentTime - $lastSaveTime) >= 3600) {
            $sensorData = SensorData::create([
                'temperature' => $data['temperature'],
                'humidity' => $data['humidity'],
                'power_status' => $data['power_status'],
                'voltage' => $data['voltage'],
                'location' => $data['location'],
                'recorded_at' => now(),
                'power' => $data['power'],
            ]);

            // Update last save time
            Cache::put('last_db_save_time', $currentTime, 86400); // Cache for 24 hours
            Cache::put('latest_db_record', $sensorData->id, 86400);
            
            return $sensorData;
        }
        
        return null;
    }

    /**
     * Check if data was just saved to database
     */
    private function wasJustSavedToDatabase()
    {
        $lastSaveTime = Cache::get('last_db_save_time', 0);
        $currentTime = now()->timestamp;
        
        // If saved within last 60 seconds, consider it "just saved"
        return ($currentTime - $lastSaveTime) < 60;
    }

    /**
     * Get latest sensor data for ESP32
     */
    public function latest(): JsonResponse
    {
        try {
            // 🔄 FIRST: Try to get real-time data from cache
            $cachedData = Cache::get('latest_sensor_data');
            
            if ($cachedData) {
                return response()->json([
                    'success' => true,
                    'source' => 'real-time-cache',
                    'data' => [
                        'temperature' => $cachedData['temperature'],
                        'humidity' => $cachedData['humidity'],
                        'power_status' => $cachedData['power_status'],
                        'voltage' => $cachedData['voltage'],
                        'location' => $cachedData['location'],
                        'recorded_at' => $cachedData['last_updated'],
                        'current' => $cachedData['current'],
                        'power' => $cachedData['power'],
                        // Add status calculations
                        'temperature_status' => $this->getTemperatureStatus($cachedData['temperature']),
                        'humidity_status' => $this->getHumidityStatus($cachedData['humidity']),
                        'status_color' => $this->getStatusColor($cachedData['temperature'], $cachedData['humidity']),
                    ]
                ]);
            }

            // 🗄️ FALLBACK: Get from database if no cache
            $latestData = SensorData::latest('recorded_at')->first();

            if (!$latestData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data sensor',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'source' => 'database-fallback',
                'data' => [
                    'temperature' => $latestData->temperature,
                    'humidity' => $latestData->humidity,
                    'power_status' => $latestData->power_status,
                    'voltage' => $latestData->voltage,
                    'location' => $latestData->location,
                    'recorded_at' => $latestData->recorded_at->toISOString(),
                    'temperature_status' => $latestData->temperature_status,
                    'humidity_status' => $latestData->humidity_status,
                    'status_color' => $latestData->status_color,
                    'current' => $latestData->current,
                    'power' => $latestData->power,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper methods for status calculations
     */
    private function getTemperatureStatus($temperature)
    {
        if ($temperature < 18) return 'Dingin';
        if ($temperature > 27) return 'Panas';
        return 'Normal';
    }

    private function getHumidityStatus($humidity)
    {
        if ($humidity < 40) return 'Kering';
        if ($humidity > 60) return 'Lembab';
        return 'Normal';
    }

    private function getStatusColor($temperature, $humidity)
    {
        $tempOk = ($temperature >= 18 && $temperature <= 27);
        $humidityOk = ($humidity >= 40 && $humidity <= 60);
        
        if ($tempOk && $humidityOk) return 'success';
        if (!$tempOk && !$humidityOk) return 'danger';
        return 'warning';
    }

    /**
     * Get sensor configuration for ESP32
     */
    public function config(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'config' => [
                'temperature_min' => 18,
                'temperature_max' => 27,
                'humidity_min' => 40,
                'humidity_max' => 60,
                'voltage_min' => 200,
                'voltage_max' => 250,
                'reading_interval' => 30, // seconds
                'location' => 'Server Room BMKG',

                // 🔹 Tambahan konfigurasi batas arus dan daya
                'current_max' => 10,
                'power_max' => 2000,
            ]
        ]);
    }

    /**
     * Get system status showing real-time vs database separation
     */
    public function systemStatus(): JsonResponse
    {
        try {
            $lastSaveTime = Cache::get('last_db_save_time', 0);
            $currentTime = now()->timestamp;
            
            // Calculate next database save
            $nextSaveIn = 3600 - ($currentTime - $lastSaveTime);
            if ($nextSaveIn < 0) $nextSaveIn = 0;
            
            // Get cache status
            $cacheData = Cache::get('latest_sensor_data');
            
            // Get latest database record
            $latestDbRecord = \App\Models\SensorData::latest('recorded_at')->first();
            
            return response()->json([
                'success' => true,
                'system_info' => [
                    'real_time_monitoring' => [
                        'status' => $cacheData ? 'Active' : 'No Data',
                        'last_update' => $cacheData ? $cacheData['last_updated'] : null,
                        'update_interval' => '30 seconds',
                        'source' => 'Memory Cache'
                    ],
                    'database_storage' => [
                        'status' => 'Active',
                        'save_interval' => '1 hour',
                        'last_save' => $lastSaveTime ? date('Y-m-d H:i:s', $lastSaveTime) : 'Never',
                        'next_save_in' => $nextSaveIn > 0 ? gmdate('H:i:s', $nextSaveIn) : 'Due Now',
                        'latest_record' => $latestDbRecord ? $latestDbRecord->recorded_at->format('Y-m-d H:i:s') : 'No Records'
                    ],
                    'data_flow' => [
                        'esp32_sends' => 'Every 30 seconds',
                        'real_time_cache' => 'Updated immediately',
                        'database_save' => 'Every 1 hour',
                        'notifications' => 'Real-time based on cache data'
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting system status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
