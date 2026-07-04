@extends('emails.layout')

@section('content')
    <p class="greeting">Action Required, {{ $user->name }}</p>

    <p class="text">
        One of your submitted documents needs to be updated before your profile can be fully verified.
    </p>

    <div class="warning-box">
        <strong style="color: #EA580C;">Document: {{ $document->documentType->name ?? 'Document' }}</strong>
        @if($document->document_number)
            <p style="margin-top: 4px; font-size: 13px; color: #64748B;">No: {{ $document->document_number }}</p>
        @endif
        @if($document->rejection_reason)
            <p style="margin-top: 10px; font-size: 14px; color: #334155;">
                <strong>Reason:</strong> {{ $document->rejection_reason }}
            </p>
        @endif
    </div>

    <p class="text">Please re-upload the correct document as soon as possible:</p>

    <ol style="padding-left: 20px; font-size: 14px; color: #334155; margin-bottom: 20px; line-height: 1.9;">
        <li>Log in to your account</li>
        <li>Click the button below to go to your documents page</li>
        <li>Upload the corrected <strong>{{ $document->documentType->name ?? 'document' }}</strong></li>
        <li>Re-submit for verification</li>
    </ol>

    <div style="text-align: center;">
        <a href="{{ route('provider.onboarding.documents') }}" class="btn btn-secondary">Re-Upload Document</a>
    </div>

    <hr class="divider">
    <p class="note">
        Need help? <a href="{{ config('app.url') }}/contact" style="color: #0F94EA;">Contact our verification team</a>
        and reference your Account ID: <strong>{{ $user->user_code }}</strong>.
    </p>
@endsection
