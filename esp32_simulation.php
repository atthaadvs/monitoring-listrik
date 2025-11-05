<?php
/**
 * Continuous ESP32 Power Status Simulation
 * Mengirim data secara berkesinambungan untuk testing real-time
 */

$baseUrl = 'http://127.0.0.1:8000';

echo "⚡ ESP32 Continuous Power Status Simulation\n";
echo "==========================================\n";
echo "Tekan Ctrl+C untuk berhenti\n\n";

$scenarios = [
    [
        'name' => '🟢 LISTRIK HIDUP - Normal',
        'temperature' => 23.5,
        'humidity' => 55.0,
        'power_status' => true,
        'voltage' => 220.0,
        'power' => 850.0
    ],
    [
        'name' => '🔴 LISTRIK MATI - Maintenance',
        'temperature' => 24.0,
        'humidity' => 52.0,
        'power_status' => false,
        'voltage' => 0,
        'power' => 0
    ],
    [
        'name' => '🟢 LISTRIK HIDUP - High Load',
        'temperature' => 26.2,
        'humidity' => 58.5,
        'power_status' => true,
        'voltage' => 225.5,
        'power' => 1450.8
    ],
    [
        'name' => '🟡 LISTRIK HIDUP - Low Voltage',
        'temperature' => 22.8,
        'humidity' => 48.2,
        'power_status' => true,
        'voltage' => 195.2,
        'power' => 320.5
    ],
    [
        'name' => '🔴 LISTRIK MATI - Emergency',
        'temperature' => 29.1,
        'humidity' => 68.5,
        'power_status' => false,
        'voltage' => 0,
        'power' => 0
    ]
];

$currentScenario = 0;

while (true) {
    $scenario = $scenarios[$currentScenario];
    
    echo sprintf("[%s] %s\n", date('H:i:s'), $scenario['name']);
    
    // Tambahkan variasi random kecil
    $data = [
        'temperature' => $scenario['temperature'] + (rand(-20, 20) / 10),
        'humidity' => $scenario['humidity'] + (rand(-30, 30) / 10),
        'power_status' => $scenario['power_status'],
        'voltage' => $scenario['voltage'] + (rand(-50, 50) / 10),
        'power' => $scenario['power'] + (rand(-100, 100) / 10),
        'location' => 'Server Room BMKG'
    ];
    
    // Pastikan power dan voltage 0 jika status mati
    if (!$data['power_status']) {
        $data['voltage'] = 0;
        $data['power'] = 0;
    }
    
    // Kirim data
    $response = sendSensorData($baseUrl, $data);
    $responseData = json_decode($response, true);
    
    if ($responseData && $responseData['success']) {
        echo sprintf("  📊 T: %.1f°C, H: %.1f%%, V: %.1fV, P: %.1fW - Status: %s\n", 
            $data['temperature'], 
            $data['humidity'], 
            $data['voltage'], 
            $data['power'],
            $data['power_status'] ? 'ON' : 'OFF'
        );
    } else {
        echo "  ❌ Error sending data\n";
    }
    
    // Pindah ke scenario berikutnya
    $currentScenario = ($currentScenario + 1) % count($scenarios);
    
    sleep(10); // 10 detik interval
}

function sendSensorData($baseUrl, $data) {
    $url = $baseUrl . '/api/sensor-data';
    
    $postdata = http_build_query($data);
    
    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postdata
        ]
    ];
    
    $context = stream_context_create($opts);
    $result = @file_get_contents($url, false, $context);
    
    return $result ?: 'Error sending data';
}