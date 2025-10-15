<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
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

        .countdown {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .countdown-item {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 15px 20px;
            min-width: 80px;
        }

        .countdown-number {
            font-size: 32px;
            font-weight: 700;
            display: block;
        }

        .countdown-label {
            font-size: 12px;
            opacity: 0.8;
            text-transform: uppercase;
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
        <div class="icon">🚀</div>
        <h1>Coming Soon</h1>
        <p>{{ \App\Services\SettingsService::get('coming_soon_message', 'Something amazing is coming soon! Stay tuned.') }}</p>

        @php
            $endTime = \App\Services\SettingsService::get('maintenance_end_time');
        @endphp

        @if($endTime)
            <div class="countdown">
                <div class="countdown-item">
                    <span class="countdown-number" id="days">--</span>
                    <span class="countdown-label">Days</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="hours">--</span>
                    <span class="countdown-label">Hours</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="minutes">--</span>
                    <span class="countdown-label">Minutes</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="seconds">--</span>
                    <span class="countdown-label">Seconds</span>
                </div>
            </div>

            <script>
                const countdownDate = new Date("{{ $endTime }}").getTime();

                function updateCountdown() {
                    const now = new Date().getTime();
                    const distance = countdownDate - now;

                    if (distance > 0) {
                        document.getElementById('days').textContent = Math.floor(distance / (1000 * 60 * 60 * 24));
                        document.getElementById('hours').textContent = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        document.getElementById('minutes').textContent = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        document.getElementById('seconds').textContent = Math.floor((distance % (1000 * 60)) / 1000);
                    } else {
                        document.getElementById('days').textContent = '00';
                        document.getElementById('hours').textContent = '00';
                        document.getElementById('minutes').textContent = '00';
                        document.getElementById('seconds').textContent = '00';
                    }
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            </script>
        @endif

        <div class="admin-link">
            <a href="{{ route('login') }}">Super Admin Login</a>
        </div>
    </div>
</body>
</html>

