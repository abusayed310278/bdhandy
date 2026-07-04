<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\SavedProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SavedProviderController extends Controller
{
    public function index(): View
    {
        $saved = Auth::user()->savedProviders()
            ->with(['provider.providerProfile.services.service'])
            ->latest()
            ->paginate(12);

        return view('customer.saved.index', compact('saved'));
    }

    public function toggle(ProviderProfile $provider)
    {
        $user = Auth::user();
        $existing = SavedProvider::where('customer_id', $user->id)
            ->where('provider_id', $provider->user_id)
            ->first();

        if ($existing) {
            $existing->delete();
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'saved' => false,
                    'message' => 'Provider removed from saved.'
                ]);
            }
            return back()->with('success', 'Provider removed from saved.');
        }

        SavedProvider::create(['customer_id' => $user->id, 'provider_id' => $provider->user_id]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'saved' => true,
                'message' => 'Provider saved!'
            ]);
        }
        return back()->with('success', 'Provider saved!');
    }
}
