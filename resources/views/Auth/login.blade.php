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
            background: rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.15); /* Transparan putih */
            backdrop-filter: blur(15px); /* Efek blur kaca */
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            padding: 50px 40px;
            color: white;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            max-height: 65px;
            width: auto;
            object-fit: contain;
            display: inline-block;
        }

        .login-header p.sub-title {
            font-size: 16px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.9);
        }

        .login-header h2 {
            font-size: 26px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Styling Input Wrapper & Icons */
        .input-group-custom {
            position: relative;
            margin-bottom: 22px;
        }

        .input-group-custom i.input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            z-index: 5;
        }

        .input-group-custom i.toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
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
            border-radius: 12px;
            color: white;
            padding: 13px 40px 13px 48px; /* Padding kiri untuk ikon, kanan untuk mata */
            font-size: 15px;
            transition: all 0.3s;
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

        /* Lupa Kata Sandi */
        .forgot-password {
            text-align: right;
            margin-top: -12px;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: underline;
            font-size: 13px;
            transition: color 0.2s;
        }

        .forgot-password a:hover {
            color: white;
        }

        /* Tombol Masuk Merah Bulat */
        .btn-login {
            background: #a61c30; /* Warna merah gelap Telkom */
            color: white;
            border: none;
            padding: 13px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(166, 28, 48, 0.3);
        }

        .btn-login:hover {
            background: #be2037;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(166, 28, 48, 0.5);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Media Sosial Login */
        .social-login {
            text-align: center;
            margin-top: 35px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .social-icons {
            display: flex;
            gap: 10px;
        }

        .social-icons a {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            text-decoration: none;
        }

        .social-icons a:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.1);
        }

        .alert-custom {
            background: rgba(220, 53, 69, 0.3);
            border: 1px solid rgba(220, 53, 69, 0.4);
            color: #ffb3b8;
            border-radius: 12px;
            font-size: 14px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }

        /* Responsif untuk HP */
        @media (max-width: 768px) {
            body {
                justify-content: center;
                padding-left: 15px;
                padding-right: 15px;
                background-position: 30% center; /* Geser background sedikit ke kanan di layar kecil */
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
                <!-- Logo Telkom -->
                <img src="{{ asset('image/lg-login.png') }}" alt="Logo Telkom" class="login-logo mb-3">
                <p class="sub-title">Selamat Datang di</p>
                <h2>Billing Telkom Cirebon</h2>
            </div>

            @if ($errors->any())
                <div class="alert alert-custom">
                    <i class="fas fa-exclamation-circle me-2"></i> 
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Input Username / Email -->
                <div class="input-group-custom">
                    <i class="far fa-user input-icon"></i>
                    <input type="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Username" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus>
                </div>

                <!-- Input Password -->
                <div class="input-group-custom">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           name="password" 
                           id="password-input"
                           class="form-control" 
                           placeholder="Kata Sandi" 
                           required>
                    <i class="far fa-eye-slash toggle-password" onclick="togglePasswordVisibility()"></i>
                </div>

                <!-- Lupa Sandi -->
                <div class="forgot-password">
                    <a href="#">Lupa Kata Sandi?</a>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-login">
                    Masuk
                </button>

                <!-- Default Credentials Hint -->
                <div class="text-center mt-4" style="font-size: 13px; color: rgba(255, 255, 255, 0.6);">
                    <small>Default: admin@telkom.com / password</small>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fungsi show/hide password
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