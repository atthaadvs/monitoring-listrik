<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring Ruang Server BMKG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'blue': {
                            '50': '#eff6ff',
                            '500': '#3b82f6',
                            '600': '#2563eb',
                            '700': '#1d4ed8',
                            '800': '#1e40af',
                            '900': '#1e3a8a'
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 30%, #EFF6FF 100%);
            min-height: 100vh;
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #0EA5E9 0%, #0284C7 50%, #0369A1 100%);
        }
        
        .card-gradient {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.9) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(14, 165, 233, 0.2);
            box-shadow: 0 8px 32px rgba(14, 165, 233, 0.1);
        }
        
        .card-white {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(14, 165, 233, 0.15);
            box-shadow: 0 4px 20px rgba(14, 165, 233, 0.08);
        }
        
        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(14, 165, 233, 0.15);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .icon-glow {
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.5));
        }
        
        .donut-text-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 1;
        }
    </style>
</head>
<body class="min-h-screen flex">
    <!-- Sidebar -->
    <div class="sidebar-gradient w-20 flex flex-col items-center py-8 shadow-2xl">
        <div class="mb-8">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-server text-white text-xl icon-glow"></i>
            </div>
        </div>
        
        <nav class="flex-1 flex flex-col space-y-6">
            <a href="{{ route('dashboard') }}" class="w-12 h-12 bg-white bg-opacity-30 rounded-xl flex items-center justify-center hover:bg-opacity-40 transition-all duration-300 group">
                <i class="fas fa-chart-bar text-white text-lg group-hover:scale-110 transition-transform"></i>
            </a>
            <a href="{{ route('history') }}" class="w-12 h-12 bg-white bg-opacity-10 rounded-xl flex items-center justify-center hover:bg-opacity-20 transition-all duration-300 group">
                <i class="fas fa-history text-white text-lg group-hover:scale-110 transition-transform"></i>
            </a>
        </nav>
        
        <div class="mt-8">
            <div class="w-12 h-12 bg-white bg-opacity-10 rounded-xl flex items-center justify-center">
                <i class="fas fa-user text-white text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Monitoring</h1>
                    <span class="text-gray-500">|</span>
                    <span class="text-gray-600">Ruang Server BMKG</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Welcome Message -->
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-700">Selamat datang!</p>
                    </div>
                    
                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 p-8 overflow-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard Monitoring Server</h1>
                <p class="text-blue-600 text-lg font-medium">Badan Meteorologi, Klimatologi, dan Geofisika</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 px-4 py-2 bg-white/90 rounded-2xl backdrop-blur-sm border border-blue-200 shadow-sm">
                    <div id="system-status-indicator" class="w-3 h-3 rounded-full bg-green-400 animate-pulse"></div>
                    <span id="system-status-text" class="text-gray-700 font-medium">{{ $systemStatus['message'] ?? 'Loading...' }}</span>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        <div id="alerts-section" class="mb-8 animate-fade-in">
            @if(isset($alerts) && count($alerts) > 0)
                @foreach($alerts as $alert)
                    <div class="alert-{{ $alert['type'] }} p-4 mb-3 rounded-2xl bg-white/95 border-l-4
                        @if($alert['type'] === 'danger') border-red-400 
                        @elseif($alert['type'] === 'warning') border-yellow-400 
                        @else border-blue-400 @endif
                        shadow-lg card-hover">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-full 
                                @if($alert['type'] === 'danger') bg-red-100 
                                @elseif($alert['type'] === 'warning') bg-yellow-100 
                                @else bg-blue-100 @endif">
                                <i class="fas fa-exclamation-triangle 
                                    @if($alert['type'] === 'danger') text-red-600 
                                    @elseif($alert['type'] === 'warning') text-yellow-600 
                                    @else text-blue-600 @endif text-lg"></i>
                            </div>
                            <span class="font-medium text-gray-800 text-lg">{{ $alert['message'] }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-12 gap-6 animate-slide-up">
            <!-- Pemantau Suhu Real-time - Large Card -->
            <div class="col-span-12 lg:col-span-6 card-gradient rounded-3xl p-8 shadow-2xl card-hover">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 bg-red-500 rounded-full animate-pulse"></div>
                        <h2 class="text-xl font-bold text-gray-800">Pemantau Suhu Real-time</h2>
                    </div>
                </div>

                <!-- Kartu Statistik dalam Pemantau Suhu Real-time -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Info Suhu -->
                    <div class="bg-white/90 rounded-2xl p-4 shadow-lg">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="text-3xl">🌡️</div>
                            <div>
                                <h4 class="font-bold text-gray-800">Suhu</h4>
                                <p class="text-gray-600 text-sm">Status saat ini</p>
                            </div>
                        </div>
                        <div class="text-2xl font-bold mb-2" id="temp-status-word-main">
                            @if(isset($latestData))
                                @if($latestData->temperature_status === 'normal') normal
                                @elseif($latestData->temperature_status === 'high') tinggi
                                @else rendah @endif
                            @else
                                --
                            @endif
                        </div>
                        <p class="text-gray-600 text-sm">Rentang: 18-27°C</p>
                    </div>

                    <!-- Kelembaban Saat Ini -->
                    <div class="bg-white/90 rounded-2xl p-4 shadow-lg">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="text-3xl">💧</div>
                            <div>
                                <h4 class="font-bold text-gray-800">Kelembaban Saat Ini</h4>
                                <p class="text-gray-600 text-sm">Kondisi terkini</p>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-blue-600 mb-2" id="humidity-prediction-main">
                            @if(isset($latestData))
                                {{ $latestData->humidity }}%
                            @else
                                --%
                            @endif
                        </div>
                        <p class="text-gray-600 text-sm">Kondisi optimal</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-center">
                    <div class="w-full max-w-4xl">
                        <div class="grid grid-cols-3 gap-8">
                            <div class="flex flex-col items-center justify-center text-center">
                                <p class="text-gray-600 text-sm mb-3 font-medium">Waktu</p>
                                <div id="current-time" class="text-3xl font-bold text-blue-600 mb-2">
                                    @if(isset($latestData))
                                        {{ $latestData->recorded_at->format('H:i:s') }}
                                    @else
                                        --:--:--
                                    @endif
                                </div>
                                <p class="text-gray-500 text-xs mb-3">
                                    @if(isset($latestData))
                                        {{ $latestData->recorded_at->format('l, d M Y') }}
                                    @else
                                        Loading...
                                    @endif
                                </p>
                                <!-- Data Source Indicator -->
                                <div class="flex justify-center">
                                    <p id="data-source-indicator" class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                        Waktu-nyata
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-center justify-center text-center">
                                <p class="text-gray-600 text-sm mb-3 font-medium">Suhu Saat Ini</p>
                                <div id="temperature-value-main" class="text-4xl font-bold text-blue-700 mb-2">
                                    {{ $latestData->temperature ?? '--' }}°
                                </div>
                                <p class="text-gray-500 text-xs font-medium">Celsius</p>
                            </div>
                            
                            <div class="flex flex-col items-center justify-center text-center">
                                <p class="text-gray-600 text-sm mb-3 font-medium">Kelembaban</p>
                                <div id="humidity-value-main" class="text-4xl font-bold text-blue-700 mb-2">
                                    {{ $latestData->humidity ?? '--' }}%
                                </div>
                                <p class="text-gray-500 text-xs font-medium">Relatif</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ml-12 flex-shrink-0 animate-float">
                        <div class="w-36 h-36 bg-gradient-to-br from-blue-50 to-blue-100 rounded-full flex items-center justify-center border-4 border-blue-200 shadow-lg">
                            <i class="fas fa-thermometer-half text-blue-600 text-6xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Comparison Chart -->
            <div class="col-span-12 lg:col-span-6 card-gradient rounded-3xl p-8 shadow-2xl card-hover">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                            <i class="fas fa-bolt text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Status Listrik</h2>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="relative w-24 h-24 mx-auto mb-4">
                        <canvas id="power-donut" width="96" height="96"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i id="power-plug-icon" class="fas fa-plug text-2xl
                                @if(isset($latestData) && $latestData->power_status) text-green-600 @else text-red-600 @endif"></i>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-center space-x-2">
                            <div id="power-status-led" class="w-3 h-3 rounded-full 
                                @if(isset($latestData) && $latestData->power_status) bg-green-400 @else bg-red-400 @endif animate-pulse"></div>
                            <span class="text-gray-700 font-medium">Status Daya</span>
                        </div>
                        
                        <div id="power-status-text" class="text-3xl font-bold 
                            @if(isset($latestData) && $latestData->power_status) text-green-600 @else text-red-600 @endif">
                            @if(isset($latestData) && $latestData->power_status) HIDUP @else MATI @endif
                        </div>

                        <div class="flex space-x-3">
                            <div class="bg-blue-50 rounded-xl p-3 border border-blue-100 flex-1">
                                <p class="text-gray-600 text-sm">Tegangan</p>
                                <p class="text-blue-700 font-bold voltage-value">{{ $latestData->voltage ?? '--' }}V</p>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-3 border border-blue-100 flex-1">
                                <p class="text-gray-600 text-sm">Daya</p>
                                <p class="text-blue-700 font-bold power-value">{{ $latestData->power ?? '--' }}W</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Grafik - Dipindah ke Bawah -->
        <div class="grid grid-cols-12 gap-6 mt-8 animate-slide-up">
            <!-- Grafik Perbandingan Data -->
            <div class="col-span-12 card-gradient rounded-3xl p-8 shadow-2xl card-hover">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Grafik Perbandingan Data</h2>
                    </div>
                    <p class="text-gray-600 text-sm">Lihat & bandingkan data dari database dengan data waktu-nyata</p>
                </div>
                <div style="height: 280px;" class="bg-gray-50 rounded-2xl p-4 border border-blue-100">
                    <canvas id="comparison-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Storage Info Card -->
        <div class="mt-8 animate-fade-in">
            <div class="card-white rounded-3xl p-6 shadow-xl card-hover">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-sky-400 to-sky-600 shadow-lg">
                            <i class="fas fa-history text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Data Tersimpan Terakhir</h3>
                            <p class="text-gray-600" id="last-saved-data">
                                @if(isset($latestData))
                                    {{ $latestData->recorded_at->format('d/m/Y | H:i:s') }}
                                @else
                                    Loading...
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- Real-Time Status Indicator untuk Status Listrik -->
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-700">Status Monitoring</p>
                            <span id="power-monitoring-status" class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                🔌 Real-time Aktif
                            </span>
                        </div>
                        <div id="power-real-time-indicator" class="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let powerDonutChart, comparisonChart;
        
        // Initialize all charts
        function initCharts() {
            // Power Status Donut (Main)
            const powerCtx = document.getElementById('power-donut').getContext('2d');
            const powerStatus = {{ isset($latestData) && $latestData->power_status ? 'true' : 'false' }};
            powerDonutChart = new Chart(powerCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: powerStatus ? [100, 0] : [0, 100],
                        backgroundColor: powerStatus ? ['#0EA5E9', '#E2E8F0'] : ['#EF4444', '#E2E8F0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                }
            });

            // Comparison Chart
            const compCtx = document.getElementById('comparison-chart').getContext('2d');
            comparisonChart = new Chart(compCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Suhu (°C)',
                        data: [],
                        borderColor: '#0EA5E9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#0EA5E9',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: { color: '#374151', font: { size: 12 } }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#6B7280', font: { size: 11 } },
                            grid: { color: '#E5E7EB' }
                        },
                        y: {
                            ticks: {
                                color: '#6B7280',
                                font: { size: 11 },
                                callback: function(value) { return value + '°C'; }
                            },
                            grid: { color: '#E5E7EB' }
                        }
                    }
                }
            });
        }

        // Update power donut
        function updatePowerDonut(powerStatus) {
            powerDonutChart.data.datasets[0].data = powerStatus ? [100, 0] : [0, 100];
            powerDonutChart.data.datasets[0].backgroundColor = powerStatus ? ['#0EA5E9', '#E2E8F0'] : ['#EF4444', '#E2E8F0'];
            powerDonutChart.update('none');
        }

        // Update real-time data
        function updateRealTimeData() {
            fetch('/api/sensor-data')
                .then(response => response.json())
                .then(data => {
                    // Update main display
                    document.getElementById('temperature-value-main').textContent = data.temperature + '°';
                    document.getElementById('humidity-value-main').textContent = data.humidity + '%';
                    document.getElementById('humidity-prediction-main').textContent = data.humidity + '%';
                    
                    // Update voltage, power values dengan efek visual
                    const voltageElements = document.querySelectorAll('.voltage-value');
                    const powerElements = document.querySelectorAll('.power-value');
                    
                    voltageElements.forEach(el => {
                        const newValue = (data.voltage || '--') + 'V';
                        if (el.textContent !== newValue) {
                            el.style.transition = 'all 0.3s ease';
                            el.style.transform = 'scale(1.1)';
                            el.textContent = newValue;
                            setTimeout(() => {
                                el.style.transform = 'scale(1)';
                            }, 300);
                        }
                    });
                    
                    powerElements.forEach(el => {
                        const newValue = (data.power || '--') + 'W';
                        if (el.textContent !== newValue) {
                            el.style.transition = 'all 0.3s ease';
                            el.style.transform = 'scale(1.1)';
                            el.textContent = newValue;
                            setTimeout(() => {
                                el.style.transform = 'scale(1)';
                            }, 300);
                        }
                    });
                    
                    // Update time
                    const now = new Date();
                    document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID');
                    document.getElementById('last-saved-data').textContent = now.toLocaleDateString('id-ID') + ' | ' + now.toLocaleTimeString('id-ID');
                    
                    // ⚡ UPDATE STATUS LISTRIK REAL-TIME
                    updatePowerDonut(data.power_status);
                    
                    // Update status listrik LED indicator
                    const powerStatusLED = document.getElementById('power-status-led');
                    if (powerStatusLED) {
                        powerStatusLED.className = `w-3 h-3 rounded-full animate-pulse ${data.power_status ? 'bg-green-400' : 'bg-red-400'}`;
                    }
                    
                    // Update status listrik text (HIDUP/MATI)
                    const powerStatusText = document.getElementById('power-status-text');
                    if (powerStatusText) {
                        powerStatusText.textContent = data.power_status ? 'HIDUP' : 'MATI';
                        powerStatusText.className = `text-3xl font-bold ${data.power_status ? 'text-green-600' : 'text-red-600'}`;
                    }
                    
                    // Update power plug icon
                    const powerPlugIcon = document.getElementById('power-plug-icon');
                    if (powerPlugIcon) {
                        powerPlugIcon.className = `fas fa-plug text-2xl ${data.power_status ? 'text-green-600' : 'text-red-600'}`;
                    }
                    
                    // Update temperature status word
                    const tempStatusWord = document.getElementById('temp-status-word-main');
                    if (data.temperature_status === 'normal') {
                        tempStatusWord.textContent = 'normal';
                        tempStatusWord.className = 'text-2xl font-bold text-blue-600 mb-2';
                    } else if (data.temperature_status === 'high') {
                        tempStatusWord.textContent = 'tinggi';
                        tempStatusWord.className = 'text-2xl font-bold text-red-500 mb-2';
                    } else {
                        tempStatusWord.textContent = 'rendah';
                        tempStatusWord.className = 'text-2xl font-bold text-yellow-500 mb-2';
                    }
                    
                    // Update system status indicator
                    const statusIndicator = document.getElementById('system-status-indicator');
                    statusIndicator.className = 'w-3 h-3 rounded-full animate-pulse ' + 
                        (data.status_color === 'green' ? 'bg-green-400' : 
                         data.status_color === 'red' ? 'bg-red-400' : 'bg-yellow-400');
                    
                    // Update data source indicator
                    const sourceIndicator = document.getElementById('data-source-indicator');
                    if (data.source === 'real-time-cache') {
                        sourceIndicator.textContent = 'Waktu-nyata (Cache)';
                        sourceIndicator.className = 'text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium';
                    } else if (data.source === 'database-fallback') {
                        sourceIndicator.textContent = 'Database (Cadangan)';
                        sourceIndicator.className = 'text-xs px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium';
                    } else {
                        sourceIndicator.textContent = 'Tidak Ada Data';
                        sourceIndicator.className = 'text-xs px-3 py-1 rounded-full bg-red-100 text-red-700 font-medium';
                    }
                    
                    // Update status monitoring listrik
                    const powerMonitoringStatus = document.getElementById('power-monitoring-status');
                    const powerRealTimeIndicator = document.getElementById('power-real-time-indicator');
                    
                    if (data.source === 'real-time-cache') {
                        powerMonitoringStatus.textContent = '⚡ Monitoring Real-time';
                        powerMonitoringStatus.className = 'text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium';
                        powerRealTimeIndicator.className = 'w-4 h-4 bg-green-400 rounded-full animate-pulse';
                    } else if (data.source === 'database-fallback') {
                        powerMonitoringStatus.textContent = '🔌 Mode Cadangan';
                        powerMonitoringStatus.className = 'text-xs px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium';
                        powerRealTimeIndicator.className = 'w-4 h-4 bg-yellow-400 rounded-full animate-pulse';
                    } else {
                        powerMonitoringStatus.textContent = '⚠️ Offline';
                        powerMonitoringStatus.className = 'text-xs px-3 py-1 rounded-full bg-red-100 text-red-700 font-medium';
                        powerRealTimeIndicator.className = 'w-4 h-4 bg-red-400 rounded-full animate-pulse';
                    }
                })
                .catch(error => console.error('Error fetching real-time data:', error));
        }

        // Update chart data for comparison chart
        function updateChartData() {
            fetch('/api/chart-data')
                .then(response => response.json())
                .then(data => {
                    comparisonChart.data.labels = data.labels.slice(-12); // Last 12 hours
                    comparisonChart.data.datasets[0].data = data.temperature.slice(-12);
                    comparisonChart.update('none');
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            
            @if(isset($latestData))
                updatePowerDonut({{ $latestData->power_status ? 'true' : 'false' }});
            @endif
            
            // Load initial chart data
            updateChartData();
            
            // Update real-time data every 5 seconds
            setInterval(updateRealTimeData, 5000);
            
            // Update chart data every 30 seconds
            setInterval(updateChartData, 30000);
            
            // Update time every second
            setInterval(function() {
                const now = new Date();
                document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID');
            }, 1000);
        });

    </script>
    </div> <!-- End Content Area -->
    </div> <!-- End Main Content -->
</body>
</html>
