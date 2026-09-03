<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/icon.png') }}">
    
    <title>Login - Billing Telkom Cirebon</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Font Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            background: url('/image/bg-login.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Menggeser card ke kiri */
            font-family: 'Montserrat', 'Segoe UI', sans-serif;
            margin: 0;
            padding-left: 8%; /* Jarak card dari tepi kiri layar */
        }
        
        /* Overlay gelap transparan di belakang card agar gambar tetap terlihat tapi teks lebih terbaca */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.25);
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 375px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.16); /* Transparan putih */
            backdrop-filter: blur(15px); /* Efek blur kaca */
            -webkit-backdrop-filter: blur(15px);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
            padding: 45px 32px; /* Panjang vertikal proporsional */
            color: white;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-logo {
            max-height: 50px;
            width: auto;
            object-fit: contain;
            display: inline-block;
        }

        .login-header p.sub-title {
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, 0.9);
        }

        .login-header h2 {
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Styling Input Wrapper & Icons */
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

        .input-group-custom i.toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
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
            padding: 11px 40px 11px 42px;
            font-size: 13.5px;
            transition: all 0.25s;
            width: 100%;
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
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group-custom .form-control:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.6);
            outline: none;
            box-shadow: none;
            color: white;
        }

        /* Tombol Masuk Merah Bulat */
        .btn-login {
            background: #a61c30; /* Warna merah gelap Telkom */
            color: white;
            border: none;
            padding: 11px;
            border-radius: 22px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            width: 100%;
            transition: all 0.25s;
            box-shadow: 0 4px 12px rgba(166, 28, 48, 0.3);
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #be2037;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(166, 28, 48, 0.45);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert-custom {
            background: rgba(220, 53, 69, 0.3);
            border: 1px solid rgba(220, 53, 69, 0.4);
            color: #ffb3b8;
            border-radius: 10px;
            font-size: 12.5px;
            padding: 8px 12px;
            margin-bottom: 18px;
        }

        /* Responsif untuk HP */
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

        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 14px;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.72);
            text-decoration: none;
            transition: color 0.2s;
            letter-spacing: 0.3px;
        }

        .forgot-link:hover {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <!-- Logo Telkom -->
                <img src="{{ asset('image/lg-login.png') }}" alt="Logo Telkom" class="login-logo mb-3">
                <p class="sub-title">Selamat Datang di</p>
                <h2>Billing Telkom Cirebon</h2>
            </div>

            @if (session('success_reset'))
                <div class="alert" style="background: rgba(25, 135, 84, 0.35); border: 1px solid rgba(25, 135, 84, 0.5); color: #c0f3c4; border-radius: 10px; font-size: 12.5px; padding: 10px 12px; margin-bottom: 18px;">
                    <i class="fas fa-check-circle me-1.5"></i> 
                    {{ session('success_reset') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-custom">
                    <i class="fas fa-exclamation-circle me-1.5"></i> 
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
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

                <!-- Input Password dengan Custom Toggle Eye -->
                <div class="input-group-custom">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           name="password" 
                           id="password-input"
                           class="form-control" 
                           placeholder="Kata Sandi" 
                           required>
                    <i class="far fa-eye-slash toggle-password" onclick="togglePasswordVisibility()" title="Tampilkan/Sembunyikan Kata Sandi"></i>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-login">
                    Masuk
                </button>

                <!-- Link Lupa Password -->
                <a href="{{ route('password.request') }}" class="forgot-link">
                    <i class="fas fa-key me-1"></i> Lupa Password?
                </a>
            </form>
        </div>
    </div>

    <script>
        // Fungsi show/hide password interaktif
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password-input');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>