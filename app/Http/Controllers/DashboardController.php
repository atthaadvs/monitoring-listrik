<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Get latest sensor data - try cache first, fallback to database
        $cachedData = Cache::get('latest_sensor_data');
        
        if ($cachedData) {
            // Convert cached data to object for view compatibility
            $latestData = (object) $cachedData;
            $latestData->recorded_at = Carbon::parse($cachedData['last_updated']);
            $latestData->temperature_status = $this->getTemperatureStatus($cachedData['temperature']);
            $latestData->humidity_status = $this->getHumidityStatus($cachedData['humidity']);
            $latestData->status_color = $this->getStatusColor($cachedData['temperature'], $cachedData['humidity']);
        } else {
            $latestData = SensorData::latest('recorded_at')->first();
        }
        
        // Get data for charts (last 24 hours) - always from database
        $chartData = SensorData::where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Get alerts
        $alerts = $this->getAlerts($latestData);

        // Get system status
        $systemStatus = $this->getSystemStatus($latestData);

        return view('dashboard', compact('latestData', 'chartData', 'alerts', 'systemStatus'));
    }

    public function apiData()
    {
        // 🔄 REAL-TIME: Get from cache first (real-time data)
        $cachedData = Cache::get('latest_sensor_data');
        
        if ($cachedData) {
            return response()->json([
                'temperature' => $cachedData['temperature'],
                'humidity' => $cachedData['humidity'],
                'power_status' => $cachedData['power_status'],
                'voltage' => $cachedData['voltage'] ?? 0,
                'power' => $cachedData['power'] ?? 0,
                'temperature_status' => $this->getTemperatureStatus($cachedData['temperature']),
                'humidity_status' => $this->getHumidityStatus($cachedData['humidity']),
                'status_color' => $this->getStatusColor($cachedData['temperature'], $cachedData['humidity']),
                'last_update' => $cachedData['last_updated'],
                'source' => 'real-time-cache'
            ]);
        }
        
        // 🗄️ FALLBACK: Get from database if no cache
        $latestData = SensorData::latest('recorded_at')->first();
        
        if (!$latestData) {
            return response()->json([
                'temperature' => 0,
                'humidity' => 0,
                'power_status' => false,
                'voltage' => 0,
                'power' => 0,
                'temperature_status' => 'no-data',
                'humidity_status' => 'no-data',
                'status_color' => 'gray',
                'last_update' => now(),
                'source' => 'no-data'
            ]);
        }
        
        return response()->json([
            'temperature' => $latestData->temperature ?? 0,
            'humidity' => $latestData->humidity ?? 0,
            'power_status' => $latestData->power_status ?? false,
            'voltage' => $latestData->voltage ?? 0,
            'power' => $latestData->power ?? 0,
            'temperature_status' => $latestData->temperature_status ?? 'normal',
            'humidity_status' => $latestData->humidity_status ?? 'normal',
            'status_color' => $latestData->status_color ?? 'green',
            'last_update' => $latestData->recorded_at ?? now(),
            'source' => 'database-fallback'
        ]);
    }

    public function chartData()
    {
        $data = SensorData::where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('recorded_at', 'asc')
            ->get();

        $chartData = [
            'labels' => $data->pluck('recorded_at')->map(function($date) {
                return $date->format('H:i');
            }),
            'temperature' => $data->pluck('temperature'),
            'humidity' => $data->pluck('humidity')
        ];

        return response()->json($chartData);
    }

    private function getAlerts($latestData)
    {
        $alerts = [];
        
        if (!$latestData) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Tidak ada data sensor yang tersedia'
            ];
            return $alerts;
        }

        if ($latestData->temperature > 27) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Suhu terlalu tinggi: {$latestData->temperature}°C (Maks: 27°C)"
            ];
        } elseif ($latestData->temperature < 18) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Suhu terlalu rendah: {$latestData->temperature}°C (Min: 18°C)"
            ];
        }

        if ($latestData->humidity > 60) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Kelembaban terlalu tinggi: {$latestData->humidity}% (Maks: 60%)"
            ];
        } elseif ($latestData->humidity < 40) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Kelembaban terlalu rendah: {$latestData->humidity}% (Min: 40%)"
            ];
        }

        if (!$latestData->power_status) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Status listrik: OFF'
            ];
        }

        return $alerts;
    }

    private function getSystemStatus($latestData)
    {
        if (!$latestData) {
            return [
                'status' => 'offline',
                'color' => 'red',
                'message' => 'Sistem Offline'
            ];
        }

        $tempOk = $latestData->temperature >= 18 && $latestData->temperature <= 27;
        $humidityOk = $latestData->humidity >= 40 && $latestData->humidity <= 60;
        $powerOk = $latestData->power_status;

        if ($tempOk && $humidityOk && $powerOk) {
            return [
                'status' => 'normal',
                'color' => 'green',
                'message' => 'Sistem Normal'
            ];
        } elseif (!$powerOk || $latestData->temperature > 27 || $latestData->humidity > 60) {
            return [
                'status' => 'critical',
                'color' => 'red',
                'message' => 'Sistem Critical'
            ];
        } else {
            return [
                'status' => 'warning',
                'color' => 'yellow',
                'message' => 'Sistem Warning'
            ];
        }
    }

    private function getTemperatureStatus($temperature)
    {
        if ($temperature < 18) return 'low';
        if ($temperature > 27) return 'high';
        return 'normal';
    }

    private function getHumidityStatus($humidity)
    {
        if ($humidity < 40) return 'low';
        if ($humidity > 60) return 'high';
        return 'normal';
    }

    private function getStatusColor($temperature, $humidity)
    {
        $tempOk = ($temperature >= 18 && $temperature <= 27);
        $humidityOk = ($humidity >= 40 && $humidity <= 60);
        
        if ($tempOk && $humidityOk) return 'green';
        if (!$tempOk && !$humidityOk) return 'red';
        return 'yellow';
    }
}
