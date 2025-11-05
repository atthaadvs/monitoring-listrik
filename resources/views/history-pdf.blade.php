<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring Ruang Server - {{ $reportDate }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #0EA5E9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #0EA5E9;
            margin: 0 0 10px 0;
        }
        
        .header h2 {
            font-size: 16px;
            color: #666;
            margin: 0 0 5px 0;
        }
        
        .header p {
            margin: 0;
            color: #888;
        }
        
        .info-box {
            background-color: #F0F9FF;
            border: 1px solid #0EA5E9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .info-row:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #0369A1;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #0EA5E9;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #F8FAFC;
        }
        
        .status-normal {
            background-color: #10B981;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
        }
        
        .status-warning {
            background-color: #F59E0B;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
        }
        
        .status-critical {
            background-color: #EF4444;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
        }
        
        .power-on {
            color: #10B981;
            font-weight: bold;
        }
        
        .power-off {
            color: #EF4444;
            font-weight: bold;
        }
        
        .temp-normal { color: #10B981; font-weight: bold; }
        .temp-warning { color: #F59E0B; font-weight: bold; }
        .temp-critical { color: #EF4444; font-weight: bold; }
        
        .humidity-normal { color: #10B981; font-weight: bold; }
        .humidity-warning { color: #F59E0B; font-weight: bold; }
        .humidity-critical { color: #EF4444; font-weight: bold; }
        
        .summary {
            margin-top: 30px;
            border-top: 2px solid #0EA5E9;
            padding-top: 20px;
        }
        
        .summary h3 {
            color: #0EA5E9;
            margin-bottom: 15px;
        }
        
        .summary-grid {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .summary-item {
            text-align: center;
            background-color: #F8FAFC;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            flex: 1;
            min-width: 120px;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #0EA5E9;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN MONITORING RUANG SERVER</h1>
        <h2>Badan Meteorologi, Klimatologi, dan Geofisika</h2>
        <p>Sistem Monitoring Kondisi Listrik, Suhu dan Kelembaban</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Tanggal Laporan:</span>
            <span>{{ $reportDate }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Periode Data:</span>
            <span>24 Jam (00:00 - 23:59)</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Data Points:</span>
            <span>{{ $data->count() }} record</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Generate:</span>
            <span>{{ \Carbon\Carbon::now()->format('d F Y, H:i:s') }} WIB</span>
        </div>
    </div>

    <h3 style="color: #0EA5E9; margin-bottom: 15px;">Data Monitoring Per Jam</h3>
    
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Suhu (°C)</th>
                <th>Kelembaban (%)</th>
                <th>Tegangan (V)</th>
                <th>Daya (W)</th>
                <th>Status Listrik</th>
                <th>Status Sistem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $record)
            <tr>
                <td>{{ $record['recorded_at']->format('H:i') }}</td>
                <td class="@if($record['temperature'] >= 18 && $record['temperature'] <= 27) temp-normal @elseif($record['temperature'] > 27) temp-critical @else temp-warning @endif">
                    {{ $record['temperature'] }}°C
                </td>
                <td class="@if($record['humidity'] >= 40 && $record['humidity'] <= 60) humidity-normal @elseif($record['humidity'] > 60) humidity-critical @else humidity-warning @endif">
                    {{ $record['humidity'] }}%
                </td>
                <td>{{ $record['voltage'] }}V</td>
                <td>{{ $record['power'] }}W</td>
                <td class="@if($record['power_status']) power-on @else power-off @endif">
                    @if($record['power_status']) ON @else OFF @endif
                </td>
                <td>
                    <span class="status-{{ strtolower($record['status']) }}">
                        {{ $record['status'] }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Ringkasan Statistik</h3>
        
        @php
            $avgTemp = $data->avg('temperature');
            $avgHumidity = $data->avg('humidity');
            $avgVoltage = $data->avg('voltage');
            $avgPower = $data->avg('power');
            $maxTemp = $data->max('temperature');
            $minTemp = $data->min('temperature');
            $maxHumidity = $data->max('humidity');
            $minHumidity = $data->min('humidity');
            $powerUptime = $data->where('power_status', true)->count() / $data->count() * 100;
            $normalCount = $data->where('status', 'Normal')->count();
            $warningCount = $data->where('status', 'Warning')->count();
            $criticalCount = $data->where('status', 'Critical')->count();
        @endphp

        <div class="summary-grid">
            <div class="summary-item">
                <div>Rata-rata Suhu</div>
                <div class="summary-value">{{ round($avgTemp, 1) }}°C</div>
            </div>
            <div class="summary-item">
                <div>Rata-rata Kelembaban</div>
                <div class="summary-value">{{ round($avgHumidity, 1) }}%</div>
            </div>
            <div class="summary-item">
                <div>Rata-rata Tegangan</div>
                <div class="summary-value">{{ round($avgVoltage, 1) }}V</div>
            </div>
            <div class="summary-item">
                <div>Rata-rata Daya</div>
                <div class="summary-value">{{ round($avgPower, 1) }}W</div>
            </div>
        </div>

        <table style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th colspan="2">Detail Statistik</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Suhu Tertinggi</strong></td>
                    <td class="@if($maxTemp > 27) temp-critical @else temp-normal @endif">{{ $maxTemp }}°C</td>
                </tr>
                <tr>
                    <td><strong>Suhu Terendah</strong></td>
                    <td class="@if($minTemp < 18) temp-warning @else temp-normal @endif">{{ $minTemp }}°C</td>
                </tr>
                <tr>
                    <td><strong>Kelembaban Tertinggi</strong></td>
                    <td class="@if($maxHumidity > 60) humidity-critical @else humidity-normal @endif">{{ $maxHumidity }}%</td>
                </tr>
                <tr>
                    <td><strong>Kelembaban Terendah</strong></td>
                    <td class="@if($minHumidity < 40) humidity-warning @else humidity-normal @endif">{{ $minHumidity }}%</td>
                </tr>
                <tr>
                    <td><strong>Uptime Listrik</strong></td>
                    <td class="@if($powerUptime >= 95) power-on @else power-off @endif">{{ round($powerUptime, 1) }}%</td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="2">Status Sistem</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Status Normal</strong></td>
                    <td style="color: #10B981;">{{ $normalCount }} jam ({{ round($normalCount/$data->count()*100, 1) }}%)</td>
                </tr>
                <tr>
                    <td><strong>Status Warning</strong></td>
                    <td style="color: #F59E0B;">{{ $warningCount }} jam ({{ round($warningCount/$data->count()*100, 1) }}%)</td>
                </tr>
                <tr>
                    <td><strong>Status Critical</strong></td>
                    <td style="color: #EF4444;">{{ $criticalCount }} jam ({{ round($criticalCount/$data->count()*100, 1) }}%)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Monitoring Ruang Server BMKG</p>
        <p>{{ \Carbon\Carbon::now()->format('d F Y, H:i:s') }} WIB</p>
    </div>
</body>
</html>
