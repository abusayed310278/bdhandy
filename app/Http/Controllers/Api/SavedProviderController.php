<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SavedProvider;
use Illuminate\Support\Facades\Auth;

class SavedProviderController extends Controller
{
    public function index()
    {
        // We assume SavedProvider has a 'provider' relationship
        $saved = SavedProvider::with('provider')->where('user_id', Auth::id())->get();
        return response()->json([
            'success' => true,
            'data' => $saved
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|integer'
        ]);

        $existing = SavedProvider::where('user_id', Auth::id())
            ->where('provider_id', $request->provider_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['success' => true, 'message' => 'Provider removed from saved.']);
        } else {
            SavedProvider::create([
                'user_id' => Auth::id(),
                'provider_id' => $request->provider_id,
            ]);
            return response()->json(['success' => true, 'message' => 'Provider saved.']);
        }
    }
}
