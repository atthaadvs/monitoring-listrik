<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan BMKG - {{ $monthName }} {{ $year }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #0EA5E9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 20px;
            color: #0EA5E9;
            margin: 0;
        }
        
        .header h2 {
            font-size: 16px;
            color: #666;
            margin: 5px 0;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            background: #0EA5E9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .summary {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 5px solid #0EA5E9;
        }
        
        .summary h3 {
            color: #0EA5E9;
            margin: 0 0 15px 0;
            font-size: 14px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .summary-item {
            text-align: center;
            background-color: #fff;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #0284C7;
            margin-bottom: 5px;
        }
        
        .summary-label {
            color: #64748b;
            font-size: 9px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 9px;
        }
        
        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #0EA5E9;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .status-normal { 
            background-color: #dcfce7; 
            color: #16a34a;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-warning { 
            background-color: #fef3c7; 
            color: #d97706;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-critical { 
            background-color: #fecaca; 
            color: #dc2626;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #64748b;
            font-size: 8px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .extremes-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        
        .extreme-box {
            background: #fff;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        
        .extreme-title {
            font-weight: bold;
            color: #0284C7;
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .extreme-item {
            margin: 5px 0;
            font-size: 9px;
        }
        
        .chart-section {
            margin: 30px 0;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .chart-title {
            font-size: 12px;
            font-weight: bold;
            color: #0284C7;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .chart-container {
            margin: 20px 0;
        }
        
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .chart-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            background: #fafafa;
        }
        
        .chart-header {
            font-size: 10px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .bar-chart {
            display: flex;
            align-items: end;
            justify-content: space-between;
            height: 120px;
            margin: 10px 0;
            padding: 5px;
            border-bottom: 1px solid #d1d5db;
        }
        
        .bar {
            width: 8px;
            background: linear-gradient(to top, #0ea5e9, #0284c7);
            margin: 0 1px;
            border-radius: 2px 2px 0 0;
            position: relative;
        }
        
        .bar-temp {
            background: linear-gradient(to top, #ef4444, #dc2626);
        }
        
        .bar-humidity {
            background: linear-gradient(to top, #06b6d4, #0891b2);
        }
        
        .chart-legend {
            display: flex;
            justify-content: center;
            margin-top: 10px;
            font-size: 8px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin: 0 10px;
        }
        
        .legend-color {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            margin-right: 5px;
        }
        
        .temp-legend { background: linear-gradient(to right, #ef4444, #dc2626); }
        .humidity-legend { background: linear-gradient(to right, #06b6d4, #0891b2); }
        
        .chart-stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
            font-size: 8px;
            text-align: center;
        }
        
        .stat-box {
            background: #f8fafc;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }
        
        .stat-value {
            font-weight: bold;
            color: #0284c7;
            font-size: 10px;
        }
        
        .stat-label {
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">B</div>
        <h1>LAPORAN MONITORING BULANAN</h1>
        <h2>Badan Meteorologi, Klimatologi, dan Geofisika</h2>
        <h2>{{ $monthName }} {{ $year }}</h2>
        <div style="margin-top: 15px; font-size: 9px; color: #64748b;">
            Digenerate pada: {{ $generatedAt }} WIB
        </div>
    </div>

    <div class="summary">
        <h3>📈 RINGKASAN BULANAN</h3>
        
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $summary['total_days'] }}</div>
                <div class="summary-label">Hari Pemantauan</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($summary['total_records']) }}</div>
                <div class="summary-label">Total Data</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($summary['avg_temperature'], 1) }}°C</div>
                <div class="summary-label">Rata-rata Suhu</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($summary['avg_humidity'], 1) }}%</div>
                <div class="summary-label">Rata-rata Kelembaban</div>
            </div>
        </div>

        <div class="extremes-grid">
            <div class="extreme-box">
                <div class="extreme-title">🌡️ Kondisi Suhu</div>
                <div class="extreme-item">Tertinggi: <strong>{{ number_format($summary['max_temperature'], 1) }}°C</strong></div>
                <div class="extreme-item">Terendah: <strong>{{ number_format($summary['min_temperature'], 1) }}°C</strong></div>
                <div class="extreme-item">Rata-rata: <strong>{{ number_format($summary['avg_temperature'], 1) }}°C</strong></div>
            </div>
            <div class="extreme-box">
                <div class="extreme-title">💧 Kondisi Kelembaban</div>
                <div class="extreme-item">Tertinggi: <strong>{{ number_format($summary['max_humidity'], 1) }}%</strong></div>
                <div class="extreme-item">Terendah: <strong>{{ number_format($summary['min_humidity'], 1) }}%</strong></div>
                <div class="extreme-item">Rata-rata: <strong>{{ number_format($summary['avg_humidity'], 1) }}%</strong></div>
            </div>
        </div>

        <div class="extremes-grid">
            <div class="extreme-box">
                <div class="extreme-title">📊 Status Harian</div>
                <div class="extreme-item">Normal: <strong>{{ $summary['normal_days'] }} hari</strong></div>
                <div class="extreme-item">Warning: <strong>{{ $summary['warning_days'] }} hari</strong></div>
                <div class="extreme-item">Critical: <strong>{{ $summary['critical_days'] }} hari</strong></div>
            </div>
            <div class="extreme-box">
                <div class="extreme-title">⚡ Sistem</div>
                <div class="extreme-item">Uptime: <strong>{{ number_format($summary['system_uptime'], 1) }}%</strong></div>
                <div class="extreme-item">Monitoring: <strong>{{ $summary['total_days'] }}/{{ \Carbon\Carbon::create($year, $month, 1)->daysInMonth }} hari</strong></div>
            </div>
        </div>
    </div>

    <!-- Grafik Section -->
    <div class="chart-section">
        <div class="chart-title">📊 GRAFIK TREN BULANAN</div>
        
        <div class="chart-grid">
            <!-- Temperature Chart -->
            <div class="chart-box">
                <div class="chart-header">🌡️ Tren Suhu Harian (°C)</div>
                <div class="bar-chart">
                    @if($dailyData->count() > 0)
                        @php
                            $maxTemp = $dailyData->max('avg_temperature');
                            $minTemp = $dailyData->min('avg_temperature');
                            $tempRange = $maxTemp - $minTemp;
                        @endphp
                        @foreach($dailyData->take(31) as $day)
                            @php
                                $height = $tempRange > 0 ? (($day['avg_temperature'] - $minTemp) / $tempRange) * 100 : 50;
                                $height = max(10, min(100, $height));
                            @endphp
                            <div class="bar bar-temp" style="height: {{ $height }}%; title='{{ $day['date'] }}: {{ $day['avg_temperature'] }}°C'"></div>
                        @endforeach
                    @endif
                </div>
                <div class="chart-stats">
                    <div class="stat-box">
                        <div class="stat-value">{{ number_format($summary['max_temperature'], 1) }}°C</div>
                        <div class="stat-label">Maksimal</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">{{ number_format($summary['avg_temperature'], 1) }}°C</div>
                        <div class="stat-label">Rata-rata</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">{{ number_format($summary['min_temperature'], 1) }}°C</div>
                        <div class="stat-label">Minimal</div>
                    </div>
                </div>
            </div>

            <!-- Humidity Chart -->
            <div class="chart-box">
                <div class="chart-header">💧 Tren Kelembaban Harian (%)</div>
                <div class="bar-chart">
                    @if($dailyData->count() > 0)
                        @php
                            $maxHumidity = $dailyData->max('avg_humidity');
                            $minHumidity = $dailyData->min('avg_humidity');
                            $humidityRange = $maxHumidity - $minHumidity;
                        @endphp
                        @foreach($dailyData->take(31) as $day)
                            @php
                                $height = $humidityRange > 0 ? (($day['avg_humidity'] - $minHumidity) / $humidityRange) * 100 : 50;
                                $height = max(10, min(100, $height));
                            @endphp
                            <div class="bar bar-humidity" style="height: {{ $height }}%; title='{{ $day['date'] }}: {{ $day['avg_humidity'] }}%'"></div>
                        @endforeach
                    @endif
                </div>
                <div class="chart-stats">
                    <div class="stat-box">
                        <div class="stat-value">{{ number_format($summary['max_humidity'], 1) }}%</div>
                        <div class="stat-label">Maksimal</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">{{ number_format($summary['avg_humidity'], 1) }}%</div>
                        <div class="stat-label">Rata-rata</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">{{ number_format($summary['min_humidity'], 1) }}%</div>
                        <div class="stat-label">Minimal</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined Trend Analysis -->
        <div style="margin-top: 20px; padding: 15px; background: #f8fafc; border-radius: 6px; border-left: 4px solid #0ea5e9;">
            <h4 style="color: #0284c7; margin: 0 0 10px 0; font-size: 10px;">📈 ANALISIS TREN</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 8px;">
                <div>
                    <strong>Kondisi Suhu:</strong><br>
                    • Range: {{ number_format($summary['min_temperature'], 1) }}°C - {{ number_format($summary['max_temperature'], 1) }}°C<br>
                    • Stabilitas: 
                    @if(($summary['max_temperature'] - $summary['min_temperature']) < 5)
                        Sangat Stabil
                    @elseif(($summary['max_temperature'] - $summary['min_temperature']) < 10)
                        Stabil
                    @else
                        Fluktuatif
                    @endif
                </div>
                <div>
                    <strong>Kondisi Kelembaban:</strong><br>
                    • Range: {{ number_format($summary['min_humidity'], 1) }}% - {{ number_format($summary['max_humidity'], 1) }}%<br>
                    • Stabilitas: 
                    @if(($summary['max_humidity'] - $summary['min_humidity']) < 10)
                        Sangat Stabil
                    @elseif(($summary['max_humidity'] - $summary['min_humidity']) < 20)
                        Stabil
                    @else
                        Fluktuatif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($dailyData->count() > 0)
    <h3 style="color: #0EA5E9; margin-bottom: 15px;">📅 DATA HARIAN DETAIL</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Suhu (°C)</th>
                <th>Kelembaban (%)</th>
                <th>Tegangan (V)</th>
                <th>Arus (A)</th>
                <th>Daya (W)</th>
                <th>Records</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyData as $day)
            <tr>
                <td>{{ $day['date'] }}</td>
                <td>{{ $day['day'] }}</td>
                <td>{{ $day['avg_temperature'] }}</td>
                <td>{{ $day['avg_humidity'] }}</td>
                <td>{{ $day['avg_voltage'] }}</td>
                <td>{{ $day['avg_current'] }}</td>
                <td>{{ $day['avg_power'] }}</td>
                <td>{{ $day['total_records'] }}</td>
                <td>
                    <span class="status-{{ strtolower($day['status']) }}">
                        {{ $day['status'] }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Daily Status Chart -->
    <div class="chart-section" style="margin: 30px 0;">
        <div class="chart-title">📊 DISTRIBUSI STATUS HARIAN</div>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <!-- Status Distribution Chart -->
            <div class="chart-box">
                <div class="chart-header">📈 Status Harian ({{ $summary['total_days'] }} Hari)</div>
                
                <!-- Pie Chart Simulation -->
                <div style="display: flex; justify-content: center; margin: 20px 0;">
                    @php
                        $totalDaysInMonth = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;
                        $normalPct = $summary['total_days'] > 0 ? ($summary['normal_days'] / $summary['total_days']) * 100 : 0;
                        $warningPct = $summary['total_days'] > 0 ? ($summary['warning_days'] / $summary['total_days']) * 100 : 0;
                        $criticalPct = $summary['total_days'] > 0 ? ($summary['critical_days'] / $summary['total_days']) * 100 : 0;
                    @endphp
                    
                    <div style="width: 150px; height: 150px; border-radius: 50%; background: conic-gradient(
                        #10b981 0% {{ $normalPct }}%, 
                        #f59e0b {{ $normalPct }}% {{ $normalPct + $warningPct }}%, 
                        #ef4444 {{ $normalPct + $warningPct }}% 100%
                    ); position: relative;">
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; color: #374151;">
                            {{ $summary['total_days'] }}<br><span style="font-size: 8px;">hari</span>
                        </div>
                    </div>
                </div>
                
                <!-- Legend -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-top: 15px;">
                    <div style="text-align: center; font-size: 8px;">
                        <div style="width: 15px; height: 15px; background: #10b981; border-radius: 3px; margin: 0 auto 5px;"></div>
                        <div><strong>{{ $summary['normal_days'] }}</strong> Normal</div>
                        <div>({{ number_format($normalPct, 1) }}%)</div>
                    </div>
                    <div style="text-align: center; font-size: 8px;">
                        <div style="width: 15px; height: 15px; background: #f59e0b; border-radius: 3px; margin: 0 auto 5px;"></div>
                        <div><strong>{{ $summary['warning_days'] }}</strong> Warning</div>
                        <div>({{ number_format($warningPct, 1) }}%)</div>
                    </div>
                    <div style="text-align: center; font-size: 8px;">
                        <div style="width: 15px; height: 15px; background: #ef4444; border-radius: 3px; margin: 0 auto 5px;"></div>
                        <div><strong>{{ $summary['critical_days'] }}</strong> Critical</div>
                        <div>({{ number_format($criticalPct, 1) }}%)</div>
                    </div>
                </div>
            </div>

            <!-- Monthly Performance Summary -->
            <div class="chart-box">
                <div class="chart-header">🎯 Performa Bulanan</div>
                
                <div style="margin: 15px 0;">
                    <!-- Overall Score Bar -->
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 8px; margin-bottom: 5px;">Skor Keseluruhan:</div>
                        @php
                            $overallScore = $summary['total_days'] > 0 ? 
                                (($summary['normal_days'] * 100) + ($summary['warning_days'] * 70) + ($summary['critical_days'] * 30)) / $summary['total_days'] : 0;
                        @endphp
                        <div style="background: #e5e7eb; height: 12px; border-radius: 6px; overflow: hidden;">
                            <div style="background: linear-gradient(to right, #ef4444, #f59e0b, #10b981); height: 100%; width: {{ $overallScore }}%; border-radius: 6px;"></div>
                        </div>
                        <div style="font-size: 10px; font-weight: bold; color: #0284c7; margin-top: 5px;">
                            {{ number_format($overallScore, 1) }}/100
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div style="font-size: 8px; line-height: 1.8;">
                        <div style="display: flex; justify-content: space-between; margin: 5px 0;">
                            <span>Hari Monitoring:</span>
                            <strong>{{ $summary['total_days'] }}/{{ $totalDaysInMonth }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin: 5px 0;">
                            <span>Coverage:</span>
                            <strong>{{ number_format(($summary['total_days']/$totalDaysInMonth)*100, 1) }}%</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin: 5px 0;">
                            <span>Uptime System:</span>
                            <strong>{{ number_format($summary['system_uptime'], 1) }}%</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin: 5px 0;">
                            <span>Total Records:</span>
                            <strong>{{ number_format($summary['total_records']) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h4 style="color: #0284C7; margin: 0 0 15px 0; font-size: 12px;">📋 INFORMASI TEKNIS</h4>
        <div style="font-size: 9px; line-height: 1.6;">
            <strong>Parameter Normal:</strong><br>
            • Suhu: 18°C - 27°C<br>
            • Kelembaban: 40% - 60%<br>
            • Power Status: Selalu ON untuk operasi normal<br><br>
            
            <strong>Sistem Monitoring:</strong><br>
            • Data dikumpulkan secara real-time dari sensor ESP32<br>
            • Backup otomatis setiap hari<br>
            • Notifikasi email untuk kondisi abnormal<br>
            • Laporan bulanan otomatis setiap akhir bulan<br><br>
            
            <strong>Kontak Teknis:</strong><br>
            • Email: admin@bmkg.go.id<br>
            • Monitoring: monitoring@bmkg.go.id
        </div>
    </div>

    <div class="footer">
        <p>&copy; {{ $year }} BMKG Monitoring System. All rights reserved.</p>
        <p>Sistem monitoring cuaca dan iklim otomatis - Generated {{ $generatedAt }} WIB</p>
    </div>
</body>
</html>
