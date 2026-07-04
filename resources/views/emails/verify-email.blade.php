@extends('emails.layout', ['subject' => 'Verify your email address'])

@section('content')
    <p class="greeting">Hi {{ $user->name }} 👋</p>

    <p class="text">
        Thanks for signing up to <strong>{{ config('app.name') }}</strong>!
        Before you get started, we need to verify that this email address belongs to you.
    </p>

    <p class="text">
        Click the button below to confirm your email. This link is valid for the next 60 minutes.
    </p>

    <div style="text-align: center;">
        <a href="{{ $verificationUrl }}" class="btn">Verify My Email</a>
    </div>

    <hr class="divider">

    <p class="note">
        If the button doesn't work, copy and paste this URL into your browser:
    </p>
    <p style="font-size: 12px; color: #94A3B8; word-break: break-all; background: #F8FAFC; padding: 12px; border-radius: 8px; font-family: 'SFMono-Regular', Consolas, monospace;">
        {{ $verificationUrl }}
    </p>

    <p class="note" style="margin-top: 20px;">
        Didn't sign up for {{ config('app.name') }}? You can safely ignore this email — no account will be created.
    </p>
@endsection
