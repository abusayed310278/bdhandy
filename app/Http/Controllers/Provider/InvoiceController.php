<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function create(ServiceRequest $serviceRequest): View|RedirectResponse
    {
        if ($serviceRequest->provider_id !== Auth::id()) {
            abort(403);
        }

        if ($serviceRequest->request_status !== 'completed') {
            return back()->with('error', 'Invoices can only be created for completed requests.');
        }

        if ($serviceRequest->invoice) {
            return redirect()->route('provider.invoices.show', $serviceRequest->invoice);
        }

        $currencies = Currency::where('status', 'active')->get();

        return view('provider.invoices.create', compact('serviceRequest', 'currencies'));
    }

    public function store(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        if ($serviceRequest->provider_id !== Auth::id()) {
            abort(403);
        }

        if ($serviceRequest->request_status !== 'completed') {
            return back()->with('error', 'Invoices can only be created for completed requests.');
        }

        if ($serviceRequest->invoice) {
            return redirect()->route('provider.invoices.show', $serviceRequest->invoice);
        }

        $data = $request->validate([
            'currency_id'        => ['required', 'exists:currencies,id'],
            'subtotal'           => ['required', 'numeric', 'min:0'],
            'discount_type'      => ['required', 'in:none,fixed,percent'],
            'discount_value'     => ['nullable', 'numeric', 'min:0'],
            'tax_label'          => ['nullable', 'string', 'max:30'],
            'tax_rate'           => ['nullable', 'numeric', 'min:0', 'max:100'],
            'adjustment_amount'  => ['nullable', 'numeric'],
            'adjustment_note'    => ['nullable', 'string', 'max:255'],
            'payment_status'     => ['required', 'in:draft,pending,paid,partial,due'],
            'payment_method'     => ['nullable', 'in:cash,card,online,cheque,bank_transfer,other'],
            'payment_reference'  => ['nullable', 'string', 'max:255'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'due_date'           => ['nullable', 'date'],
        ]);

        $computed = ServiceRequestInvoice::computeTotals(
            (float) $data['subtotal'],
            $data['discount_type'],
            isset($data['discount_value']) ? (float) $data['discount_value'] : null,
            isset($data['tax_rate']) ? (float) $data['tax_rate'] : null,
            isset($data['adjustment_amount']) ? (float) $data['adjustment_amount'] : null
        );

        $invoice = ServiceRequestInvoice::create([
            'invoice_number'     => ServiceRequestInvoice::generateNumber(),
            'service_request_id' => $serviceRequest->id,
            'provider_id'        => $serviceRequest->provider_id,
            'customer_id'        => $serviceRequest->customer_id,
            'currency_id'        => $data['currency_id'],
            'subtotal'           => $data['subtotal'],
            'discount_type'      => $data['discount_type'],
            'discount_value'     => $data['discount_value'] ?? null,
            'discount_amount'    => $computed['discount_amount'],
            'tax_label'          => $data['tax_label'] ?? null,
            'tax_rate'           => $data['tax_rate'] ?? null,
            'tax_amount'         => $computed['tax_amount'],
            'adjustment_amount'  => $data['adjustment_amount'] ?? null,
            'adjustment_note'    => $data['adjustment_note'] ?? null,
            'total'              => $computed['total'],
            'payment_status'     => $data['payment_status'],
            'payment_method'     => $data['payment_method'] ?? null,
            'payment_reference'  => $data['payment_reference'] ?? null,
            'notes'              => $data['notes'] ?? null,
            'due_date'           => $data['due_date'] ?? null,
            'paid_at'            => $data['payment_status'] === 'paid' ? now() : null,
            'issued_at'          => now(),
        ]);

        return redirect()->route('provider.invoices.show', $invoice)
            ->with('success', 'Invoice ' . $invoice->invoice_number . ' created successfully.');
    }

    public function show(ServiceRequestInvoice $invoice): View
    {
        if ($invoice->provider_id !== Auth::id()) {
            abort(403);
        }

        $invoice->load(['serviceRequest', 'customer', 'currency']);

        return view('provider.invoices.show', compact('invoice'));
    }

    public function edit(ServiceRequestInvoice $invoice): View|RedirectResponse
    {
        if ($invoice->provider_id !== Auth::id()) {
            abort(403);
        }

        if (!$invoice->isEditable()) {
            return back()->with('error', 'Paid invoices cannot be edited.');
        }

        $invoice->load(['serviceRequest', 'currency']);
        $currencies = Currency::where('status', 'active')->get();

        return view('provider.invoices.edit', compact('invoice', 'currencies'));
    }

    public function update(Request $request, ServiceRequestInvoice $invoice): RedirectResponse
    {
        if ($invoice->provider_id !== Auth::id()) {
            abort(403);
        }

        if (!$invoice->isEditable()) {
            return back()->with('error', 'Paid invoices cannot be edited.');
        }

        $data = $request->validate([
            'currency_id'        => ['required', 'exists:currencies,id'],
            'subtotal'           => ['required', 'numeric', 'min:0'],
            'discount_type'      => ['required', 'in:none,fixed,percent'],
            'discount_value'     => ['nullable', 'numeric', 'min:0'],
            'tax_label'          => ['nullable', 'string', 'max:30'],
            'tax_rate'           => ['nullable', 'numeric', 'min:0', 'max:100'],
            'adjustment_amount'  => ['nullable', 'numeric'],
            'adjustment_note'    => ['nullable', 'string', 'max:255'],
            'payment_status'     => ['required', 'in:draft,pending,paid,partial,due'],
            'payment_method'     => ['nullable', 'in:cash,card,online,cheque,bank_transfer,other'],
            'payment_reference'  => ['nullable', 'string', 'max:255'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'due_date'           => ['nullable', 'date'],
        ]);

        $computed = ServiceRequestInvoice::computeTotals(
            (float) $data['subtotal'],
            $data['discount_type'],
            isset($data['discount_value']) ? (float) $data['discount_value'] : null,
            isset($data['tax_rate']) ? (float) $data['tax_rate'] : null,
            isset($data['adjustment_amount']) ? (float) $data['adjustment_amount'] : null
        );

        $paidAt = $invoice->paid_at;
        if ($data['payment_status'] === 'paid' && !$paidAt) {
            $paidAt = now();
        } elseif ($data['payment_status'] !== 'paid') {
            $paidAt = null;
        }

        $invoice->update([
            'currency_id'       => $data['currency_id'],
            'subtotal'          => $data['subtotal'],
            'discount_type'     => $data['discount_type'],
            'discount_value'    => $data['discount_value'] ?? null,
            'discount_amount'   => $computed['discount_amount'],
            'tax_label'         => $data['tax_label'] ?? null,
            'tax_rate'          => $data['tax_rate'] ?? null,
            'tax_amount'        => $computed['tax_amount'],
            'adjustment_amount' => $data['adjustment_amount'] ?? null,
            'adjustment_note'   => $data['adjustment_note'] ?? null,
            'total'             => $computed['total'],
            'payment_status'    => $data['payment_status'],
            'payment_method'    => $data['payment_method'] ?? null,
            'payment_reference' => $data['payment_reference'] ?? null,
            'notes'             => $data['notes'] ?? null,
            'due_date'          => $data['due_date'] ?? null,
            'paid_at'           => $paidAt,
        ]);

        return redirect()->route('provider.invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }
}
