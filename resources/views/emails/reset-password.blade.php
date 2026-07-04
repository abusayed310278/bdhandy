@extends('emails.layout', ['subject' => 'Reset your password'])

@section('content')
    <p class="greeting">Hi {{ $user->name }} 🔒</p>

    <p class="text">
        We received a request to reset the password for your <strong>{{ config('app.name') }}</strong> account.
    </p>

    <p class="text">
        Click the button below to choose a new password. This link will expire in
        <strong>{{ $count ?? 60 }} minutes</strong>.
    </p>

    <div style="text-align: center;">
        <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
    </div>

    <hr class="divider">

    <div class="warning-box">
        <p style="font-size: 13px; color: #9A3412; margin: 0;">
            <strong>⚠️ Didn't request this?</strong>
            You can safely ignore this email — your password won't be changed.
        </p>
    </div>

    <p class="note" style="margin-top: 20px;">
        If the button doesn't work, copy and paste this URL into your browser:
    </p>
    <p style="font-size: 12px; color: #94A3B8; word-break: break-all; background: #F8FAFC; padding: 12px; border-radius: 8px; font-family: 'SFMono-Regular', Consolas, monospace;">
        {{ $resetUrl }}
    </p>
@endsection
