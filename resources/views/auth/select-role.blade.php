<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Role</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('{{ asset('bg-role.jpg') }}') no-repeat center center/cover;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            position: absolute;
            top: 20px;
            left: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }
        .header h1 {
            font-size: 20px;
            color: #fff;
            margin: 0;
        }

       .role-buttons {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.role-btn {
    display: block;
    margin: 12px auto;
    width: 220px;
    padding: 14px;
    background: #FFD600;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    transition: 0.3s;
    color: #000; /* Ensure text is black */
}
.role-btn:hover {
    background: #e6c200;
    transform: scale(1.05);
}


        /* Footer */
        .footer {
            text-align: center;
            padding: 15px;
            color: #fff;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- School Logo + Name -->
    <div class="header">
        <img src="{{ asset('storage/'.\App\Models\Setting::getValue('system_logo')) }}" alt="Logo">
        <h1>{{ \App\Models\Setting::getValue('department_name') ?? 'Your Department' }}</h1>
    </div>

    <!-- Role Buttons -->
   <div class="role-buttons">
    <button class="role-btn" onclick="window.location.href='{{ url('/login/admin') }}'">Administrator</button>
    <button class="role-btn" onclick="window.location.href='{{ url('/login/dean') }}'">Dean</button>
    <button class="role-btn" onclick="window.location.href='{{ url('/login/chairperson') }}'">Chairperson</button>
    <button class="role-btn" onclick="window.location.href='{{ url('/login/faculty') }}'">Faculty</button>

</div>

    <!-- Footer -->
    <div class="footer">
        © {{ date('Y') }} School Records Management System. All rights reserved.
    </div>
</body>
</html>
