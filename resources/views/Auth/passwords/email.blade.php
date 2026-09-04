<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('image/icon.png') }}?v=2">
    
    <title>Lupa Password - Billing Telkom Cirebon</title>

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
            padding: 42px 30px;
            color: white;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-logo {
            max-height: 48px;
            width: auto;
            object-fit: contain;
            display: inline-block;
        }

        .login-header p.sub-title {
            font-size: 12.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, 0.9);
        }

        .login-header h2 {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .desc-text {
            font-size: 12px;
            color: rgba(255,255,255,0.85);
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.55;
            background: rgba(0, 136, 204, 0.2);
            border: 1px solid rgba(0, 136, 204, 0.4);
            border-radius: 10px;
            padding: 10px 12px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 18px;
        }

        .input-group-custom i.input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            z-index: 5;
        }

        .input-group-custom .form-control {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 10px;
            color: white;
            padding: 11px 15px 11px 42px;
            font-size: 13.5px;
            transition: all 0.25s;
            width: 100%;
        }

        .input-group-custom .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group-custom .form-control:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.6);
            outline: none;
            box-shadow: none;
            color: white;
        }

        .btn-telegram-otp {
            background: linear-gradient(135deg, #0088cc 0%, #006699 100%);
            color: white;
            border: none;
            padding: 11px;
            border-radius: 22px;
            font-weight: 700;
            font-size: 13.5px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            width: 100%;
            transition: all 0.25s;
            box-shadow: 0 4px 12px rgba(0, 136, 204, 0.35);
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-telegram-otp:hover {
            background: linear-gradient(135deg, #0099e6 0%, #0077b3 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 136, 204, 0.5);
            color: white;
        }

        .btn-telegram-otp:active {
            transform: translateY(0);
        }

        .alert-custom {
            background: rgba(220, 53, 69, 0.35);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ffd6d9;
            border-radius: 10px;
            font-size: 12px;
            padding: 9px 12px;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .alert-success-custom {
            background: rgba(25, 135, 84, 0.35);
            border: 1px solid rgba(25, 135, 84, 0.5);
            color: #bbf0bf;
            border-radius: 10px;
            font-size: 12px;
            padding: 9px 12px;
            margin-bottom: 16px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 12.5px;
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
                <p class="sub-title">Reset Password</p>
                <h2>Billing Telkom Cirebon</h2>
            </div>

            {{-- Notifikasi Error --}}
            @if ($errors->any())
                <div class="alert alert-custom">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Info Telegram --}}
            <div class="desc-text">
                <i class="fab fa-telegram-plane me-1 text-info"></i>
                Masukkan Username atau Email akun Anda. Kode OTP 6-digit akan dikirimkan langsung ke <b>Telegram pribadi</b> Anda.
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Input Username / Email -->
                <div class="input-group-custom">
                    <i class="far fa-user input-icon"></i>
                    <input type="text"
                           name="login"
                           class="form-control"
                           placeholder="Username atau Email"
                           value="{{ old('login') }}"
                           required
                           autofocus>
                </div>

                <button type="submit" class="btn-telegram-otp">
                    <i class="fab fa-telegram-plane fa-lg"></i>
                    <span>Kirim Kode OTP</span>
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
            </a>
        </div>
    </div>
</body>
</html>
