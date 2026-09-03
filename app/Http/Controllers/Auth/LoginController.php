<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('Auth.login');
    }

    /**
     * Handle login request (HANYA MENGGUNAKAN USERNAME ATAU EMAIL RESMI YANG TERDAFTAR).
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));

        // Kunci throttle unik berdasarkan input login dan IP address
        $throttleKey = Str::transliterate(Str::lower($loginInput).'|'.$request->ip());

        // Cek jika percobaan login gagal melebihi 5 kali
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('login');
        }

        // Cari HANYA berdasarkan kolom email ATAU kolom username yang terdaftar secara persis
        $user = User::where(function ($query) use ($loginInput) {
            $query->where('email', $loginInput)
                  ->orWhere('email', strtolower($loginInput));

            if (Schema::hasColumn('users', 'username')) {
                $query->orWhere('username', $loginInput)
                      ->orWhere('username', strtolower($loginInput));
            }
        })->first();

        // Verifikasi kata sandi jika akun ditemukan
        if ($user && Hash::check($request->password, $user->password)) {
            
            // Cek apakah akun berstatus aktif
            if (!$user->isActive()) {
                return back()->withErrors([
                    'login' => 'Akun Anda berstatus Tidak Aktif. Silakan hubungi Admin PIKOL untuk mengaktifkan akun.',
                ])->onlyInput('login');
            }

            // Login akun
            Auth::login($user, $request->filled('remember'));

            // Hapus hitungan rate limiter setelah berhasil login
            RateLimiter::clear($throttleKey);

            // Regenerate session untuk keamanan
            $request->session()->regenerate();
            
            // Redirect ke dashboard
            return redirect()->intended(route('dashboard'));
        }

        // Catat percobaan gagal (akan mengunci selama 60 detik jika mencapai 5 percobaan)
        RateLimiter::hit($throttleKey, 60);

        // Jika login gagal
        return back()->withErrors([
            'login' => 'Username/Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}