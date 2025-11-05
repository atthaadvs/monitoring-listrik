<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Data Monitoring - BMKG</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #E0F2FE 0%, #BAE6FD 50%, #7DD3FC 100%);
            min-height: 100vh;
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #0EA5E9 0%, #0284C7 50%, #0369A1 100%);
        }
        
        .card-gradient {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex">
    <!-- Sidebar -->
    <div class="sidebar-gradient w-20 flex flex-col items-center py-8 shadow-2xl">
        <div class="mb-8">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-server text-white text-xl"></i>
            </div>
        </div>
        
        <nav class="flex-1 flex flex-col space-y-6">
            <a href="/" class="w-12 h-12 bg-white bg-opacity-10 rounded-xl flex items-center justify-center hover:bg-opacity-20 transition-all duration-300 group">
                <i class="fas fa-chart-bar text-white text-lg group-hover:scale-110 transition-transform"></i>
            </a>
            <a href="/history" class="w-12 h-12 bg-white bg-opacity-30 rounded-xl flex items-center justify-center hover:bg-opacity-40 transition-all duration-300 group">
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
    <div class="flex-1 p-8 overflow-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <div class="flex items-center space-x-4 mb-4">
                    <div class="p-3 rounded-2xl bg-white bg-opacity-20 backdrop-blur-sm">
                        <i class="fas fa-history text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">Riwayat Data Tersimpan</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="card-gradient rounded-3xl p-6 shadow-2xl mb-6">
            <form method="GET" action="{{ route('history') }}" class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari data..." 
                               class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl transition-colors">
                        <i class="fas fa-search mr-1"></i>
                        Cari
                    </button>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-600 text-sm">Status:</span>
                        <select name="status" class="px-3 py-1 bg-gray-50 border border-gray-200 rounded-lg text-sm" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Normal" {{ request('status') === 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Warning" {{ request('status') === 'Warning' ? 'selected' : '' }}>Warning</option>
                            <option value="Critical" {{ request('status') === 'Critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="card-gradient rounded-3xl shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Hari</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Total Record</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Avg Suhu (°C)</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Avg Kelembaban (%)</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body" class="divide-y divide-gray-100">
                        @forelse($historyData as $data)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $data->date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $data->day }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $data->total_records }} data</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ number_format($data->avg_temperature, 1) }}°C</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ number_format($data->avg_humidity, 1) }}%</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                    @if($data->status === 'Normal') text-green-600 bg-green-100
                                    @elseif($data->status === 'Warning') text-yellow-600 bg-yellow-100
                                    @else text-red-600 bg-red-100 @endif">
                                    {{ $data->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('history.download', $data->date_formatted) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-sky-500 hover:bg-sky-600 text-white text-xs rounded-lg transition-colors">
                                    <i class="fas fa-download mr-1"></i>
                                    PDF
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <i class="fas fa-database text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg">Tidak ada data tersimpan</p>
                                <p class="text-sm">Belum ada data monitoring yang terekam</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Menampilkan {{ $historyData->firstItem() ?? 0 }} sampai {{ $historyData->lastItem() ?? 0 }} dari {{ $historyData->total() }} data
                </div>
                <div class="flex items-center space-x-2">
                    {{ $historyData->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto submit form when search input changes (with debounce)
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });
        }
    </script>
</body>
</html>
