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
            color: #f093fb;
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #666;
        }

        .countdown {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }

        .countdown-item {
            background: #f0f0f0;
            border-radius: 10px;
            padding: 15px 20px;
            min-width: 80px;
            flex: 0 1 auto;
        }

        .countdown-number {
            font-size: 32px;
            font-weight: 700;
            display: block;
            color: #f093fb;
        }

        .countdown-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
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

            .countdown {
                gap: 15px;
            }

            .countdown-item {
                padding: 12px 16px;
                min-width: 70px;
            }

            .countdown-number {
                font-size: 28px;
            }

            .countdown-label {
                font-size: 11px;
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

            .countdown {
                gap: 10px;
            }

            .countdown-item {
                padding: 10px 12px;
                min-width: 60px;
            }

            .countdown-number {
                font-size: 24px;
            }

            .countdown-label {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚀</div>
        <h1>Something Exciting Is Coming!</h1>
        <p>{{ \App\Services\SettingsService::get('coming_soon_message', 'We\'re working hard to bring you an amazing experience. Our platform will be launching soon with incredible features designed just for you. Stay tuned!') }}</p>

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
    </div>
</body>
</html>

