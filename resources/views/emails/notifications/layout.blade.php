<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #f4f6f9; color: #1a1a2e; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 32px 40px; }
        .header h1 { color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: -0.3px; }
        .header p  { color: rgba(255,255,255,0.7); font-size: 13px; margin-top: 4px; }
        .body { padding: 36px 40px; }
        .body h2 { font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
        .body p  { font-size: 15px; color: #4b5563; line-height: 1.7; margin-bottom: 16px; }
        .cta { display: inline-block; margin-top: 8px; padding: 12px 28px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 28px 0; }
        .footer { background: #f9fafb; padding: 24px 40px; }
        .footer p { font-size: 12px; color: #9ca3af; line-height: 1.6; }
        .footer a { color: #6366f1; text-decoration: none; }
        @media (max-width: 640px) {
            .header, .body, .footer { padding: 24px 20px; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Notification from your account</p>
    </div>

    <div class="body">
        <h2>{{ $heading }}</h2>
        <p>{!! $message !!}</p>

        @if (!empty($actionUrl) && !empty($actionText))
            <a href="{{ $actionUrl }}" class="cta">{{ $actionText }}</a>
        @endif

        @isset($slot)
            <hr class="divider">
            {{ $slot }}
        @endisset
    </div>

    <div class="footer">
        <p>
            You received this email because you have an account with <strong>{{ config('app.name') }}</strong>.<br>
            You can manage your notification preferences in your
            <a href="{{ url('/profile') }}">account settings</a>.<br><br>
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</div>
</body>
</html>
