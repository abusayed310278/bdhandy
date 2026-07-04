@extends('emails.layout', ['subject' => "You've been invited to " . config('app.name')])

@section('content')
    <p class="greeting">Welcome, {{ $member->full_name }}! 👋</p>

    <p class="text">
        <strong>{{ $member->business?->business_name ?? 'Your team' }}</strong> has invited you to join their team
        on <strong>{{ config('app.name') }}</strong> as
        @if($member->designation)
            <strong>{{ $member->designation }}</strong>.
        @else
            a team member.
        @endif
    </p>

    <div class="highlight-box">
        <p style="margin-bottom: 8px; font-size: 13px; color: #475569;">Your employee code</p>
        <span class="user-code">{{ $member->employee_code }}</span>
    </div>

    <p class="text">
        To complete your setup, click the button below and choose a secure password.
        The link will expire in <strong>60 minutes</strong> for your security.
    </p>

    <div style="text-align: center;">
        <a href="{{ $setupUrl }}" class="btn">Set Up My Account</a>
    </div>

    <hr class="divider">

    <p class="note">
        After setting your password, you can log in and start receiving job assignments,
        track your daily schedule, log work hours, and view your earnings.
    </p>

    <p class="note" style="margin-top: 16px;">
        Didn't expect this invite? You can safely ignore this email.
    </p>
@endsection
