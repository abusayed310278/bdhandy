<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function profile(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->onboarding_profile_done) {
            return $user->customerAddresses()->exists()
                ? redirect()->route('customer.dashboard')
                : redirect()->route('customer.onboarding.address');
        }

        return view('customer.onboarding.profile', ['user' => $user]);
    }

    public function profileStore(Request $request): RedirectResponse
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'bio'           => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender'        => ['nullable', 'in:male,female,other'],
            'photo'         => ['nullable', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        $data = array_merge(
            $request->only(['name', 'bio', 'date_of_birth', 'gender']),
            ['onboarding_profile_done' => true],
        );

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('customer.onboarding.address')
            ->with('status', 'Profile saved! Now add your primary address.');
    }

    public function address(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->onboarding_profile_done) {
            return redirect()->route('customer.onboarding.profile');
        }

        if ($user->customerAddresses()->exists()) {
            return redirect()->route('customer.dashboard');
        }

        $countries   = Country::where('status', 'active')->orderBy('name')->get();
        $autoCountry = $countries->count() === 1 ? $countries->first() : null;
        $divisions   = $autoCountry
            ? Division::where('country_id', $autoCountry->id)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('customer.onboarding.address', compact('countries', 'divisions', 'autoCountry'));
    }

    public function addressStore(Request $request): RedirectResponse
    {
        $request->validate([
            'addresses'                  => ['required', 'array', 'min:1'],
            'addresses.*.label'          => ['required', 'string', 'max:100'],
            'addresses.*.address_type'   => ['required', 'in:house,office,business,other'],
            'addresses.*.country_id'     => ['required', 'exists:countries,id'],
            'addresses.*.division_id'    => ['required', 'exists:divisions,id'],
            'addresses.*.district_id'    => ['required', 'exists:districts,id'],
            'addresses.*.area_id'        => ['required', 'exists:areas,id'],
            'addresses.*.street'         => ['required', 'string', 'max:500'],
            'addresses.*.latitude'       => ['nullable', 'numeric'],
            'addresses.*.longitude'      => ['nullable', 'numeric'],
            'addresses.*.is_primary'     => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();
        $user->customerAddresses()->update(['is_primary' => false]);

        $primarySet = false;

        foreach ($request->input('addresses') as $data) {
            $isPrimary = !$primarySet && !empty($data['is_primary']);
            if ($isPrimary) {
                $primarySet = true;
            }

            $user->customerAddresses()->create([
                'label'        => $data['label'],
                'address_type' => $data['address_type'],
                'country_id'   => $data['country_id'],
                'division_id'  => $data['division_id'],
                'district_id'  => $data['district_id'],
                'area_id'      => $data['area_id'],
                'address'      => $data['street'],
                'latitude'     => $data['latitude'] ?? null,
                'longitude'    => $data['longitude'] ?? null,
                'is_primary'   => $isPrimary,
            ]);
        }

        // Guarantee at least one primary
        if (!$primarySet) {
            $user->customerAddresses()->latest('id')->first()?->update(['is_primary' => true]);
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'Profile complete! Welcome to ServiceHub.');
    }
}
