<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Tampilkan formulir permintaan OTP Lupa Password
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Kirim Kode OTP 6-Digit ke Telegram anggota
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ], [
            'login.required' => 'Username atau Alamat Email wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));

        // Throttle untuk mencegah spam (maksimal 3x kirim dalam 5 menit)
        $throttleKey = 'send-otp:' . Str::lower($loginInput) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login' => "Terlalu banyak permintaan OTP. Silakan tunggu {$seconds} detik sebelum mencoba lagi.",
            ])->withInput();
        }

        // Cari user berdasarkan username ATAU email
        $user = User::where('email', $loginInput)
            ->orWhere('email', strtolower($loginInput))
            ->orWhere('username', $loginInput)
            ->orWhere('username', strtolower($loginInput))
            ->first();

        if (!$user) {
            return back()->withErrors([
                'login' => 'Akun dengan Username atau Email tersebut tidak ditemukan di sistem.',
            ])->withInput();
        }

        // Cek apakah akun memiliki Telegram ID
        if (empty($user->telegram_id) || trim($user->telegram_id) === '-') {
            return back()->withErrors([
                'login' => "Akun Anda ({$user->name}) belum memiliki Telegram ID terdaftar. Silakan hubungi Admin PIKOL untuk mendaftarkan Telegram ID akun Anda.",
            ])->withInput();
        }

        // Cek status akun
        if (!$user->isActive()) {
            return back()->withErrors([
                'login' => 'Akun Anda berstatus Tidak Aktif. Silakan hubungi Admin PIKOL.',
            ])->withInput();
        }

        // Generate kode OTP 6 Digit acak
        $otp = (string) random_int(100000, 999999);

        // Simpan OTP di Cache selama 10 menit (600 detik)
        Cache::put('pwd_reset_otp_' . $user->id, $otp, 600);

        // Simpan sesi user untuk pre-fill
        session([
            'pwd_reset_user_id' => $user->id,
            'pwd_reset_login'   => $loginInput,
        ]);

        // Kirim Pesan OTP via Telegram Bot
        $message  = "🔐 <b>KODE OTP RESET PASSWORD</b>\n\n";
        $message .= "Halo <b>" . htmlspecialchars($user->name) . "</b>,\n\n";
        $message .= "Anda menerima pesan ini karena ada permintaan untuk mereset kata sandi akun <b>Billing Telkom Cirebon</b>.\n\n";
        $message .= "Kode Verifikasi OTP Anda:\n";
        $message .= "👉 <code>{$otp}</code> 👈\n\n";
        $message .= "⏰ <i>Kode ini berlaku selama <b>10 menit</b>.</i>\n";
        $message .= "⚠️ <i>Jangan berikan kode ini kepada siapapun demi keamanan akun Anda.</i>";

        $sendResult = $this->telegramService->sendMessage($user->telegram_id, $message);

        if (!$sendResult['success']) {
            return back()->withErrors([
                'login' => 'Gagal mengirim pesan ke Telegram Anda. Pastikan Anda sudah memulai chat (/start) dengan Bot Telegram Billing Telkom.',
            ])->withInput();
        }

        // Catat percobaan throttle
        RateLimiter::hit($throttleKey, 300);

        return redirect()->route('password.reset')->with('status', 'Kode OTP 6-digit telah berhasil dikirim ke akun Telegram Anda. Silakan cek pesan dari Bot Telegram!');
    }

    /**
     * Tampilkan formulir verifikasi OTP & buat password baru
     */
    public function showResetForm(Request $request)
    {
        $userId = session('pwd_reset_user_id');
        $loginInput = session('pwd_reset_login');
        $maskedTelegram = null;

        if ($userId) {
            $user = User::find($userId);
            if ($user && !empty($user->telegram_id)) {
                $tid = (string) $user->telegram_id;
                $maskedTelegram = (strlen($tid) > 4) 
                    ? str_repeat('*', strlen($tid) - 4) . substr($tid, -4) 
                    : $tid;
            }
        }

        return view('auth.passwords.reset', compact('loginInput', 'maskedTelegram'));
    }

    /**
     * Proses verifikasi OTP dan perbarui kata sandi baru
     */
    public function reset(Request $request)
    {
        $request->validate([
            'login'                 => 'required|string',
            'otp'                   => 'required|string|size:6',
            'password'              => 'required|string|min:6|confirmed',
        ], [
            'login.required'        => 'Username atau Email wajib diisi.',
            'otp.required'          => 'Kode OTP 6-digit wajib diisi.',
            'otp.size'              => 'Kode OTP harus berupa 6 digit angka.',
            'password.required'     => 'Kata sandi baru wajib diisi.',
            'password.min'          => 'Kata sandi minimal 6 karakter.',
            'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $loginInput = trim($request->input('login'));
        $otpInput   = trim($request->input('otp'));

        // Cari user
        $user = User::where('email', $loginInput)
            ->orWhere('email', strtolower($loginInput))
            ->orWhere('username', $loginInput)
            ->orWhere('username', strtolower($loginInput))
            ->first();

        if (!$user) {
            return back()->withErrors([
                'login' => 'Akun dengan Username atau Email tersebut tidak ditemukan.',
            ])->withInput();
        }

        // Ambil OTP dari Cache
        $cachedOtp = Cache::get('pwd_reset_otp_' . $user->id);

        if (!$cachedOtp || $cachedOtp !== $otpInput) {
            return back()->withErrors([
                'otp' => 'Kode OTP salah atau telah kedaluwarsa. Silakan minta kode OTP baru.',
            ])->withInput();
        }

        // Update kata sandi baru
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Hapus OTP dari Cache agar tidak bisa digunakan lagi (One-Time Use)
        Cache::forget('pwd_reset_otp_' . $user->id);
        session()->forget(['pwd_reset_user_id', 'pwd_reset_login']);

        // Kirim konfirmasi notifikasi sukses ke Telegram
        if (!empty($user->telegram_id)) {
            $confirmMsg  = "✅ <b>PASSWORD BERHASIL DIPERBARUI</b>\n\n";
            $confirmMsg .= "Halo <b>" . htmlspecialchars($user->name) . "</b>,\n";
            $confirmMsg .= "Kata sandi akun Billing Telkom Anda telah berhasil diubah.\n";
            $confirmMsg .= "Silakan login di web menggunakan kata sandi baru Anda.\n\n";
            $confirmMsg .= "⚠️ <i>Jika Anda tidak merasa melakukan perubahan ini, segera hubungi Admin PIKOL.</i>";

            $this->telegramService->sendMessage($user->telegram_id, $confirmMsg);
        }

        return redirect()->route('login')->with('success_reset', 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }
}
