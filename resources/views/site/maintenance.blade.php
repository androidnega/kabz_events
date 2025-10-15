<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - {{ config('app.name') }}</title>
    @php
        $primaryColor = \App\Services\SettingsService::get('primary_color', '#9333ea');
    @endphp
    
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 90%;
            text-align: center;
            background: #ffffff;
            border-radius: 20px;
            padding: 50px 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 42px;
            margin-bottom: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #666;
        }

        .end-time {
            background: #f0f0f0;
            border-radius: 10px;
            padding: 15px;
            margin-top: 30px;
            font-size: 16px;
            color: #555;
        }

        .end-time strong {
            color: #333;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 40px 25px;
            }

            .icon {
                font-size: 60px;
            }

            h1 {
                font-size: 32px;
            }

            p {
                font-size: 16px;
            }

            .end-time {
                font-size: 14px;
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                width: 95%;
            }

            .icon {
                font-size: 50px;
            }

            h1 {
                font-size: 28px;
            }

            p {
                font-size: 15px;
            }

            .end-time {
                font-size: 13px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚙️</div>
        <h1>We'll Be Right Back!</h1>
        <p>{{ \App\Services\SettingsService::get('maintenance_message', 'Our site is currently undergoing scheduled maintenance to improve your experience. We appreciate your patience and will be back online soon!') }}</p>
        
        @php
            $endTime = \App\Services\SettingsService::get('maintenance_end_time');
        @endphp
        
        @if($endTime)
            <div class="end-time">
                <strong>Expected to be back:</strong><br>
                {{ \Carbon\Carbon::parse($endTime)->format('F j, Y g:i A') }}
            </div>
        @endif
    </div>
</body>
</html>

