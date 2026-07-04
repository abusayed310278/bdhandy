<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Currency;
use App\Models\CustomerRequirement;
use App\Models\RequirementAttachment;
use App\Models\RequirementProposal;
use App\Models\ServiceRequest;
use App\Models\RequestStatusLog;
use App\Models\RequestAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RequirementController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab  = $request->get('tab', 'open');

        $query = $user->customerRequirements()->with(['category', 'service', 'currency', 'proposals']);

        $requirements = match($tab) {
            'assigned'  => $query->whereIn('status', ['assigned'])->latest()->paginate(10),
            'completed' => $query->whereIn('status', ['completed'])->latest()->paginate(10),
            'closed'    => $query->whereIn('status', ['expired', 'cancelled'])->latest()->paginate(10),
            default     => $query->where('status', 'open')->latest()->paginate(10),
        };

        return view('customer.requirements.index', compact('requirements', 'tab'));
    }

    public function create(): View
    {
        $categories = Category::with(['services' => fn($q) => $q->where('status', 'active')])
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        $currencies = Currency::where('status', 'active')->get();

        $categoriesForJs = $categories->map(fn($c) => [
            'id'       => $c->id,
            'name'     => $c->getTranslation('translations', 'en') ?: $c->slug,
            'services' => $c->services->map(fn($s) => [
                'id'   => $s->id,
                'name' => ($s->getTranslation('translations', 'en')['name'] ?? null) ?: $s->slug,
            ])->values(),
        ])->values();

        $addresses = Auth::user()->customerAddresses;

        return view('customer.requirements.create', compact('categories', 'currencies', 'categoriesForJs', 'addresses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['required', 'string', 'max:2000'],
            'category_id'         => ['required', 'exists:categories,id'],
            'service_id'          => ['required', 'exists:services,id'],
            'budget_type'         => ['required', 'in:fixed,range,negotiable'],
            'budget_fixed'        => ['nullable', 'numeric', 'min:0'],
            'budget_min'          => ['nullable', 'numeric', 'min:0'],
            'budget_max'          => ['nullable', 'numeric', 'min:0'],
            'currency_id'         => ['required', 'exists:currencies,id'],
            'urgency'             => ['required', 'in:normal,urgent,emergency'],
            'preferred_date'      => ['nullable', 'date', 'after_or_equal:today'],
            'address'             => ['required', 'string', 'max:500'],
            'latitude'            => ['nullable', 'numeric'],
            'longitude'           => ['nullable', 'numeric'],
            'visibility_radius_km'=> ['nullable', 'numeric', 'min:1', 'max:50'],
            'attachments.*'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $req = CustomerRequirement::create([
            'customer_id'          => $user->id,
            'category_id'          => $request->category_id,
            'service_id'           => $request->service_id,
            'title'                => $request->title,
            'description'          => $request->description,
            'budget_type'          => $request->budget_type,
            'budget_fixed'         => $request->budget_fixed,
            'budget_min'           => $request->budget_min,
            'budget_max'           => $request->budget_max,
            'currency_id'          => $request->currency_id,
            'urgency'              => $request->urgency,
            'preferred_date'       => $request->preferred_date,
            'address'              => $request->address,
            'latitude'             => $request->latitude,
            'longitude'            => $request->longitude,
            'visibility_radius_km' => $request->visibility_radius_km ?? 10,
            'expiry_at'            => now()->addHours(24),
            'status'               => 'open',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('requirement-attachments', 'public');
                RequirementAttachment::create([
                    'requirement_id' => $req->id,
                    'file'           => $path,
                    'file_type'      => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'pdf',
                ]);
            }
        }

        return redirect()->route('customer.requirements.show', $req)->with('success', 'Requirement posted successfully!');
    }

    public function show(CustomerRequirement $requirement): View|RedirectResponse
    {
        if ($requirement->customer_id !== Auth::id()) {
            abort(403);
        }

        $requirement->load(['category', 'service', 'currency', 'attachments', 'proposals.provider.providerProfile', 'proposals.currency']);

        return view('customer.requirements.show', compact('requirement'));
    }

    public function cancel(CustomerRequirement $requirement): RedirectResponse
    {
        if ($requirement->customer_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($requirement->status, ['open'])) {
            return back()->with('error', 'Only open requirements can be cancelled.');
        }

        $requirement->update(['status' => 'cancelled']);

        return redirect()->route('customer.requirements.index')->with('success', 'Requirement cancelled.');
    }

    public function acceptProposal(CustomerRequirement $requirement, RequirementProposal $proposal): RedirectResponse
    {
        if ($requirement->customer_id !== Auth::id()) abort(403);
        if ($proposal->requirement_id !== $requirement->id) abort(404);
        if ($requirement->status !== 'open') return back()->with('error', 'Requirement is no longer open.');

        $serviceRequest = DB::transaction(function () use ($requirement, $proposal) {
            $proposal->update(['status' => 'accepted']);
            $requirement->update(['status' => 'assigned']);

            // Reject all other proposals
            $requirement->proposals()->where('id', '!=', $proposal->id)->update(['status' => 'rejected']);

            // 1. Resolve the appropriate service_id
            $serviceId = $requirement->service_id;
            if (!$serviceId) {
                // Try to find a service in the same category that the provider offers
                $providerProfile = $proposal->provider?->providerProfile;
                if ($providerProfile) {
                    $providerService = $providerProfile->services()
                        ->whereHas('service', function ($q) use ($requirement) {
                            $q->where('category_id', $requirement->category_id);
                        })
                        ->first();
                    if ($providerService) {
                        $serviceId = $providerService->service_id;
                    }
                }

                // Fallback to any active service in the requirement's category
                if (!$serviceId) {
                    $fallbackService = \App\Models\Service::where('category_id', $requirement->category_id)
                        ->where('status', 'active')
                        ->first() ?? \App\Models\Service::where('category_id', $requirement->category_id)->first();
                    if ($fallbackService) {
                        $serviceId = $fallbackService->id;
                    }
                }

                // Absolute fallback to any active service
                if (!$serviceId) {
                    $fallbackService = \App\Models\Service::where('status', 'active')->first() 
                        ?? \App\Models\Service::first();
                    $serviceId = $fallbackService?->id;
                }
            }

            // 2. Generate a unique request number
            $number = 'REQ-' . now()->format('Y') . '-' . str_pad(
                ServiceRequest::withTrashed()->count() + 1,
                6, '0', STR_PAD_LEFT
            );

            // Resolve provider_service_id: find matching ProviderService for this provider + service
            $providerServiceId = null;
            if ($serviceId) {
                $providerProfile = $proposal->provider?->providerProfile;
                if ($providerProfile) {
                    $providerServiceId = $providerProfile->services()
                        ->where('service_id', $serviceId)
                        ->value('id');
                }
            }

            // 3. Create the ServiceRequest
            $sr = ServiceRequest::create([
                'customer_id'         => $requirement->customer_id,
                'provider_id'         => $proposal->provider_id,
                'service_id'          => $serviceId,
                'provider_service_id' => $providerServiceId,
                'request_number'      => $number,
                'title'               => $requirement->title,
                'description'         => $requirement->description,
                'preferred_date'      => $requirement->preferred_date,
                'address'             => $requirement->address,
                'latitude'            => $requirement->latitude,
                'longitude'           => $requirement->longitude,
                'urgency'             => $requirement->urgency,
                'estimated_price'     => $proposal->proposed_price,
                'final_price'         => $proposal->proposed_price,
                'currency_id'         => $proposal->currency_id,
                'payment_status'      => 'pending',
                'request_status'      => 'accepted',
            ]);

            // 4. Create the RequestStatusLog
            RequestStatusLog::create([
                'service_request_id' => $sr->id,
                'old_status'         => null,
                'new_status'         => 'accepted',
                'changed_by'         => Auth::id(),
                'notes'              => 'Service Request automatically created upon proposal acceptance',
            ]);

            // 5. Copy attachments from CustomerRequirement to ServiceRequest
            foreach ($requirement->attachments as $attachment) {
                RequestAttachment::create([
                    'service_request_id' => $sr->id,
                    'file'               => $attachment->file,
                    'file_type'          => $attachment->file_type,
                ]);
            }

            return $sr;
        });

        return redirect()->route('customer.requests.show', $serviceRequest)
            ->with('success', 'Proposal accepted! A service request has been automatically created.');
    }

    public function rejectProposal(CustomerRequirement $requirement, RequirementProposal $proposal): RedirectResponse
    {
        if ($requirement->customer_id !== Auth::id()) abort(403);
        if ($proposal->requirement_id !== $requirement->id) abort(404);

        $proposal->update(['status' => 'rejected']);

        return back()->with('success', 'Proposal declined.');
    }
}
