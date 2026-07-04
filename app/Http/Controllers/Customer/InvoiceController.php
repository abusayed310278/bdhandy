<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequestInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function show(ServiceRequestInvoice $invoice): View
    {
        if ($invoice->customer_id !== Auth::id()) {
            abort(403);
        }

        $invoice->load(['serviceRequest', 'provider.providerProfile', 'currency']);

        return view('customer.invoices.show', compact('invoice'));
    }
}
