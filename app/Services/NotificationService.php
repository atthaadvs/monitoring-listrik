<?php

namespace App\Services;

use App\Models\SensorData;
use App\Mail\SensorAlert;
use App\Mail\MonthlyReport;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class NotificationService
{
    protected $adminEmails = [
        'admin@bmkg.go.id',
        'monitoring@bmkg.go.id'
        // Tambahkan email admin lainnya di sini
    ];

    public function checkSensorConditions($sensorData)
    {
        $alerts = [];

        // Handle both object and array data structures
        $temperature = is_object($sensorData) ? $sensorData->temperature : $sensorData['temperature'];
        $humidity = is_object($sensorData) ? $sensorData->humidity : $sensorData['humidity'];
        $power = is_object($sensorData) ? $sensorData->power : $sensorData['power'];
        $location = is_object($sensorData) ? $sensorData->location : $sensorData['location'];

        // Check temperature conditions
        if ($temperature < 18 || $temperature > 27) {
            $alerts[] = [
                'type' => 'temperature',
                'message' => $this->getTemperatureMessage($temperature)
            ];
        }

        // Check humidity conditions
        if ($humidity < 40 || $humidity > 60) {
            $alerts[] = [
                'type' => 'humidity',
                'message' => $this->getHumidityMessage($humidity)
            ];
        }

        // Check power status
        $powerStatus = is_object($sensorData) ? $sensorData->power_status : $sensorData['power_status'];
        if (!$powerStatus) {
            $alerts[] = [
                'type' => 'power',
                'message' => 'Sistem power dalam kondisi OFF! Perangkat monitoring tidak mendapat daya listrik.'
            ];
        }

        // Send alerts if any
        foreach ($alerts as $alert) {
            $this->sendAlert($alert['type'], $sensorData, $alert['message']);
        }

        return count($alerts) > 0;
    }

    protected function getTemperatureMessage($temperature)
    {
        if ($temperature < 18) {
            return "Suhu terlalu rendah ({$temperature}°C). Suhu normal: 18°C - 27°C. Segera periksa kondisi lingkungan!";
        } else {
            return "Suhu terlalu tinggi ({$temperature}°C). Suhu normal: 18°C - 27°C. Segera periksa kondisi lingkungan!";
        }
    }

    protected function getHumidityMessage($humidity)
    {
        if ($humidity < 40) {
            return "Kelembaban terlalu rendah ({$humidity}%). Kelembaban normal: 40% - 60%. Segera periksa kondisi lingkungan!";
        } else {
            return "Kelembaban terlalu tinggi ({$humidity}%). Kelembaban normal: 40% - 60%. Segera periksa kondisi lingkungan!";
        }
    }

    protected function sendAlert($alertType, $sensorData, $message)
    {
        try {
            foreach ($this->adminEmails as $email) {
                Mail::to($email)->send(new SensorAlert($alertType, $sensorData, $message));
            }
            
            \Log::info("Alert sent for {$alertType}: {$message}");
        } catch (\Exception $e) {
            \Log::error("Failed to send alert email: " . $e->getMessage());
        }
    }

    public function generateMonthlyReport($month, $year)
    {
        try {
            // Get monthly data
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $monthlyData = SensorData::whereBetween('recorded_at', [$startDate, $endDate])
                ->orderBy('recorded_at', 'asc')
                ->get();

            if ($monthlyData->isEmpty()) {
                \Log::info("No data available for monthly report: {$year}-{$month}");
                return false;
            }

            // Calculate summary statistics
            $summary = $this->calculateMonthlySummary($monthlyData, $month, $year);

            // Generate daily summary data for PDF
            $dailySummary = $this->getDailySummary($monthlyData);

            // Generate PDF
            $pdf = Pdf::loadView('reports.monthly-report', [
                'month' => $month,
                'year' => $year,
                'monthName' => Carbon::create($year, $month, 1)->locale('id')->translatedFormat('F'),
                'summary' => $summary,
                'dailyData' => $dailySummary,
                'generatedAt' => Carbon::now('Asia/Jakarta')->format('d F Y, H:i:s')
            ]);

            // Save PDF to storage
            $fileName = "laporan_bulanan_{$year}_{$month}.pdf";
            $filePath = storage_path("app/reports/{$fileName}");
            
            // Create directory if not exists
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            $pdf->save($filePath);

            // Send email with PDF attachment
            $this->sendMonthlyReport($month, $year, $filePath, $summary);

            return $filePath;

        } catch (\Exception $e) {
            \Log::error("Failed to generate monthly report: " . $e->getMessage());
            return false;
        }
    }

    protected function calculateMonthlySummary($data, $month, $year)
    {
        $totalDays = Carbon::create($year, $month, 1)->daysInMonth;
        $daysWithData = $data->groupBy(function($item) {
            return $item->recorded_at->format('Y-m-d');
        })->count();

        $summary = [
            'total_days' => $daysWithData,
            'total_records' => $data->count(),
            'avg_temperature' => $data->avg('temperature'),
            'min_temperature' => $data->min('temperature'),
            'max_temperature' => $data->max('temperature'),
            'avg_humidity' => $data->avg('humidity'),
            'min_humidity' => $data->min('humidity'),
            'max_humidity' => $data->max('humidity'),
            'system_uptime' => ($daysWithData / $totalDays) * 100,
        ];

        // Calculate status days
        $dailyStatus = $data->groupBy(function($item) {
            return $item->recorded_at->format('Y-m-d');
        })->map(function($dayData) {
            $avgTemp = $dayData->avg('temperature');
            $avgHumidity = $dayData->avg('humidity');
            
            if ($avgTemp >= 18 && $avgTemp <= 27 && $avgHumidity >= 40 && $avgHumidity <= 60) {
                return 'normal';
            } elseif ($avgTemp > 27 || $avgHumidity > 60) {
                return 'critical';
            } else {
                return 'warning';
            }
        });

        $summary['normal_days'] = $dailyStatus->filter(fn($status) => $status === 'normal')->count();
        $summary['warning_days'] = $dailyStatus->filter(fn($status) => $status === 'warning')->count();
        $summary['critical_days'] = $dailyStatus->filter(fn($status) => $status === 'critical')->count();

        return $summary;
    }

    protected function getDailySummary($data)
    {
        return $data->groupBy(function($item) {
            return $item->recorded_at->format('Y-m-d');
        })->map(function($dayData, $date) {
            return [
                'date' => Carbon::parse($date)->format('d/m/Y'),
                'day' => Carbon::parse($date)->locale('id')->translatedFormat('l'),
                'avg_temperature' => round($dayData->avg('temperature'), 1),
                'avg_humidity' => round($dayData->avg('humidity'), 1),
                'avg_voltage' => round($dayData->avg('voltage'), 1),
                'avg_current' => round($dayData->avg('current'), 2),
                'avg_power' => round($dayData->avg('power'), 1),
                'total_records' => $dayData->count(),
                'status' => $this->getDayStatus($dayData)
            ];
        })->values();
    }

    protected function getDayStatus($dayData)
    {
        $avgTemp = $dayData->avg('temperature');
        $avgHumidity = $dayData->avg('humidity');
        
        if ($avgTemp >= 18 && $avgTemp <= 27 && $avgHumidity >= 40 && $avgHumidity <= 60) {
            return 'Normal';
        } elseif ($avgTemp > 27 || $avgHumidity > 60) {
            return 'Critical';
        } else {
            return 'Warning';
        }
    }

    protected function sendMonthlyReport($month, $year, $filePath, $summary)
    {
        try {
            foreach ($this->adminEmails as $email) {
                Mail::to($email)->send(new MonthlyReport($month, $year, $filePath, $summary));
            }
            
            \Log::info("Monthly report sent for {$year}-{$month}");
        } catch (\Exception $e) {
            \Log::error("Failed to send monthly report email: " . $e->getMessage());
        }
    }
}
