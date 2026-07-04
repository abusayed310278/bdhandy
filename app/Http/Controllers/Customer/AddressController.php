<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\District;
use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = Auth::user()->customerAddresses()->with(['country', 'division', 'district', 'area'])->latest()->get();

        return view('customer.addresses.index', compact('addresses'));
    }

    public function create(): View
    {
        $countries = Country::orderBy('name')->get();
        return view('customer.addresses.form', ['address' => null, 'countries' => $countries, 'divisions' => collect(), 'districts' => collect(), 'areas' => collect()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'label'       => ['required', 'string', 'max:100'],
            'address_type'=> ['required', 'in:house,office,business'],
            'address'     => ['required', 'string', 'max:500'],
            'country_id'  => ['required', 'exists:countries,id'],
            'division_id' => ['required', 'exists:divisions,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'area_id'     => ['required', 'exists:areas,id'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
        ]);

        $user = Auth::user();
        $isFirst = $user->customerAddresses()->doesntExist();

        $user->customerAddresses()->create([
            'label'        => $request->label,
            'address_type' => $request->address_type,
            'address'      => $request->address,
            'country_id'   => $request->country_id,
            'division_id'  => $request->division_id,
            'district_id'  => $request->district_id,
            'area_id'      => $request->area_id,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'is_primary'   => $isFirst,
        ]);

        return redirect()->route('customer.addresses.index')->with('success', 'Address added.');
    }

    public function edit(CustomerAddress $address): View|RedirectResponse
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $countries = Country::orderBy('name')->get();
        $divisions = Division::where('country_id', $address->country_id)->orderBy('name')->get();
        $districts = District::where('division_id', $address->division_id)->orderBy('name')->get();
        $areas     = Area::where('district_id', $address->district_id)->orderBy('name')->get();

        return view('customer.addresses.form', compact('address', 'countries', 'divisions', 'districts', 'areas'));
    }

    public function update(Request $request, CustomerAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $request->validate([
            'label'       => ['required', 'string', 'max:100'],
            'address_type'=> ['required', 'in:house,office,business'],
            'address'     => ['required', 'string', 'max:500'],
            'country_id'  => ['required', 'exists:countries,id'],
            'division_id' => ['required', 'exists:divisions,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'area_id'     => ['required', 'exists:areas,id'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
        ]);

        $address->update($request->only(['label', 'address_type', 'address', 'country_id', 'division_id', 'district_id', 'area_id', 'latitude', 'longitude']));

        return redirect()->route('customer.addresses.index')->with('success', 'Address updated.');
    }

    public function destroy(CustomerAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $wasPrimary = $address->is_primary;
        $address->delete();

        if ($wasPrimary) {
            $first = Auth::user()->customerAddresses()->first();
            $first?->update(['is_primary' => true]);
        }

        return redirect()->route('customer.addresses.index')->with('success', 'Address removed.');
    }

    public function setPrimary(CustomerAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) abort(403);

        Auth::user()->customerAddresses()->update(['is_primary' => false]);
        $address->update(['is_primary' => true]);

        return redirect()->route('customer.addresses.index')->with('success', 'Primary address updated.');
    }
}
