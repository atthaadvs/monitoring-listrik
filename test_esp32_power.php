<?php
/**
 * Test ESP32 Power Status Real-Time
 * Simulasi pengiriman data sensor dari ESP32 dengan fokus pada status listrik
 */

$baseUrl = 'http://127.0.0.1:8000';

echo "🔌 Testing ESP32 Power Status Real-Time\n";
echo "=====================================\n\n";

// Test 1: Status Listrik HIDUP dengan data normal
echo "Test 1: Status Listrik HIDUP (ON)\n";
$data1 = [
    'temperature' => 23.5,
    'humidity' => 55.2,
    'power_status' => true,
    'voltage' => 220.5,
    'power' => 850.3,
    'location' => 'Server Room BMKG'
];

$response1 = sendSensorData($baseUrl, $data1);
echo "Response: " . $response1 . "\n\n";
sleep(2);

// Test 2: Status Listrik MATI dengan voltage 0
echo "Test 2: Status Listrik MATI (OFF)\n";
$data2 = [
    'temperature' => 24.1,
    'humidity' => 52.8,
    'power_status' => false,
    'voltage' => 0,
    'power' => 0,
    'location' => 'Server Room BMKG'
];

$response2 = sendSensorData($baseUrl, $data2);
echo "Response: " . $response2 . "\n\n";
sleep(2);

// Test 3: Status Listrik HIDUP dengan voltage tinggi
echo "Test 3: Status Listrik HIDUP - Voltage Tinggi\n";
$data3 = [
    'temperature' => 25.2,
    'humidity' => 58.1,
    'power_status' => true,
    'voltage' => 245.8,
    'power' => 1200.5,
    'location' => 'Server Room BMKG'
];

$response3 = sendSensorData($baseUrl, $data3);
echo "Response: " . $response3 . "\n\n";
sleep(2);

// Test 4: Status Listrik MATI dengan suhu tinggi (emergency)
echo "Test 4: Status Listrik MATI - Emergency Mode\n";
$data4 = [
    'temperature' => 29.5,
    'humidity' => 65.3,
    'power_status' => false,
    'voltage' => 0,
    'power' => 0,
    'location' => 'Server Room BMKG'
];

$response4 = sendSensorData($baseUrl, $data4);
echo "Response: " . $response4 . "\n\n";

echo "🎯 Testing completed!\n";
echo "Buka dashboard di: http://127.0.0.1:8000\n";
echo "Status listrik akan berubah secara real-time setiap 5 detik.\n";

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
    $result = file_get_contents($url, false, $context);
    
    return $result ?: 'Error sending data';
}