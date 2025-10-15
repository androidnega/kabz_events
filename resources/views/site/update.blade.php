<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Update - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        h1 {
            font-size: 42px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .progress-container {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }

        .progress-bar {
            width: 100%;
            height: 30px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            width: 0%;
            animation: progress 3s ease-in-out infinite;
        }

        @keyframes progress {
            0% {
                width: 0%;
            }
            50% {
                width: 70%;
            }
            100% {
                width: 100%;
            }
        }

        .progress-text {
            margin-top: 15px;
            font-size: 14px;
            opacity: 0.8;
        }

        .end-time {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-top: 30px;
            font-size: 16px;
        }

        .admin-link {
            margin-top: 40px;
            font-size: 14px;
        }

        .admin-link a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .admin-link a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⬆️</div>
        <h1>System Update</h1>
        <p>{{ \App\Services\SettingsService::get('update_message', 'We are currently updating our system with exciting new features. Please check back soon!') }}</p>

        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <div class="progress-text">Updating system components...</div>
        </div>

        @php
            $endTime = \App\Services\SettingsService::get('maintenance_end_time');
        @endphp
        
        @if($endTime)
            <div class="end-time">
                <strong>Update expected to complete:</strong><br>
                {{ \Carbon\Carbon::parse($endTime)->format('F j, Y g:i A') }}
            </div>
        @endif

        <div class="admin-link">
            <a href="{{ route('login') }}">Super Admin Login</a>
        </div>
    </div>
</body>
</html>

