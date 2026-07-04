<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background-color: #F8FAFC; color: #334155; line-height: 1.6; }
        .wrapper { max-width: 600px; margin: 40px auto; padding: 20px; }
        .card { background: #ffffff; border: 1px solid #E2E8F0; border-radius: 16px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #0F94EA 0%, #0277C7 100%); padding: 32px 40px; text-align: center; }
        .header-logo { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
        .header-icon { width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; }
        .header-name { color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px; }
        .body { padding: 40px; }
        .greeting { font-size: 20px; font-weight: 600; color: #0F172A; margin-bottom: 16px; }
        .text { font-size: 15px; color: #334155; margin-bottom: 16px; }
        .btn { display: inline-block; padding: 14px 32px; background: #0F94EA; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; margin: 20px 0; }
        .btn:hover { background: #0277C7; }
        .btn-secondary { background: #F97316; }
        .divider { border: none; border-top: 1px solid #E2E8F0; margin: 28px 0; }
        .note { font-size: 13px; color: #64748B; }
        .highlight-box { background: #F0F8FF; border: 1px solid #BAE0FD; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .highlight-box strong { color: #0277C7; }
        .warning-box { background: #FFF7ED; border: 1px solid #FED7AA; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .success-box { background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .footer { background: #F8FAFC; padding: 24px 40px; border-top: 1px solid #E2E8F0; text-align: center; }
        .footer-text { font-size: 12px; color: #94A3B8; }
        .footer-links a { color: #64748B; text-decoration: none; margin: 0 8px; font-size: 12px; }
        .user-code { display: inline-block; background: #E0F1FE; color: #0277C7; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 16px; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="header-logo">
                    <div class="header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                    <span class="header-name">{{ config('app.name', 'ServiceHub') }}</span>
                </div>
            </div>
            <div class="body">
                @yield('content')
            </div>
            <div class="footer">
                <div class="footer-links">
                    <a href="{{ config('app.url') }}">Home</a>
                    <a href="{{ config('app.url') }}/help">Help</a>
                    <a href="{{ config('app.url') }}/contact">Contact</a>
                </div>
                <p class="footer-text" style="margin-top: 12px;">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                    You received this email because you have an account on our platform.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
