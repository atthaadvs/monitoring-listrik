<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan BMKG</title>
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
        .summary-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 5px solid #0EA5E9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .stat-item {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0284C7;
        }
        .stat-label {
            color: #64748b;
            font-size: 14px;
            margin-top: 5px;
        }
        .footer {
            background: #64748b;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .attachment-info {
            background: #dbeafe;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #93c5fd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 LAPORAN BULANAN</h1>
            <p>Badan Meteorologi, Klimatologi, dan Geofisika</p>
            <h3>{{ \Carbon\Carbon::create($year, $month, 1)->locale('id')->translatedFormat('F Y') }}</h3>
        </div>

        <div class="content">
            <div class="summary-box">
                <h3>📈 Ringkasan Bulan Ini</h3>
                <p>Berikut adalah ringkasan data monitoring untuk periode {{ \Carbon\Carbon::create($year, $month, 1)->locale('id')->translatedFormat('F Y') }}:</p>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">{{ $summary['total_days'] ?? 0 }}</div>
                    <div class="stat-label">Hari Pemantauan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $summary['total_records'] ?? 0 }}</div>
                    <div class="stat-label">Total Data</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ number_format($summary['avg_temperature'] ?? 0, 1) }}°C</div>
                    <div class="stat-label">Rata-rata Suhu</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ number_format($summary['avg_humidity'] ?? 0, 1) }}%</div>
                    <div class="stat-label">Rata-rata Kelembaban</div>
                </div>
            </div>

            <div class="summary-box">
                <h4>🌡️ Kondisi Ekstrem:</h4>
                <ul>
                    <li><strong>Suhu Tertinggi:</strong> {{ number_format($summary['max_temperature'] ?? 0, 1) }}°C</li>
                    <li><strong>Suhu Terendah:</strong> {{ number_format($summary['min_temperature'] ?? 0, 1) }}°C</li>
                    <li><strong>Kelembaban Tertinggi:</strong> {{ number_format($summary['max_humidity'] ?? 0, 1) }}%</li>
                    <li><strong>Kelembaban Terendah:</strong> {{ number_format($summary['min_humidity'] ?? 0, 1) }}%</li>
                </ul>
            </div>

            <div class="summary-box">
                <h4>📊 Status Operasional:</h4>
                <ul>
                    <li><strong>Uptime System:</strong> {{ number_format($summary['system_uptime'] ?? 0, 1) }}%</li>
                    <li><strong>Hari Normal:</strong> {{ $summary['normal_days'] ?? 0 }} hari</li>
                    <li><strong>Hari Warning:</strong> {{ $summary['warning_days'] ?? 0 }} hari</li>
                    <li><strong>Hari Critical:</strong> {{ $summary['critical_days'] ?? 0 }} hari</li>
                </ul>
            </div>

            <div class="attachment-info">
                <h4>📎 Lampiran Laporan</h4>
                <p>
                    <strong>📄 Laporan_BMKG_{{ $year }}_{{ $month }}.pdf</strong><br>
                    Laporan lengkap berisi data detail, grafik, dan analisis mendalam untuk periode ini.
                </p>
                <p style="font-size: 14px; color: #64748b;">
                    File PDF terlampir berisi data harian, tren, dan rekomendasi berdasarkan analisis data monitoring.
                </p>
            </div>

            <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4>🔍 Catatan Penting:</h4>
                <ul>
                    <li>Data dikumpulkan dari sensor monitoring BMKG secara real-time</li>
                    <li>Laporan ini dibuat otomatis setiap akhir bulan</li>
                    <li>Untuk pertanyaan teknis, hubungi tim administrator sistem</li>
                    <li>Data backup tersimpan aman di sistem database</li>
                </ul>
            </div>

            <p style="color: #64748b; font-size: 14px; margin-top: 30px;">
                Laporan ini digenerate otomatis pada {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d F Y, H:i') }} WIB
                oleh BMKG Monitoring System.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} BMKG Monitoring System. All rights reserved.</p>
            <p>Sistem monitoring cuaca dan iklim otomatis</p>
        </div>
    </div>
</body>
</html>
