@extends('emails.layout')

@section('content')
    <p class="greeting">Great news, {{ $user->name }}!</p>

    <p class="text">
        Your document has been reviewed and approved by our verification team.
    </p>

    <div class="success-box">
        <strong style="color: #16A34A;">✓ Approved Document</strong>
        <p style="margin-top: 8px; font-size: 14px; color: #334155;">
            <strong>{{ $document->documentType->name ?? 'Document' }}</strong>
            @if($document->document_number)
                &nbsp;·&nbsp; No: {{ $document->document_number }}
            @endif
        </p>
    </div>

    <p class="text">
        If all your required documents are approved, you can continue using the platform normally.
        If any documents are still pending, you'll be notified once the review is complete.
    </p>

    <div style="text-align: center;">
        <a href="{{ route('provider.onboarding.documents') }}" class="btn">View My Documents</a>
    </div>

    <hr class="divider">
    <p class="note">
        Questions? <a href="{{ config('app.url') }}/contact" style="color: #0F94EA;">Contact our support team</a>
        and reference your Account ID: <strong>{{ $user->user_code }}</strong>.
    </p>
@endsection
