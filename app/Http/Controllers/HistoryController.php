<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SensorData::selectRaw('
            DATE(recorded_at) as date,
            COUNT(*) as total_records,
            AVG(temperature) as avg_temperature,
            AVG(humidity) as avg_humidity,
            AVG(voltage) as avg_voltage,
            AVG(current) as avg_current,
            AVG(power) as avg_power,
            SUM(CASE WHEN power_status = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100 as power_uptime
        ')
        ->groupBy('date')
        ->orderBy('date', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereRaw('DATE(recorded_at) LIKE ?', ["%{$search}%"]);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'Normal') {
                $query->havingRaw('AVG(temperature) BETWEEN 18 AND 27 AND AVG(humidity) BETWEEN 40 AND 60');
            } elseif ($status === 'Warning') {
                $query->havingRaw('(AVG(temperature) < 18 OR (AVG(temperature) > 27 AND AVG(temperature) < 35) OR AVG(humidity) < 40 OR (AVG(humidity) > 60 AND AVG(humidity) < 80))');
            } elseif ($status === 'Critical') {
                $query->havingRaw('AVG(temperature) >= 35 OR AVG(humidity) >= 80');
            }
        }

        $historyData = $query->paginate(10);

        // Process the data to add additional fields
        $historyData->getCollection()->transform(function ($item) {
            $date = Carbon::parse($item->date)->setTimezone('Asia/Jakarta');
            $item->day = $date->locale('id')->translatedFormat('l');
            $item->date = $date->format('d/m/Y');
            $item->date_formatted = $date->format('Y-m-d');
            
            // Determine status based on averages
            if ($item->avg_temperature >= 18 && $item->avg_temperature <= 27 && 
                $item->avg_humidity >= 40 && $item->avg_humidity <= 60) {
                $item->status = 'Normal';
            } elseif ($item->avg_temperature >= 35 || $item->avg_humidity >= 80) {
                $item->status = 'Critical';
            } else {
                $item->status = 'Warning';
            }
            
            return $item;
        });

        return view('history', compact('historyData'));
    }

    public function downloadPdf($date)
    {
        // Get all data for the selected date without aggregation
        $data = SensorData::whereDate('recorded_at', $date)
            ->orderBy('recorded_at', 'asc')
            ->get()
            ->filter(function($item) use ($date) {
                // Only include records that are actually from the target date in Jakarta timezone
                $jakartaTime = $item->recorded_at->setTimezone('Asia/Jakarta');
                return $jakartaTime->format('Y-m-d') === $date;
            })
            ->map(function($item) {
                // Convert timezone and add status
                $jakartaTime = $item->recorded_at->setTimezone('Asia/Jakarta');
                
                return [
                    'hour' => $jakartaTime->format('H:i'), // Format as HH:MM (e.g., 13:59)
                    'temperature' => $item->temperature,
                    'humidity' => $item->humidity,
                    'power_status' => $item->power_status,
                    'voltage' => $item->voltage,
                    'power' => $item->power,
                    'recorded_at' => $jakartaTime,
                    'status' => $this->getRecordStatus($item)
                ];
            });

        $pdf = Pdf::loadView('history-pdf', [
            'data' => $data,
            'date' => $date,
            'reportDate' => Carbon::parse($date)->format('d F Y'),
            'avgTemp' => $data->avg('temperature'),
            'avgHumidity' => $data->avg('humidity'),
            'avgVoltage' => $data->avg('voltage'),
            'avgPower' => $data->avg('power')
        ]);

        $filename = 'riwayat-monitoring-' . $date . '.pdf';
        
        return $pdf->download($filename);
    }

    private function getRecordStatus($record)
    {
        $temp = $record->temperature;
        $humidity = $record->humidity;
        $powerStatus = $record->power_status;

        // If power is off, it's critical
        if (!$powerStatus) {
            return 'Critical';
        }

        // Check temperature and humidity ranges
        if ($temp >= 18 && $temp <= 27 && $humidity >= 40 && $humidity <= 60) {
            return 'Normal';
        } elseif ($temp > 27 || $humidity > 60) {
            return 'Critical';
        } else {
            return 'Warning';
        }
    }

    private function getOverallStatus($hourlyData)
    {
        $avgTemp = $hourlyData->avg('temperature');
        $avgHumidity = $hourlyData->avg('humidity');
        $powerStatus = $hourlyData->last()->power_status;

        if (!$powerStatus) {
            return 'Critical';
        }

        $tempOk = $avgTemp >= 18 && $avgTemp <= 27;
        $humidityOk = $avgHumidity >= 40 && $avgHumidity <= 60;

        if ($tempOk && $humidityOk) {
            return 'Normal';
        } elseif ($avgTemp > 27 || $avgHumidity > 60) {
            return 'Critical';
        } else {
            return 'Warning';
        }
    }

    public function apiHistory(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();

        $data = SensorData::whereBetween('recorded_at', [$startDate, $endDate])
            ->orderBy('recorded_at', 'desc')
            ->take(24)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $data->count()
        ]);
    }
}
