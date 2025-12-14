<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('settings.system_title') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column; /* so footer text can sit at bottom */
            background: url('{{ asset("bg-landing.png") }}') no-repeat center center fixed;
            background-size: cover; /* prevent repetition */
            color: white;
        }

        /* Container */
        .container {
            position: relative;
            text-align: center;
            width: 100%;
        }

        /* Yellow Login Box */
        .login-box {
            background: #FFD700;
            padding: 40px 30px;
            border-radius: 30px;
            display: inline-block;
            margin-bottom: 40px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .login-box h1 {
            font-size: 36px;
            margin-bottom: 10px;
            color: #000;
            font-weight: bold;
        }

        .login-box p {
            font-size: 18px;
            margin-bottom: 20px;
            color: #000;
        }

        .btn-login {
            display: inline-block;
            background: #0A61C9;
            color: white;
            padding: 12px 40px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .btn-login:hover {
            background: #084a99;
        }

        /* Mission, Vision, Core Values Section */
        .footer-info {
            display: flex;
            justify-content: space-around;
            text-align: center;
            flex-wrap: wrap;
            max-width: 1000px;
            margin: 0 auto;
        }

        .footer-info div {
            flex: 1;
            min-width: 250px;
            margin: 10px;
        }

        .footer-info h3 {
            margin-bottom: 10px;
            font-weight: bold;
            color: #fff;
        }

        .footer-info p {
            font-size: 16px;
            line-height: 1.5;
        }

        /* Footer (All rights reserved) */
        .footer {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            color: white;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1> Biliran Province State University</h1>
            <p>School Record Management System</p>

            <!-- Login button always visible -->
            <a href="{{ route('select') }}" class="btn-login">Login</a>
        </div>
    </div>

    <div class="footer-info">
        <div>
            <h3>Mission</h3>
            <p>To provide quality and relevant instruction, research, and extension services for the empowerment and development of our people. </p>
        </div>
        <div>
            <h3>Vision</h3>
            <p>BiPSU is an internationally recognized university responsive to the needs of the local and global communities.</p>
        </div>
        <div>
            <h3>Core Values</h3>
            <p>Brilliance. Innovation. Progress. Service. Unit. </p>
        </div>
    </div>

    <div class="footer">
        © {{ date('Y') }} School Record Management System. All rights reserved.
    </div>
</body>
</html>
