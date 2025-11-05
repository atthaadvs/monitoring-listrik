# 📧 SISTEM NOTIFIKASI EMAIL BMKG MONITORING

## 🎯 Fitur Notifikasi
1. **Real-time Alerts** - Notifikasi otomatis saat kondisi abnormal
2. **Monthly Reports** - Laporan PDF bulanan otomatis

---

## ⚙️ SETUP EMAIL CONFIGURATION

### 1. Update file `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS="bmkg-monitoring@gmail.com"
MAIL_FROM_NAME="BMKG Monitoring System"
MAIL_ENCRYPTION=tls
```

### 2. Update email admin di `app/Services/NotificationService.php`:
```php
protected $adminEmails = [
    'admin@bmkg.go.id',
    'monitoring@bmkg.go.id',
    'your-email@gmail.com'  // Tambahkan email Anda
];
```

---

## 🚨 REAL-TIME NOTIFICATIONS

### Kondisi Alert:
- **Suhu < 18°C atau > 27°C** → Email alert temperatur
- **Kelembaban < 40% atau > 60%** → Email alert kelembaban  
- **Power Status = OFF** → Email alert power system

### Cara Kerja:
1. ESP32 kirim data ke `/api/sensor-data`
2. System otomatis cek kondisi abnormal
3. Jika abnormal → kirim email alert ke admin
4. Email berisi data lengkap + status + rekomendasi

---

## 📊 MONTHLY REPORTS

### Generate Manual:
```bash
# Generate untuk bulan lalu
php artisan report:monthly

# Generate untuk bulan/tahun tertentu
php artisan report:monthly --month=10 --year=2025
```

### Schedule Otomatis (Tambahkan ke `app/Console/Kernel.php`):
```php
protected function schedule(Schedule $schedule)
{
    // Generate laporan bulanan setiap tanggal 1 jam 00:00
    $schedule->command('report:monthly')
             ->monthlyOn(1, '00:00');
}
```

### Isi Laporan PDF:
- ✅ Summary statistik bulanan
- ✅ Data harian detail
- ✅ Kondisi ekstrem (max/min)
- ✅ Status operasional
- ✅ Grafik dan analisis

---

## 🧪 TESTING

### Test Alert Email:
```
GET /test-email
```

### Test Monthly Report:
```
GET /test-monthly-report
```

### Test via Terminal:
```bash
# Test manual report generation
php artisan report:monthly --month=10 --year=2025
```

---

## 📁 FILE STRUCTURE

```
app/
├── Mail/
│   ├── SensorAlert.php          # Email alert sensor
│   └── MonthlyReport.php        # Email laporan bulanan
├── Services/
│   └── NotificationService.php  # Logic notifikasi
└── Console/Commands/
    └── GenerateMonthlyReport.php # Command generate laporan

resources/views/
├── emails/
│   ├── sensor-alert.blade.php   # Template email alert
│   └── monthly-report.blade.php # Template email laporan
└── reports/
    └── monthly-report.blade.php # Template PDF bulanan

storage/app/reports/             # Generated PDF files
```

---

## 🔧 CUSTOMIZATION

### Ubah Batas Alert:
Edit di `app/Services/NotificationService.php`:
```php
// Temperature: 18-27°C
// Humidity: 40-60%
```

### Tambah Email Admin:
```php
protected $adminEmails = [
    'admin1@bmkg.go.id',
    'admin2@bmkg.go.id',
    'your-email@domain.com'
];
```

### Ubah Schedule:
Edit di `app/Console/Kernel.php` untuk mengatur kapan laporan dikirim.

---

## 📧 EMAIL TEMPLATES

### Alert Email Features:
- 🌡️ Status suhu dengan color coding
- 💧 Status kelembaban dengan badge
- ⚡ Status power system
- 📊 Data lengkap sensor
- 📋 Informasi teknis & rekomendasi

### Monthly Report Features:
- 📈 Summary statistik bulan
- 🌡️ Kondisi ekstrem (max/min)
- 📊 Status harian (Normal/Warning/Critical)
- ⚡ System uptime percentage
- 📎 PDF attachment lengkap

---

## ✅ CHECKLIST AKTIVASI

- [ ] Update `.env` dengan konfigurasi email SMTP
- [ ] Update email admin di `NotificationService.php`
- [ ] Test email dengan `/test-email`
- [ ] Test monthly report dengan `/test-monthly-report`
- [ ] Setup cron job untuk laporan otomatis
- [ ] Verifikasi ESP32 kirim data abnormal untuk test alert

---

## 🆘 TROUBLESHOOTING

### Email tidak terkirim:
1. Cek konfigurasi SMTP di `.env`
2. Pastikan Gmail App Password benar
3. Cek log Laravel: `storage/logs/laravel.log`

### PDF tidak generate:
1. Pastikan folder `storage/app/reports/` exists dan writable
2. Cek apakah ada data di database
3. Cek error di log file

### Alert tidak muncul:
1. Pastikan ESP32 kirim data ke `/api/sensor-data`
2. Cek nilai suhu/kelembaban sudah abnormal
3. Verifikasi email admin di `NotificationService.php`

---

Sistem siap digunakan! 🚀📧
