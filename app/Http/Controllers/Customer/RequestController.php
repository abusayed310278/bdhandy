<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\RequestStatusLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab  = $request->get('tab', 'active');

        $query = $user->serviceRequests()->with(['service.category', 'provider.providerProfile']);

        $requests = match($tab) {
            'completed'  => $query->whereIn('request_status', ['completed'])->latest()->paginate(10),
            'cancelled'  => $query->whereIn('request_status', ['cancelled', 'expired'])->latest()->paginate(10),
            default      => $query->whereIn('request_status', ['pending', 'accepted', 'in_progress', 'disputed'])->latest()->paginate(10),
        };

        return view('customer.requests.index', compact('requests', 'tab'));
    }

    public function show(ServiceRequest $request): View|RedirectResponse
    {
        if ($request->customer_id !== Auth::id()) {
            abort(403);
        }

        $request->load(['service.category', 'provider.providerProfile', 'statusLogs.changedBy', 'attachments', 'currency', 'invoice.currency']);
        $review = \App\Models\Review::where('service_request_id', $request->id)->where('customer_id', Auth::id())->first();

        return view('customer.requests.show', compact('request', 'review'));
    }

    public function cancel(ServiceRequest $request): RedirectResponse
    {
        if ($request->customer_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($request->request_status, ['pending'])) {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $old = $request->request_status;
        $request->update(['request_status' => 'cancelled']);

        RequestStatusLog::create([
            'service_request_id' => $request->id,
            'old_status'         => $old,
            'new_status'         => 'cancelled',
            'changed_by'         => Auth::id(),
            'notes'              => 'Cancelled by customer',
        ]);

        return redirect()->route('customer.requests.index')->with('success', 'Request cancelled.');
    }
}
