@extends('emails.layout')

@section('content')
    <p class="greeting">Congratulations, {{ $user->name }}!</p>

    <p class="text">
        Great news — your profile and documents have been <strong style="color: #16A34A;">verified and approved</strong>. You're now an active provider on {{ config('app.name') }}!
    </p>

    <div class="success-box">
        <strong style="color: #16A34A;">Your profile is now live!</strong>
        @if($plan)
        <p style="margin-top: 10px; font-size: 14px; color: #334155;">
            You have been assigned the <strong>{{ $plan->name }}</strong> plan.
            @if($plan->price > 0)
                Your plan is valid for {{ $plan->duration_months }} month(s).
            @else
                This is a free plan — upgrade anytime for more leads and visibility.
            @endif
        </p>
        @endif
        <ul style="margin-top: 10px; padding-left: 20px; font-size: 14px; color: #334155; line-height: 1.8;">
            <li>Customers can find and contact you</li>
            <li>You can respond to customer requirement posts</li>
            <li>Your verified badge is now visible on your profile</li>
            @if(!$plan || $plan->price == 0)
            <li>Consider upgrading for priority ranking and unlimited leads</li>
            @endif
        </ul>
    </div>

    <p class="text">
        To get the most out of {{ config('app.name') }}:
    </p>
    <ul style="padding-left: 20px; font-size: 14px; color: #334155; margin-bottom: 16px; line-height: 1.9;">
        <li>Add your service areas to appear in nearby searches</li>
        <li>Upload portfolio photos to your gallery</li>
        <li>Set your business hours so customers know when you're available</li>
        <li>Upgrade your plan for priority ranking and unlimited leads</li>
    </ul>

    <div style="text-align: center;">
        <a href="{{ config('app.url') }}/provider/dashboard" class="btn">Go to Your Dashboard</a>
    </div>

    <hr class="divider">
    <p class="note">Account ID: <strong>{{ $user->user_code }}</strong></p>
@endsection
