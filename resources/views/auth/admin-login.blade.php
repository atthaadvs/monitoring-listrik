<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Monitoring Listrik BMKG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }
        
        .bmkg-logo {
            width: 150px;
            height: 150px;
            margin: 0 auto 2rem;
            display: block;
            object-fit: contain;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            color: #6b7280;
            transition: color 0.3s ease;
            cursor: pointer;
        }
        
        .password-toggle:hover {
            color: #374151;
        }
        
        .password-toggle i {
            font-size: 16px;
            line-height: 1;
        }
        
        .login-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.05);
            padding: 3rem;
            max-width: 420px;
            width: 100%;
        }
        
        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        
        .btn-primary {
            width: 100%;
            background: #3b82f6;
            color: white;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
        }
        
        .back-btn {
            position: absolute;
            top: 2rem;
            left: 2rem;
            width: 48px;
            height: 48px;
            background: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 14px;
        }
        
        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gray-50">
    <!-- Back Button
    <button onclick="history.back()" class="back-btn">
        <i class="fas fa-arrow-left text-gray-600 text-lg"></i>
    </button> -->

    <div class="login-container">
        <!-- BMKG Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo-bmkg.png') }}" alt="BMKG Logo" class="bmkg-logo">
            <h1 class="text-2xl font-bold text-gray-900 mt-6 mb-2">Admin NOC</h1>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-error">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Success Messages -->
        @if (session('success'))
            <div class="alert alert-success">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if (session('error'))
            <div class="alert alert-error">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-times-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
            @csrf
            
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                    Login Name
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username') }}"
                    required 
                    autofocus
                    class="form-input @error('username') border-red-300 @enderror"
                    placeholder="username@domain"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    Password
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="form-input pr-12 @error('password') border-red-300 @enderror"
                        placeholder="Enter your password"
                    >
                    <button 
                        type="button" 
                        onclick="togglePassword()"
                        class="password-toggle"
                    >
                        <i id="password-toggle-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <label for="remember" class="ml-2 text-sm text-gray-600">
                    Remember me
                </label>
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn-primary">
                Next
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center mt-8 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-500">
                © 2025 BMKG - Badan Meteorologi, Klimatologi, dan Geofisika
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Auto focus pada username field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>