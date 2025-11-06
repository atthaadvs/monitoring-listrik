<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Monitoring Listrik BMKG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            <a href="{{ route('dashboard') }}" class="w-12 h-12 bg-white bg-opacity-10 rounded-xl flex items-center justify-center hover:bg-opacity-20 transition-all duration-300 group">
                <i class="fas fa-chart-bar text-white text-lg group-hover:scale-110 transition-transform"></i>
            </a>
            <a href="{{ route('history') }}" class="w-12 h-12 bg-white bg-opacity-10 rounded-xl flex items-center justify-center hover:bg-opacity-20 transition-all duration-300 group">
                <i class="fas fa-history text-white text-lg group-hover:scale-110 transition-transform"></i>
            </a>
            <a href="{{ route('admin.profile') }}" class="w-12 h-12 bg-white bg-opacity-30 rounded-xl flex items-center justify-center hover:bg-opacity-40 transition-all duration-300 group">
                <i class="fas fa-user-cog text-white text-lg group-hover:scale-110 transition-transform"></i>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800">Profil Admin</h1>
                    <span class="text-gray-500">|</span>
                    <span class="text-gray-600">Pengaturan Akun</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 p-8 overflow-auto">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl p-8 shadow-2xl mb-8">
        <div class="flex items-center space-x-6">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-user-shield text-3xl text-blue-600"></i>
            </div>
            <div class="text-white">
                <h1 class="text-3xl font-bold">Profil Admin</h1>
                <p class="text-blue-100">Kelola informasi akun Anda</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Admin Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-xl p-6">
                <div class="text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-user text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $admin->name }}</h3>
                    <p class="text-gray-600">@{{ $admin->username }}</p>
                    <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                        <i class="fas fa-check-circle mr-1"></i>{{ ucfirst($admin->role) }}
                    </span>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Status</span>
                        <span class="text-green-600 font-semibold">
                            @if($admin->is_active)
                                <i class="fas fa-circle text-green-400 mr-1"></i>Aktif
                            @else
                                <i class="fas fa-circle text-red-400 mr-1"></i>Nonaktif
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-gray-600">Login Terakhir</span>
                        <span class="text-gray-800 font-medium text-sm">
                            @if($admin->last_login_at)
                                {{ $admin->last_login_at->format('d/m/Y H:i') }}
                            @else
                                Belum pernah
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Edit Profil
                </h2>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-green-700">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-exclamation-triangle text-red-500"></i>
                            <span class="text-red-700 font-medium">Terjadi kesalahan:</span>
                        </div>
                        <ul class="ml-6 text-red-600 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-blue-600"></i>Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $admin->name) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('name') border-red-300 @enderror"
                        >
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-blue-600"></i>Email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $admin->email) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('email') border-red-300 @enderror"
                        >
                    </div>

                    <!-- Password Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-key text-blue-600 mr-2"></i>
                            Ubah Password (Opsional)
                        </h3>

                        <!-- Current Password -->
                        <div class="mb-4">
                            <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password Lama
                            </label>
                            <input 
                                type="password" 
                                id="current_password" 
                                name="current_password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('current_password') border-red-300 @enderror"
                                placeholder="Masukkan password lama"
                            >
                        </div>

                        <!-- New Password -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Password Baru
                                </label>
                                <input 
                                    type="password" 
                                    id="new_password" 
                                    name="new_password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('new_password') border-red-300 @enderror"
                                    placeholder="Password baru (min. 8 karakter)"
                                >
                            </div>
                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Konfirmasi Password
                                </label>
                                <input 
                                    type="password" 
                                    id="new_password_confirmation" 
                                    name="new_password_confirmation"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Ulangi password baru"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6">
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Dashboard
                        </a>
                        <button 
                            type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg"
                        >
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>