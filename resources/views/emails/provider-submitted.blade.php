@extends('emails.layout')

@section('content')
    <p class="greeting">Documents Submitted, {{ $user->name }}!</p>

    <p class="text">
        Thank you for submitting your documents for verification. Our team will review your profile and documents within <strong>24–48 hours</strong>.
    </p>

    <div class="highlight-box">
        <strong>What happens next?</strong>
        <ul style="margin-top: 10px; padding-left: 20px; font-size: 14px; color: #334155; line-height: 1.8;">
            <li>Our verification team reviews your documents</li>
            <li>You'll receive an email with the decision</li>
            <li>Once approved, your profile goes live and you can start receiving leads</li>
        </ul>
    </div>

    <p class="text">
        In the meantime, you can log in to check your verification status.
    </p>

    <div style="text-align: center;">
        <a href="{{ route('provider.onboarding.pending') }}" class="btn">Check Verification Status</a>
    </div>

    <hr class="divider">
    <p class="note">
        Have questions? <a href="{{ config('app.url') }}/contact" style="color: #0F94EA;">Contact our support team</a> and reference your Account ID: <strong>{{ $user->user_code }}</strong>.
    </p>
@endsection
