<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\CustomerRequirement;
use App\Models\RequirementProposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeadsController extends Controller
{
    public function index(): View
    {
        $user    = Auth::user();
        $profile = $user->providerProfile;

        // Get service IDs that this provider offers
        $serviceIds = $profile->services()->pluck('service_id');

        $leads = CustomerRequirement::with(['customer', 'service', 'category', 'currency'])
            ->where('status', 'open')
            ->when($serviceIds->isNotEmpty(), fn($q) => $q->whereIn('service_id', $serviceIds))
            ->latest()
            ->paginate(20);

        // Track which leads this provider has already proposed on
        $myProposalRequirementIds = RequirementProposal::where('provider_id', $user->id)
            ->pluck('requirement_id')
            ->toArray();

        return view('provider.leads.index', compact('leads', 'myProposalRequirementIds'));
    }

    public function show(CustomerRequirement $requirement): View
    {
        if ($requirement->status !== 'open') {
            abort(404);
        }

        $requirement->load(['customer', 'service', 'category', 'currency', 'attachments']);

        $user           = Auth::user();
        $myProposal     = RequirementProposal::where('requirement_id', $requirement->id)
            ->where('provider_id', $user->id)
            ->first();
        $proposalsCount = $requirement->proposals()->count();
        $currencies     = Currency::orderBy('name')->get();

        return view('provider.leads.show', compact('requirement', 'myProposal', 'proposalsCount', 'currencies'));
    }

    public function propose(Request $request, CustomerRequirement $requirement): RedirectResponse
    {
        if ($requirement->status !== 'open') {
            return back()->withErrors(['error' => 'This lead is no longer open.']);
        }

        $user = Auth::user();
        
        // Check subscription limits
        if (!$user->canProposeLead()) {
            return back()->withErrors(['error' => 'You have reached your monthly lead limit. Please upgrade your plan to propose more leads.']);
        }

        // Check if already proposed
        if (RequirementProposal::where('requirement_id', $requirement->id)->where('provider_id', $user->id)->exists()) {
            return back()->withErrors(['error' => 'You have already submitted a proposal for this lead.']);
        }

        $data = $request->validate([
            'message'                => ['required', 'string', 'max:2000'],
            'proposed_price'         => ['nullable', 'numeric', 'min:0'],
            'currency_id'            => ['nullable', 'exists:currencies,id'],
            'estimated_arrival_time' => ['nullable', 'string', 'max:255'],
        ]);

        RequirementProposal::create([
            'requirement_id'          => $requirement->id,
            'provider_id'             => $user->id,
            'message'                 => $data['message'],
            'proposed_price'          => $data['proposed_price'] ?? null,
            'currency_id'             => $data['currency_id'] ?? null,
            'estimated_arrival_time'  => $data['estimated_arrival_time'] ?? null,
            'status'                  => 'pending',
        ]);

        return back()->with('success', 'Proposal submitted successfully.');
    }
}
