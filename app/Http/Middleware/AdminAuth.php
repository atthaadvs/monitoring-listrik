<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login sebagai admin
        if (!Auth::guard('admin')->check()) {
            // Redirect ke halaman login admin
            return redirect()->route('admin.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah admin masih aktif
        $admin = Auth::guard('admin')->user();
        if (!$admin->isActive()) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'Akun Anda telah dinonaktifkan.');
        }

        return $next($request);
    }
}
