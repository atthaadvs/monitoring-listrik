<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Sensor BMKG</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 5px solid;
        }
        .alert-critical {
            background-color: #fef2f2;
            border-color: #ef4444;
            color: #dc2626;
        }
        .alert-warning {
            background-color: #fffbeb;
            border-color: #f59e0b;
            color: #d97706;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #f8fafc;
            border-radius: 8px;
            overflow: hidden;
        }
        .data-table th, .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-table th {
            background: #64748b;
            color: white;
            font-weight: 600;
        }
        .footer {
            background: #64748b;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-critical { background: #fef2f2; color: #dc2626; }
        .status-warning { background: #fffbeb; color: #d97706; }
        .status-normal { background: #f0fdf4; color: #16a34a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 PERINGATAN SISTEM MONITORING</h1>
            <p>Badan Meteorologi, Klimatologi, dan Geofisika</p>
        </div>

        <div class="content">
            <div class="alert-box {{ $alertType === 'power' ? 'alert-critical' : 'alert-warning' }}">
                <h3>
                    @if($alertType === 'temperature')
                        🌡️ Peringatan Suhu Abnormal
                    @elseif($alertType === 'humidity')
                        💧 Peringatan Kelembaban Abnormal
                    @else
                        ⚡ Peringatan Power System
                    @endif
                </h3>
                <p><strong>{{ $message }}</strong></p>
            </div>

            <h4>📊 Data Sensor Saat Ini:</h4>
            <table class="data-table">
                <tr>
                    <th>Parameter</th>
                    <th>Nilai</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>🌡️ Suhu</td>
                    <td>{{ $sensorData->temperature }}°C</td>
                    <td>
                        <span class="status-badge {{ $sensorData->temperature >= 18 && $sensorData->temperature <= 27 ? 'status-normal' : ($sensorData->temperature > 27 ? 'status-critical' : 'status-warning') }}">
                            @if($sensorData->temperature >= 18 && $sensorData->temperature <= 27)
                                Normal
                            @elseif($sensorData->temperature > 27)
                                Tinggi
                            @else
                                Rendah
                            @endif
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>💧 Kelembaban</td>
                    <td>{{ $sensorData->humidity }}%</td>
                    <td>
                        <span class="status-badge {{ $sensorData->humidity >= 40 && $sensorData->humidity <= 60 ? 'status-normal' : ($sensorData->humidity > 60 ? 'status-critical' : 'status-warning') }}">
                            @if($sensorData->humidity >= 40 && $sensorData->humidity <= 60)
                                Normal
                            @elseif($sensorData->humidity > 60)
                                Tinggi
                            @else
                                Rendah
                            @endif
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>⚡ Tegangan</td>
                    <td>{{ $sensorData->voltage }}V</td>
                    <td>
                        <span class="status-badge {{ $sensorData->power_status ? 'status-normal' : 'status-critical' }}">
                            {{ $sensorData->power_status ? 'Normal' : 'Tidak Ada Daya' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>🔌 Status Power</td>
                    <td>{{ $sensorData->power_status ? 'ON' : 'OFF' }}</td>
                    <td>
                        <span class="status-badge {{ $sensorData->power_status ? 'status-normal' : 'status-critical' }}">
                            {{ $sensorData->power_status ? 'Aktif' : 'Mati' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>🕐 Waktu Deteksi</td>
                    <td colspan="2">{{ $sensorData->recorded_at->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') }} WIB</td>
                </tr>
            </table>

            <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4>📋 Informasi Penting:</h4>
                <ul>
                    <li><strong>Suhu Normal:</strong> 18°C - 27°C</li>
                    <li><strong>Kelembaban Normal:</strong> 40% - 60%</li>
                    <li><strong>Power Status:</strong> Harus selalu ON untuk operasi normal</li>
                    <li><strong>Tindakan:</strong> Segera periksa perangkat monitoring jika status abnormal</li>
                </ul>
            </div>

            <p style="color: #64748b; font-size: 14px; margin-top: 30px;">
                Email ini dikirim secara otomatis oleh sistem monitoring BMKG. 
                Untuk informasi lebih lanjut, silakan hubungi administrator sistem.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} BMKG Monitoring System. All rights reserved.</p>
            <p>Sistem monitoring cuaca dan iklim otomatis</p>
        </div>
    </div>
</body>
</html>
