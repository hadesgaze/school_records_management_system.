<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($role) }} Login | SRMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- ✅ Bootstrap Icons CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('{{ asset('bg-role.jpg') }}') no-repeat center center/cover;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            background: #FFD600;
            padding: 30px 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease-in-out;
        }

        .login-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #000;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 12px 42px 12px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
            outline: none;
            transition: border 0.2s ease-in-out;
        }

        .form-control:focus {
            border-color: #004080;
            box-shadow: 0 0 5px rgba(0, 64, 128, 0.3);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #555;
            font-size: 18px;
            user-select: none;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #004080;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #004080;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-login:hover {
            background: #003060;
        }

        .extra-links {
            text-align: center;
            margin-top: 20px;
        }

        .extra-links a {
            display: inline-block;
            margin: 5px 10px;
            color: #000;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .extra-links a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* ✅ Responsive adjustments */
        @media (max-width: 480px) {
            .login-box {
                padding: 25px 20px;
            }
            .login-box h2 {
                font-size: 1.3rem;
            }
            .form-control {
                font-size: 14px;
                padding: 10px 38px 10px 10px;
            }
            .btn-login {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>{{ ucfirst($role) }} Login</h2>

        {{-- Error Message --}}
        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif
        
        <form method="POST" action="{{ url('/login/' . $role) }}">
            @csrf

            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <i class="bi bi-eye toggle-password" id="togglePassword" onclick="togglePassword()"></i>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="extra-links">
            <a href="{{ url('/password/reset') }}">Forgot Password?</a>
            <a href="{{ route('select') }}">Back to Role Selection</a>
        </div>
    </div>

    {{-- ✅ JS for show/hide + auto-hide --}}
    <script>
        let hideTimer;

        function togglePassword() {
            const passwordField = document.getElementById("password");
            const toggleIcon = document.getElementById("togglePassword");
            const isHidden = passwordField.type === "password";

            // toggle visibility
            passwordField.type = isHidden ? "text" : "password";
            toggleIcon.classList.toggle("bi-eye");
            toggleIcon.classList.toggle("bi-eye-slash");

            // clear previous timer
            clearTimeout(hideTimer);

            // auto-hide after 3 seconds
            if (isHidden) {
                hideTimer = setTimeout(() => {
                    passwordField.type = "password";
                    toggleIcon.classList.remove("bi-eye-slash");
                    toggleIcon.classList.add("bi-eye");
                }, 3000);
            }
        }
    </script>
</body>
</html>
