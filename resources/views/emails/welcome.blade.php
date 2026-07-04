@extends('emails.layout')

@section('content')
    <p class="greeting">Welcome to {{ config('app.name') }}, {{ $user->name }}!</p>

    <p class="text">
        We're thrilled to have you on board. Your account has been created successfully.
    </p>

    <div class="highlight-box">
        <p style="font-size: 13px; color: #64748B; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Your Account ID</p>
        <span class="user-code">{{ $user->user_code }}</span>
        <p style="font-size: 13px; color: #64748B; margin-top: 8px;">Keep this safe — you'll need it for support.</p>
    </div>

    @if(!$user->hasVerifiedEmail())
        <p class="text">
            Please verify your email address to unlock all features. Check your inbox for the verification link.
        </p>
    @endif

    <p class="text">
        @if($user->hasRole('customer'))
            Start by searching for trusted service providers near you.
        @else
            Complete your provider profile to start receiving leads from customers.
        @endif
    </p>

    <div style="text-align: center;">
        <a href="{{ config('app.url') }}/dashboard" class="btn">Go to Dashboard</a>
    </div>

    <hr class="divider">
    <p class="note">
        If you didn't create this account, please ignore this email or
        <a href="{{ config('app.url') }}/contact" style="color: #0F94EA;">contact support</a>.
    </p>
@endsection
