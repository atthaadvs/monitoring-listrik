# Dashboard Monitoring Ruang Server BMKG

Sistem monitoring kondisi listrik, suhu, dan kelembaban ruangan server berbasis IoT untuk BMKG menggunakan Laravel.

## Fitur Dashboard

- ✅ **Data Kondisi Listrik** - Status ON/OFF dengan monitoring voltage
- ✅ **Data Suhu Real-time** - Dari sensor DHT22 dengan status normal/tinggi/rendah
- ✅ **Data Kelembaban** - Dengan grafik donut dan indikator warna (hijau: 40-60%, kuning: <40%, merah: >60%)
- ✅ **Grafik Historis** - Line chart perubahan suhu dan kelembaban 24 jam terakhir
- ✅ **Notifikasi Alert** - Peringatan jika suhu/kelembaban melebihi batas aman
- ✅ **Status Sistem** - Indikator warna (hijau: normal, kuning: warning, merah: critical)
- ✅ **Update Real-time** - Data refresh otomatis setiap 5 detik

## Setup Database

1. Jalankan migration:
```bash
php artisan migrate
```

2. Jalankan seeder untuk data dummy:
```bash
php artisan db:seed --class=SensorDataSeeder
```

## Menjalankan Dashboard

1. Start Laravel development server:
```bash
php artisan serve
```

2. Akses dashboard di: `http://localhost:8000/dashboard`

## API Endpoints untuk ESP32

### POST /api/sensor/data
Untuk mengirim data sensor dari ESP32:
```json
{
    "temperature": 25.5,
    "humidity": 55.2,
    "power_status": true,
    "voltage": 230.5,
    "location": "Server Room BMKG"
}
```

### GET /api/sensor/latest
Untuk mendapatkan data sensor terbaru

### GET /api/sensor/config
Untuk mendapatkan konfigurasi threshold dari server

## Kode ESP32 (Arduino IDE)

Simpan kode berikut di Arduino IDE:

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <DHT.h>

// WiFi credentials
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Server URL
const char* serverURL = "http://YOUR_LARAVEL_SERVER_IP:8000/api/sensor/data";

// DHT22 sensor
#define DHT_PIN 2
#define DHT_TYPE DHT22
DHT dht(DHT_PIN, DHT_TYPE);

// Power monitoring pins
#define POWER_PIN 4  // Digital pin to check power status
#define VOLTAGE_PIN A0  // Analog pin for voltage reading

void setup() {
  Serial.begin(115200);
  
  // Initialize DHT sensor
  dht.begin();
  
  // Initialize pins
  pinMode(POWER_PIN, INPUT);
  
  // Connect to WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println();
  Serial.println("WiFi connected!");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    // Read DHT22 sensor
    float temperature = dht.readTemperature();
    float humidity = dht.readHumidity();
    
    // Check if readings are valid
    if (isnan(temperature) || isnan(humidity)) {
      Serial.println("Failed to read from DHT sensor!");
      delay(30000); // Wait 30 seconds before retry
      return;
    }
    
    // Read power status
    bool powerStatus = digitalRead(POWER_PIN);
    
    // Read voltage (adjust according to your voltage divider circuit)
    int analogValue = analogRead(VOLTAGE_PIN);
    float voltage = (analogValue * 3.3 / 4095.0) * 100; // Adjust multiplier based on your circuit
    
    // Send data to server
    sendSensorData(temperature, humidity, powerStatus, voltage);
    
    // Print readings to Serial
    Serial.println("--- Sensor Readings ---");
    Serial.print("Temperature: ");
    Serial.print(temperature);
    Serial.println("°C");
    Serial.print("Humidity: ");
    Serial.print(humidity);
    Serial.println("%");
    Serial.print("Power Status: ");
    Serial.println(powerStatus ? "ON" : "OFF");
    Serial.print("Voltage: ");
    Serial.print(voltage);
    Serial.println("V");
    Serial.println();
    
  } else {
    Serial.println("WiFi disconnected, attempting to reconnect...");
    WiFi.begin(ssid, password);
  }
  
  // Wait 30 seconds before next reading
  delay(30000);
}

void sendSensorData(float temperature, float humidity, bool powerStatus, float voltage) {
  HTTPClient http;
  http.begin(serverURL);
  http.addHeader("Content-Type", "application/json");
  
  // Create JSON payload
  StaticJsonDocument<200> doc;
  doc["temperature"] = temperature;
  doc["humidity"] = humidity;
  doc["power_status"] = powerStatus;
  doc["voltage"] = voltage;
  doc["location"] = "Server Room BMKG";
  
  String jsonString;
  serializeJson(doc, jsonString);
  
  // Send POST request
  int httpResponseCode = http.POST(jsonString);
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println("Data sent successfully!");
    Serial.print("Response: ");
    Serial.println(response);
  } else {
    Serial.print("Error sending data. HTTP response code: ");
    Serial.println(httpResponseCode);
  }
  
  http.end();
}
```

## Library ESP32 yang Diperlukan

Install library berikut di Arduino IDE:
1. **WiFi** (built-in)
2. **HTTPClient** (built-in) 
3. **ArduinoJson** by Benoit Blanchon
4. **DHT sensor library** by Adafruit

## Wiring ESP32

```
DHT22:
- VCC -> 3.3V
- GND -> GND  
- Data -> GPIO 2

Power Monitoring:
- Power Status -> GPIO 4 (connect to relay or optocoupler)
- Voltage Divider -> A0 (untuk monitoring voltage dengan voltage divider circuit)
```

## Threshold Monitoring

- **Suhu Ideal**: 18-27°C
- **Kelembaban Ideal**: 40-60%
- **Status Warna**:
  - 🟢 Hijau: Normal (suhu 18-27°C, kelembaban 40-60%)
  - 🟡 Kuning: Warning (suhu <18°C, kelembaban <40%)
  - 🔴 Merah: Critical (suhu >27°C, kelembaban >60%, atau listrik OFF)

## Screenshot Dashboard Features

Dashboard menampilkan:
1. **Real-time Cards** - Suhu, kelembaban dengan donut chart, status listrik, waktu update terakhir
2. **Alert System** - Notifikasi otomatis jika ada parameter yang melebihi batas
3. **Historical Charts** - Line chart untuk tracking trend 24 jam terakhir
4. **Combined Chart** - Grafik kombinasi suhu dan kelembaban dengan dual Y-axis
5. **System Status** - Indicator status keseluruhan sistem di header

Dashboard akan auto-refresh setiap 5 detik untuk data real-time dan setiap 30 detik untuk chart data.

## Production Deployment

Untuk production:
1. Gunakan database MySQL/PostgreSQL 
2. Setup Redis untuk caching
3. Configure queue untuk background jobs
4. Setup SSL certificate
5. Use environment variables untuk konfigurasi
