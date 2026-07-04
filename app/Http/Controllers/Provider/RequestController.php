<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(Request $request): View
    {
        $user  = Auth::user();
        $tab   = $request->get('tab', 'active');

        $query = ServiceRequest::with(['customer', 'service', 'currency', 'invoice'])
            ->where('provider_id', $user->id);

        $statusMap = [
            'pending'   => ['pending'],
            'active'    => ['accepted', 'in_progress'],
            'completed' => ['completed', 'cancelled', 'disputed', 'expired'],
        ];

        $statuses = $statusMap[$tab] ?? $statusMap['active'];
        $query->whereIn('request_status', $statuses);

        $requests = $query->latest()->paginate(20)->appends(['tab' => $tab]);

        $counts = [
            'pending'   => ServiceRequest::where('provider_id', $user->id)->where('request_status', 'pending')->count(),
            'active'    => ServiceRequest::where('provider_id', $user->id)->whereIn('request_status', ['accepted', 'in_progress'])->count(),
            'completed' => ServiceRequest::where('provider_id', $user->id)->whereIn('request_status', ['completed', 'cancelled', 'disputed', 'expired'])->count(),
        ];

        return view('provider.requests.index', compact('requests', 'tab', 'counts'));
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        if ($serviceRequest->provider_id !== Auth::id()) {
            abort(403);
        }

        $serviceRequest->load(['customer', 'service', 'currency', 'attachments', 'statusLogs', 'invoice.currency',
            'teamAssignments' => fn($q) => $q->where('assignment_type', 'primary')->with('member')->latest()->limit(1)]);

        return view('provider.requests.show', compact('serviceRequest'));
    }

    public function updateStatus(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        if ($serviceRequest->provider_id !== Auth::id()) {
            abort(403);
        }

        $allowed = match ($serviceRequest->request_status) {
            'pending'     => ['accepted', 'cancelled'],
            'accepted'    => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            default       => [],
        };

        $data = $request->validate([
            'status'               => ['required', 'in:' . implode(',', $allowed ?: ['none'])],
            'cancellation_reason'  => ['nullable', 'string', 'max:500'],
        ]);

        $update = ['request_status' => $data['status']];

        if ($data['status'] === 'completed') {
            $update['completed_at'] = now();
        }

        if ($data['status'] === 'cancelled' && !empty($data['cancellation_reason'])) {
            $update['cancellation_reason'] = $data['cancellation_reason'];
        }

        $serviceRequest->update($update);

        return back()->with('success', 'Request status updated.');
    }
}
