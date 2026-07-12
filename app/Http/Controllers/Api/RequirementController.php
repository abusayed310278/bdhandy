<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomerRequirement;
use Illuminate\Support\Facades\Auth;

class RequirementController extends Controller
{
    public function index()
    {
        $requirements = CustomerRequirement::with(['category', 'service', 'proposals'])
            ->where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $requirements
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'budget_type' => 'required|string',
        ]);

        $requirement = CustomerRequirement::create([
            'customer_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'service_id' => $request->service_id,
            'budget_type' => $request->budget_type,
            'budget_fixed' => $request->budget_fixed,
            'budget_min' => $request->budget_min,
            'budget_max' => $request->budget_max,
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Requirement posted successfully.',
            'data' => $requirement
        ]);
    }
}
