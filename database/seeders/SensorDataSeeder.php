<?php

namespace Database\Seeders;

use App\Models\SensorData;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SensorDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate dummy data for the last 24 hours
        $now = Carbon::now();
        
        for ($i = 24; $i >= 0; $i--) {
            $timestamp = $now->copy()->subHours($i);
            
            // Generate realistic server room data
            $baseTemp = 22; // Base temperature
            $baseHumidity = 50; // Base humidity
            
            // Add some variation
            $tempVariation = rand(-5, 8);
            $humidityVariation = rand(-15, 20);
            
            $temperature = $baseTemp + $tempVariation;
            $humidity = $baseHumidity + $humidityVariation;
            
            // Ensure values are within reasonable bounds
            $temperature = max(15, min(35, $temperature));
            $humidity = max(20, min(80, $humidity));
            
            SensorData::create([
                'temperature' => $temperature,
                'humidity' => $humidity,
                'power_status' => rand(0, 100) > 5, // 95% chance power is on
                'voltage' => rand(220, 240),
                'location' => 'Server Room BMKG',
                'recorded_at' => $timestamp
            ]);
        }
        
        // Add some recent data with more frequent updates
        for ($i = 60; $i >= 0; $i -= 5) {
            $timestamp = $now->copy()->subMinutes($i);
            
            $temperature = rand(20, 28);
            $humidity = rand(35, 65);
            
            SensorData::create([
                'temperature' => $temperature,
                'humidity' => $humidity,
                'power_status' => true,
                'voltage' => rand(220, 240),
                'location' => 'Server Room BMKG',
                'recorded_at' => $timestamp
            ]);
        }
    }
}
