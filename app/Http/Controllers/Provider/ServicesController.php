<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Currency;
use App\Models\ProviderService;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServicesController extends Controller
{
    private function profile()
    {
        return Auth::user()->providerProfile;
    }

    public function index(): View
    {
        $profile  = $this->profile();
        $services = ProviderService::with(['service', 'currency'])
            ->where('provider_profile_id', $profile->id)
            ->latest()
            ->paginate(20);

        return view('provider.services.index', compact('services'));
    }

    public function create(): View
    {
        $categories = $this->getCategoriesForDropdown();
        $currencies = Currency::orderBy('name')->get();

        return view('provider.services.form', compact('categories', 'currencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_id'       => ['required', 'exists:services,id'],
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'pricing_type'     => ['required', 'in:fixed,range,hourly,quote'],
            'price_fixed'      => ['nullable', 'numeric', 'min:0'],
            'price_min'        => ['nullable', 'numeric', 'min:0'],
            'price_max'        => ['nullable', 'numeric', 'min:0'],
            'currency_id'      => ['nullable', 'exists:currencies,id'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'is_emergency'     => ['boolean'],
            'status'           => ['required', 'in:active,inactive'],
        ]);

        $data['provider_profile_id'] = $this->profile()->id;
        $data['is_emergency']        = $request->boolean('is_emergency');

        ProviderService::create($data);

        return redirect()->route('provider.services.index')->with('success', 'Service added successfully.');
    }

    public function edit(ProviderService $service): View
    {
        $this->authorizeService($service);

        $categories = $this->getCategoriesForDropdown();
        $currencies = Currency::orderBy('name')->get();

        return view('provider.services.form', compact('service', 'categories', 'currencies'));
    }

    public function update(Request $request, ProviderService $service): RedirectResponse
    {
        $this->authorizeService($service);

        $data = $request->validate([
            'service_id'       => ['required', 'exists:services,id'],
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'pricing_type'     => ['required', 'in:fixed,range,hourly,quote'],
            'price_fixed'      => ['nullable', 'numeric', 'min:0'],
            'price_min'        => ['nullable', 'numeric', 'min:0'],
            'price_max'        => ['nullable', 'numeric', 'min:0'],
            'currency_id'      => ['nullable', 'exists:currencies,id'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'is_emergency'     => ['boolean'],
            'status'           => ['required', 'in:active,inactive'],
        ]);

        $data['is_emergency'] = $request->boolean('is_emergency');
        $service->update($data);

        return redirect()->route('provider.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(ProviderService $service): RedirectResponse
    {
        $this->authorizeService($service);
        $service->delete();

        return redirect()->route('provider.services.index')->with('success', 'Service removed.');
    }

    private function authorizeService(ProviderService $service): void
    {
        if ($service->provider_profile_id !== $this->profile()->id) {
            abort(403);
        }
    }

    private function getCategoriesForDropdown()
    {
        return Category::where('status', 'active')
            ->with(['services' => fn($q) => $q->where('status', 'active')->orderBy('slug')])
            ->get()
            ->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->getTranslation('translations', 'en')['name'] ?? $cat->slug,
                    'services' => $cat->services->map(fn($s) => [
                        'id' => $s->id,
                        'name' => $s->getTranslation('translations', 'en')['name'] ?? $s->slug,
                    ])
                ];
            });
    }
}
