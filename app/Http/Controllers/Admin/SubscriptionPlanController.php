<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubscriptionPlanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view subscription plans', only: ['index']),
            new Middleware('permission:create subscription plans', only: ['create', 'store']),
            new Middleware('permission:edit subscription plans', only: ['edit', 'update']),
            new Middleware('permission:delete subscription plans', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = SubscriptionPlan::with('currency');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $plans = $query->orderBy('price', 'asc')->paginate(10)->withQueryString();

        return view('admin.subscription_plans.index', compact('plans'));
    }

    public function create()
    {
        $currencies = Currency::where('status', 'active')->get();
        return view('admin.subscription_plans.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration_months' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'lead_limit' => 'nullable|integer|min:0',
            'service_area_limit' => 'nullable|integer|min:0',
            'gallery_limit' => 'nullable|integer|min:0',
            'search_rank_weight' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'target' => 'required|in:provider,business,both',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured');
        $data['is_verified_badge_included'] = $request->has('is_verified_badge_included');

        SubscriptionPlan::create($data);

        return redirect()->route('admin.subscription_plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        $currencies = Currency::where('status', 'active')->get();
        return view('admin.subscription_plans.edit', compact('subscriptionPlan', 'currencies'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration_months' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'lead_limit' => 'nullable|integer|min:0',
            'service_area_limit' => 'nullable|integer|min:0',
            'gallery_limit' => 'nullable|integer|min:0',
            'search_rank_weight' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'target' => 'required|in:provider,business,both',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured');
        $data['is_verified_badge_included'] = $request->has('is_verified_badge_included');

        $subscriptionPlan->update($data);

        return redirect()->route('admin.subscription_plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();
        return redirect()->route('admin.subscription_plans.index')->with('success', 'Plan deleted successfully.');
    }
}
