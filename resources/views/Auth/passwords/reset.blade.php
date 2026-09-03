<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" type="image/png" href="{{ asset('image/icon.png') }}">
    
    <title>Verifikasi OTP & Reset Password - Billing Telkom Cirebon</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            background: url('/image/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            font-family: 'Montserrat', 'Segoe UI', sans-serif;
            margin: 0;
            padding-left: 8%;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.25);
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 385px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
            padding: 38px 28px;
            color: white;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .login-logo {
            max-height: 46px;
            width: auto;
            object-fit: contain;
            display: inline-block;
        }

        .login-header p.sub-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, 0.9);
        }

        .login-header h2 {
            font-size: 17px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 14px;
        }

        .input-group-custom i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            font-size: 13.5px;
            z-index: 5;
        }

        .input-group-custom i.toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            font-size: 13.5px;
            z-index: 5;
            cursor: pointer;
            transition: color 0.2s;
        }

        .input-group-custom i.toggle-password:hover {
            color: white;
        }

        .input-group-custom .form-control {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 10px;
            color: white;
            padding: 10px 40px 10px 38px;
            font-size: 13px;
            transition: all 0.25s;
            width: 100%;
        }

        .otp-input {
            letter-spacing: 6px;
            font-size: 16px !important;
            font-weight: 700 !important;
            text-align: center;
            padding-left: 20px !important;
            padding-right: 20px !important;
            background: rgba(0, 136, 204, 0.22) !important;
            border-color: rgba(0, 136, 204, 0.5) !important;
        }

        /* Hapus icon mata bawaan browser Edge / Chrome / Windows */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .input-group-custom .form-control::placeholder {
            color: rgba(255, 255, 255, 0.65);
        }

        .input-group-custom .form-control:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.6);
            outline: none;
            box-shadow: none;
            color: white;
        }

        .form-label-custom {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.85);
            margin-bottom: 4px;
            display: block;
        }

        .btn-save-pass {
            background: #a61c30;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 22px;
            font-weight: 700;
            font-size: 13.5px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            width: 100%;
            transition: all 0.25s;
            box-shadow: 0 4px 12px rgba(166, 28, 48, 0.3);
            margin-top: 6px;
        }

        .btn-save-pass:hover {
            background: #be2037;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(166, 28, 48, 0.45);
            color: white;
        }

        .btn-save-pass:active {
            transform: translateY(0);
        }

        .alert-custom {
            background: rgba(220, 53, 69, 0.35);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ffd6d9;
            border-radius: 10px;
            font-size: 12px;
            padding: 8px 12px;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        .alert-success-custom {
            background: rgba(25, 135, 84, 0.35);
            border: 1px solid rgba(25, 135, 84, 0.5);
            color: #c0f3c4;
            border-radius: 10px;
            font-size: 12px;
            padding: 8px 12px;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 14px;
            font-size: 12px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: white;
        }

        @media (max-width: 768px) {
            body {
                justify-content: center;
                padding-left: 15px;
                padding-right: 15px;
                background-position: 30% center;
            }
            .login-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('image/lg-login.png') }}" alt="Logo Telkom" class="login-logo mb-3">
                <p class="sub-title">Verifikasi & Buat Password</p>
                <h2>Billing Telkom Cirebon</h2>
            </div>

            {{-- Status pengiriman OTP --}}
            @if (session('status'))
                <div class="alert-success-custom">
                    <i class="fab fa-telegram-plane me-1"></i>
                    {{ session('status') }}
                    @if (!empty($maskedTelegram))
                        <div class="mt-1 small" style="opacity: 0.9;">Terkirim ke ID: <b>{{ $maskedTelegram }}</b></div>
                    @endif
                </div>
            @endif

            {{-- Error --}}
            @if ($errors->any())
                <div class="alert-custom">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <!-- Input Username / Email -->
                <div class="input-group-custom">
                    <label class="form-label-custom">Username / Email</label>
                    <div style="position: relative;">
                        <i class="far fa-user input-icon"></i>
                        <input type="text"
                               name="login"
                               class="form-control"
                               placeholder="Username atau Email"
                               value="{{ $loginInput ?? old('login') }}"
                               required>
                    </div>
                </div>

                <!-- Input Kode OTP 6 Digit -->
                <div class="input-group-custom">
                    <label class="form-label-custom">Kode OTP (6 Angka dari Telegram)</label>
                    <input type="text"
                           name="otp"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           class="form-control otp-input"
                           placeholder="••••••"
                           value="{{ old('otp') }}"
                           required
                           autofocus
                           autocomplete="one-time-code">
                </div>

                <!-- Input Password Baru -->
                <div class="input-group-custom">
                    <label class="form-label-custom">Kata Sandi Baru</label>
                    <div style="position: relative;">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password"
                               name="password"
                               id="password-input"
                               class="form-control"
                               placeholder="Min. 6 Karakter"
                               required>
                        <i class="far fa-eye-slash toggle-password" onclick="togglePass('password-input', this)" title="Tampilkan/Sembunyikan"></i>
                    </div>
                </div>

                <!-- Input Konfirmasi Password -->
                <div class="input-group-custom">
                    <label class="form-label-custom">Konfirmasi Kata Sandi Baru</label>
                    <div style="position: relative;">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password"
                               name="password_confirmation"
                               id="password-confirm-input"
                               class="form-control"
                               placeholder="Ulangi Kata Sandi Baru"
                               required>
                        <i class="far fa-eye-slash toggle-password" onclick="togglePass('password-confirm-input', this)" title="Tampilkan/Sembunyikan"></i>
                    </div>
                </div>

                <button type="submit" class="btn-save-pass">
                    <i class="fas fa-check-circle me-1.5"></i>Simpan Kata Sandi Baru
                </button>
            </form>

            <div class="d-flex justify-content-between align-items-center mt-3 pt-1">
                <a href="{{ route('password.request') }}" class="back-link mt-0">
                    <i class="fas fa-redo-alt me-1"></i> Kirim Ulang OTP
                </a>
                <a href="{{ route('login') }}" class="back-link mt-0">
                    <i class="fas fa-sign-in-alt me-1"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePass(inputId, iconEl) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                iconEl.classList.remove('fa-eye-slash');
                iconEl.classList.add('fa-eye');
            } else {
                input.type = 'password';
                iconEl.classList.remove('fa-eye');
                iconEl.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
