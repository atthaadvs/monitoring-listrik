<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        // Redirect jika sudah login
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.admin-login');
    }

    /**
     * Handle admin login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari admin berdasarkan username
        $admin = AdminUser::where('username', $request->username)->first();

        // Cek apakah admin ada, aktif, dan password benar
        if (!$admin || !$admin->isActive() || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        // Login admin
        Auth::guard('admin')->login($admin, $request->boolean('remember'));

        // Update last login
        $admin->updateLastLogin();

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $admin->name . '!');
    }

    /**
     * Handle admin logout request.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Show admin profile.
     */
    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('auth.admin-profile', compact('admin'));
    }

    /**
     * Update admin profile.
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin_users,email,' . $admin->id,
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update basic info
        $admin->name = $request->name;
        $admin->email = $request->email;

        // Update password if provided
        if ($request->filled('new_password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $admin->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Password lama tidak sesuai.'],
                ]);
            }

            $admin->password = $request->new_password;
        }

        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
