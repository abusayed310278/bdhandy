@extends('emails.layout')

@section('content')
    <p class="greeting">Action Required, {{ $user->name }}</p>

    <p class="text">
        We reviewed your submitted documents and unfortunately we were unable to verify your profile at this time.
    </p>

    @if($reason)
        <div class="warning-box">
            <strong style="color: #EA580C;">Reason:</strong>
            <p style="margin-top: 8px; font-size: 14px; color: #334155;">{{ $reason }}</p>
        </div>
    @endif

    <p class="text">
        Don't worry — you can re-submit your documents. Here's what to do:
    </p>
    <ol style="padding-left: 20px; font-size: 14px; color: #334155; margin-bottom: 20px; line-height: 1.9;">
        <li>Log in to your account</li>
        <li>Go to your provider profile</li>
        <li>Upload the corrected document(s)</li>
        <li>Re-submit for verification</li>
    </ol>

    <div style="text-align: center;">
        <a href="{{ config('app.url') }}/provider/onboarding/documents" class="btn btn-secondary">Re-Submit Documents</a>
    </div>

    <hr class="divider">
    <p class="note">
        Need help? <a href="{{ config('app.url') }}/contact" style="color: #0F94EA;">Contact our verification team</a> and reference your Account ID: <strong>{{ $user->user_code }}</strong>.
    </p>
@endsection
