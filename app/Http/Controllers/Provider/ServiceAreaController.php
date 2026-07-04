<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Country;
use App\Models\District;
use App\Models\Division;
use App\Models\ProviderServiceArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceAreaController extends Controller
{
    private function profile()
    {
        return Auth::user()->providerProfile;
    }

    public function index(): View
    {
        $profile = $this->profile();
        $areas   = ProviderServiceArea::with(['country', 'division', 'district', 'area'])
            ->where('provider_profile_id', $profile->id)
            ->get();

        $user  = Auth::user();
        $plan  = $user->subscription_plan;
        $limit = $plan?->service_area_limit ?? 0;

        return view('provider.areas.index', compact('areas', 'limit'));
    }

    public function create(): View|RedirectResponse
    {
        if (!Auth::user()->canAddServiceArea()) {
            return redirect()->route('provider.areas.index')->with('error', 'Service area limit reached for your plan.');
        }

        $countries   = Country::orderBy('name')->get();
        $singleCountry = $countries->count() === 1 ? $countries->first() : null;
        $divisions   = $singleCountry
            ? Division::where('country_id', $singleCountry->id)->orderBy('name')->get()
            : collect();
        $districts   = collect();
        $areas       = collect();
        $area        = null;

        return view('provider.areas.form', compact('countries', 'singleCountry', 'divisions', 'districts', 'areas', 'area'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->canAddServiceArea()) {
            return redirect()->route('provider.areas.index')->with('error', 'Service area limit reached for your plan.');
        }

        $data = $request->validate([
            'country_id'  => ['required', 'exists:countries,id'],
            'division_id' => ['required', 'exists:divisions,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'area_id'     => ['nullable', 'exists:areas,id'],
            'address'     => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
            'radius_km'   => ['nullable', 'numeric', 'min:1', 'max:200'],
        ]);

        $data['provider_profile_id'] = $this->profile()->id;
        ProviderServiceArea::create($data);

        return redirect()->route('provider.areas.index')->with('success', 'Service area added.');
    }

    public function edit(ProviderServiceArea $area): View
    {
        $this->authorizeArea($area);

        $countries     = Country::orderBy('name')->get();
        $singleCountry = $countries->count() === 1 ? $countries->first() : null;
        $divisions     = Division::where('country_id', $area->country_id)->orderBy('name')->get();
        $districts     = District::where('division_id', $area->division_id)->orderBy('name')->get();
        $areas         = Area::where('district_id', $area->district_id)->orderBy('name')->get();

        return view('provider.areas.form', compact('area', 'countries', 'singleCountry', 'divisions', 'districts', 'areas'));
    }

    public function update(Request $request, ProviderServiceArea $area): RedirectResponse
    {
        $this->authorizeArea($area);

        $data = $request->validate([
            'country_id'  => ['required', 'exists:countries,id'],
            'division_id' => ['required', 'exists:divisions,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'area_id'     => ['nullable', 'exists:areas,id'],
            'address'     => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
            'radius_km'   => ['nullable', 'numeric', 'min:1', 'max:200'],
        ]);

        $area->update($data);

        return redirect()->route('provider.areas.index')->with('success', 'Service area updated.');
    }

    public function destroy(ProviderServiceArea $area): RedirectResponse
    {
        $this->authorizeArea($area);
        $area->delete();

        return redirect()->route('provider.areas.index')->with('success', 'Service area removed.');
    }

    private function authorizeArea(ProviderServiceArea $area): void
    {
        if ($area->provider_profile_id !== $this->profile()->id) {
            abort(403);
        }
    }
}
