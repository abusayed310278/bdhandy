@extends('emails.layout', ['subject' => "Your team account is ready"])

@section('content')
    <p class="greeting">Hi {{ $member->full_name }} 👋</p>

    <p class="text">
        Your account on <strong>{{ config('app.name') }}</strong> has been created by
        <strong>{{ $member->business?->business_name ?? 'your business' }}</strong>.
        You can now log in and start managing your daily work.
    </p>

    <div class="highlight-box">
        <p style="margin: 0 0 12px; font-size: 13px; color: #475569; font-weight: 600; letter-spacing: 0.3px;">YOUR LOGIN CREDENTIALS</p>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 6px 0; width: 100px; color: #64748B; font-size: 13px;">Employee Code</td>
                <td style="padding: 6px 0;"><span class="user-code">{{ $member->employee_code }}</span></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748B; font-size: 13px;">Email</td>
                <td style="padding: 6px 0; font-family: 'SFMono-Regular', Consolas, monospace; font-size: 14px; color: #0F172A; font-weight: 600;">{{ $member->email }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748B; font-size: 13px;">Password</td>
                <td style="padding: 6px 0; font-family: 'SFMono-Regular', Consolas, monospace; font-size: 14px; color: #0F172A; font-weight: 600; background: #ffffff; border: 1px solid #BAE0FD; padding: 8px 12px; border-radius: 6px;">{{ $password }}</td>
            </tr>
        </table>
    </div>

    <div class="warning-box">
        <p style="font-size: 13px; color: #9A3412; margin: 0;">
            <strong>🔒 Please change this password after your first login.</strong>
            We recommend using something only you can remember.
        </p>
    </div>

    <div style="text-align: center;">
        <a href="{{ $loginUrl }}" class="btn">Log In Now</a>
    </div>

    <hr class="divider">

    <p class="note">
        Need help? Just reply to this email and our team will get back to you.
    </p>
@endsection
