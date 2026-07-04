<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DocumentApprovedMail;
use App\Mail\DocumentRejectedMail;
use App\Mail\ProviderApprovedMail;
use App\Mail\ProviderRejectedMail;
use App\Models\ProviderDocument;
use App\Models\ProviderProfile;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProviderVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'in_review');

        $profiles = ProviderProfile::with(['user', 'documents'])
            ->when($status !== 'all', fn($q) => $q->where('verification_status', $status))
            ->latest()
            ->paginate(20);

        $counts = [
            'in_review' => ProviderProfile::where('verification_status', 'in_review')->count(),
            'pending'   => ProviderProfile::where('verification_status', 'pending')->count(),
            'approved'  => ProviderProfile::where('verification_status', 'approved')->count(),
            'rejected'  => ProviderProfile::where('verification_status', 'rejected')->count(),
        ];

        return view('admin.providers.index', compact('profiles', 'status', 'counts'));
    }

    public function show(ProviderProfile $provider): View
    {
        $provider->load([
            'user',
            'documents.documentType',
            'serviceAreas.country',
            'serviceAreas.division',
            'serviceAreas.district',
            'serviceAreas.area',
            'services.service.category',
            'services.currency',
            'businessHours.dayOfWeek',
        ]);
        $plans = SubscriptionPlan::where('status', 'active')->orderBy('price')->get();

        return view('admin.providers.show', compact('provider', 'plans'));
    }

    public function approve(Request $request, ProviderProfile $provider): RedirectResponse
    {
        $withSub = $request->input('subscription_type') === 'with';

        if ($withSub) {
            $request->validate(['plan_id' => ['required', 'exists:subscription_plans,id']]);
        }

        // Auto-approve all pending documents
        $provider->documents()
            ->where('verification_status', 'pending')
            ->update(['verification_status' => 'approved', 'rejection_reason' => null]);

        $provider->update([
            'verification_status' => 'approved',
            'is_verified'         => true,
            'status'              => 'active',
        ]);

        $plan = null;
        if ($withSub) {
            $plan    = SubscriptionPlan::findOrFail($request->input('plan_id'));
            $endDate = $plan->duration_months > 0 ? now()->addMonths($plan->duration_months) : null;

            Subscription::updateOrCreate(
                ['provider_id' => $provider->user_id],
                [
                    'plan_id'             => $plan->id,
                    'start_date'          => now(),
                    'end_date'            => $endDate,
                    'subscription_status' => 'active',
                    'payment_status'      => $plan->price > 0 ? 'pending' : 'paid',
                    'auto_renew'          => false,
                ]
            );
        }

        Mail::to($provider->user->email)->queue(new ProviderApprovedMail($provider->user, $plan));

        $suffix = $withSub ? " with {$plan->name} plan" : ' without subscription';
        return redirect()->route('admin.providers.index')
            ->with('success', "\"{$provider->business_name}\" approved{$suffix}.");
    }

    public function reject(Request $request, ProviderProfile $provider): RedirectResponse
    {
        $request->validate(['reason' => ['required', 'string', 'max:1000']]);

        $provider->update(['verification_status' => 'rejected', 'is_verified' => false]);

        $provider->documents()
            ->where('verification_status', 'pending')
            ->update(['verification_status' => 'rejected', 'rejection_reason' => $request->input('reason')]);

        Mail::to($provider->user->email)->queue(new ProviderRejectedMail($provider->user, $request->input('reason')));

        return redirect()->route('admin.providers.index')
            ->with('success', "\"{$provider->business_name}\" rejected.");
    }

    public function approveDocument(ProviderProfile $provider, ProviderDocument $document): RedirectResponse
    {
        abort_unless($document->provider_profile_id === $provider->id, 404);

        $document->update(['verification_status' => 'approved', 'rejection_reason' => null]);

        Mail::to($provider->user->email)->queue(new DocumentApprovedMail($provider->user, $document));

        return back()->with('success', "\"{$document->documentType->name}\" approved.");
    }

    public function rejectDocument(Request $request, ProviderProfile $provider, ProviderDocument $document): RedirectResponse
    {
        abort_unless($document->provider_profile_id === $provider->id, 404);

        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $document->update([
            'verification_status' => 'rejected',
            'rejection_reason'    => $request->input('reason'),
        ]);

        Mail::to($provider->user->email)->queue(new DocumentRejectedMail($provider->user, $document));

        return back()->with('success', "\"{$document->documentType->name}\" rejected — provider notified.");
    }
}
