<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = CustomerAddress::where('user_id', Auth::id())->get();
        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_type' => 'nullable|string',
            'label' => 'nullable|string',
            'address' => 'required|string',
            'is_primary' => 'boolean',
        ]);

        if ($request->is_primary) {
            CustomerAddress::where('user_id', Auth::id())->update(['is_primary' => false]);
        }

        $address = CustomerAddress::create([
            'user_id' => Auth::id(),
            'address_type' => $request->address_type ?? 'home',
            'label' => $request->label,
            'address' => $request->address,
            'is_primary' => $request->is_primary ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.',
            'data' => $address
        ]);
    }

    public function destroy($id)
    {
        $address = CustomerAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $address->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.'
        ]);
    }
}
