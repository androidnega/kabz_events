<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Removed - Kabz Events</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .reason-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f3f4f6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;">📷 Media Removed</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Kabz Events Platform</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $userName }}</strong>,</p>

        <p>We're writing to inform you that your <strong>{{ $mediaType }}</strong> has been removed from the Kabz Events platform by our administrative team.</p>

        <div class="reason-box">
            <strong>Reason for Removal:</strong><br>
            {{ $reason }}
        </div>

        <p><strong>What this means:</strong></p>
        <ul>
            <li>The media file has been permanently deleted from our system</li>
            <li>It is no longer visible on your profile or anywhere on the platform</li>
            <li>You can upload a new, compliant file if you wish</li>
        </ul>

        <p><strong>Need clarification?</strong></p>
        <p>If you believe this was done in error or need more information, please contact our support team.</p>

        <div style="text-align: center;">
            <a href="{{ url('/dashboard') }}" class="button">Go to Dashboard</a>
        </div>

        <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 14px; color: #6b7280;">
            <strong>Please ensure all future uploads comply with our Terms of Service and Community Guidelines.</strong>
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Kabz Events. All rights reserved.</p>
        <p>
            <a href="{{ url('/') }}" style="color: #667eea; text-decoration: none;">Visit Website</a> | 
            <a href="{{ url('/dashboard') }}" style="color: #667eea; text-decoration: none;">Dashboard</a>
        </p>
    </div>
</body>
</html>

