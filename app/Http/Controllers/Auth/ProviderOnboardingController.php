<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ProviderSubmittedMail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Currency;
use App\Models\DayOfWeek;
use App\Models\District;
use App\Models\Division;
use App\Models\Area;
use App\Models\DocumentType;
use App\Models\Language;
use App\Models\ProviderBusinessHour;
use App\Models\ProviderDocument;
use App\Models\ProviderProfile;
use App\Models\ProviderService;
use App\Models\ProviderServiceArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProviderOnboardingController extends Controller
{
    public function profile(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->providerProfile && $user->providerProfile->verification_status === 'in_review') {
            return redirect()->route('provider.onboarding.pending');
        }

        $taglineSuggestions = $this->loadTaglineSuggestions();

        return view('auth.onboarding.provider-profile', [
            'profile'            => $user->providerProfile,
            'languages'          => Language::getActiveLanguages(),
            'currencies'         => Currency::where('status', 'active')->get(),
            'taglineSuggestions' => $taglineSuggestions,
        ]);
    }

    public function profileStore(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $providerType = $user->hasRole('freelancer') ? 'freelancer' : 'business';

        // Prepend https:// to website and social links if not present
        foreach (['website', 'facebook_url', 'instagram_url', 'youtube_url'] as $key) {
            if ($request->filled($key)) {
                $val = trim($request->input($key));
                if (!preg_match('/^(https?:\/\/)/i', $val)) {
                    $request->merge([$key => 'https://' . $val]);
                }
            }
        }

        $otpEnabled = (bool) \App\Models\Setting::get('otp_verification_enabled', '0');
        if ($otpEnabled && !$user->phone_verified_at && !$request->boolean('verify_later')) {
            return back()->withErrors([
                'primary_phone' => 'Please verify your phone number or check "Skip phone verification for now" to continue.'
            ])->withInput();
        }

        $request->validate([
            'business_name'       => ['required', 'string', 'max:255'],
            'tagline'             => ['nullable', 'string', 'max:255'],
            'description'         => ['nullable', 'string', 'max:2000'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'experience_level'    => ['required', 'in:beginner,intermediate,expert'],
            'currency_id'         => ['required', 'exists:currencies,id'],
            'primary_phone'       => ['required', 'string', 'max:30'],
            'whatsapp_number'     => ['nullable', 'string', 'max:30'],
            'languages'           => ['required', 'array', 'min:1'],
            'emergency_available' => ['nullable', 'boolean'],
            'website'             => ['nullable', 'url', 'max:255'],
            'facebook_url'        => ['nullable', 'url', 'max:255'],
            'instagram_url'       => ['nullable', 'url', 'max:255'],
            'youtube_url'         => ['nullable', 'url', 'max:255'],
            'logo'                => ['nullable', 'image', 'max:2048'],
            'cover_photo'         => ['nullable', 'image', 'max:4096'],
        ]);

        $data = [
            'provider_type'        => $providerType,
            'business_name'        => $request->business_name,
            'tagline'              => $request->tagline,
            'description'          => $request->description,
            'years_of_experience'  => $request->years_of_experience,
            'experience_level'     => $request->experience_level,
            'currency_id'          => $request->currency_id,
            'primary_phone'        => $request->primary_phone,
            'whatsapp_number'      => $request->whatsapp_number,
            'languages'            => $request->input('languages') ?? [],
            'emergency_available'  => $request->boolean('emergency_available'),
            'website'              => $request->website,
            'facebook_url'         => $request->facebook_url,
            'instagram_url'        => $request->instagram_url,
            'youtube_url'          => $request->youtube_url,
            'verification_status'  => 'pending',
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('provider-logos', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $data['cover_photo'] = $request->file('cover_photo')->store('provider-covers', 'public');
        }

        ProviderProfile::updateOrCreate(['user_id' => $user->id], $data);

        return redirect()->route('provider.onboarding.services');
    }

    public function services(): View|RedirectResponse
    {
        $user    = Auth::user();
        $profile = $user->providerProfile;

        if (!$profile) {
            return redirect()->route('provider.onboarding.profile');
        }

        $categories = Category::with(['services' => fn($q) => $q->where('status', 'active')])
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        $currencies      = Currency::where('status', 'active')->get();
        $defaultCurrency = $currencies->first();
        $days            = DayOfWeek::all();
        $existingHours   = $profile->businessHours->keyBy('day_of_week_id');

        // Prepared for @json() — safe HTML encoding
        $categoriesForJs = $categories->map(fn($c) => [
            'id'       => $c->id,
            'name'     => $c->getTranslation('translations', 'en') ?: $c->slug,
            'services' => $c->services->map(fn($s) => [
                'id'   => $s->id,
                'name' => ($s->getTranslation('translations', 'en')['name'] ?? null) ?: $s->slug,
            ])->values(),
        ])->values();

        $currenciesForJs = $currencies->map(fn($c) => [
            'id' => $c->id, 'symbol' => $c->symbol, 'name' => $c->name,
        ])->values();

        $existingServices = $profile->services->map(fn($s) => [
            'category_id'      => $s->service?->category_id,
            'service_id'       => $s->service_id,
            'title'            => $s->title,
            'description'      => $s->description ?? '',
            'pricing_type'     => $s->pricing_type,
            'price_fixed'      => $s->price_fixed,
            'price_min'        => $s->price_min,
            'price_max'        => $s->price_max,
            'currency_id'      => $s->currency_id,
            'duration_minutes' => $s->duration_minutes,
            'is_emergency'     => (bool) $s->is_emergency,
        ])->values()->toArray();

        $hoursForJs = $days->map(fn($day) => [
            'dayId'  => $day->id,
            'name'   => $day->getTranslation('translations', 'en') ?: 'Day ' . $day->id,
            'closed' => $existingHours->has($day->id) ? (bool) $existingHours->get($day->id)->is_closed : false,
            'start'  => $existingHours->has($day->id)
                ? substr($existingHours->get($day->id)->start_time ?? '09:00', 0, 5)
                : '09:00',
            'end'    => $existingHours->has($day->id)
                ? substr($existingHours->get($day->id)->end_time ?? '18:00', 0, 5)
                : '18:00',
        ])->values()->toArray();

        return view('auth.onboarding.provider-services', [
            'categoriesForJs'  => $categoriesForJs,
            'currenciesForJs'  => $currenciesForJs,
            'defaultCurrencyId'=> $defaultCurrency?->id ?? 1,
            'existingServices' => $existingServices,
            'hoursForJs'       => $hoursForJs,
        ]);
    }

    public function servicesStore(Request $request): RedirectResponse
    {
        $user    = Auth::user();
        $profile = $user->providerProfile;

        if (!$profile) {
            return redirect()->route('provider.onboarding.profile');
        }

        $request->validate([
            'services'                      => ['nullable', 'array'],
            'services.*.service_id'         => ['required', 'exists:services,id'],
            'services.*.title'              => ['required', 'string', 'max:255'],
            'services.*.description'        => ['nullable', 'string', 'max:2000'],
            'services.*.pricing_type'       => ['required', 'in:fixed,range,hourly,quote'],
            'services.*.price_fixed'        => ['nullable', 'numeric', 'min:0'],
            'services.*.price_min'          => ['nullable', 'numeric', 'min:0'],
            'services.*.price_max'          => ['nullable', 'numeric', 'min:0'],
            'services.*.currency_id'        => ['required', 'exists:currencies,id'],
            'services.*.duration_minutes'   => ['nullable', 'integer', 'min:1'],
            'hours'                         => ['nullable', 'array'],
            'hours.*.start_time'            => ['nullable', 'date_format:H:i'],
            'hours.*.end_time'              => ['nullable', 'date_format:H:i'],
        ]);

        // Sync services
        $profile->services()->delete();
        foreach (($request->services ?? []) as $svc) {
            $profile->services()->create([
                'service_id'       => $svc['service_id'],
                'title'            => $svc['title'],
                'description'      => $svc['description'] ?? null,
                'pricing_type'     => $svc['pricing_type'],
                'price_fixed'      => $svc['price_fixed'] ?? null,
                'price_min'        => $svc['price_min'] ?? null,
                'price_max'        => $svc['price_max'] ?? null,
                'currency_id'      => $svc['currency_id'],
                'duration_minutes' => $svc['duration_minutes'] ?? null,
                'is_emergency'     => isset($svc['is_emergency']) && $svc['is_emergency'] == '1',
                'status'           => 'active',
            ]);
        }

        // Sync business hours
        foreach (($request->hours ?? []) as $dayId => $hour) {
            $isClosed = isset($hour['is_closed']) && $hour['is_closed'] === '1';

            ProviderBusinessHour::updateOrCreate(
                ['provider_profile_id' => $profile->id, 'day_of_week_id' => $dayId],
                [
                    'is_closed'  => $isClosed,
                    'start_time' => $isClosed ? null : ($hour['start_time'] ?? null),
                    'end_time'   => $isClosed ? null : ($hour['end_time'] ?? null),
                ]
            );
        }

        return redirect()->route('provider.onboarding.service-area');
    }

    public function serviceArea(): View|RedirectResponse
    {
        $user    = Auth::user();
        $profile = $user->providerProfile;

        if (!$profile) {
            return redirect()->route('provider.onboarding.profile');
        }

        $area      = $profile->serviceAreas->first();
        $countries = Country::orderBy('name')->get();

        $singleCountry = $countries->count() === 1 ? $countries->first() : null;
        $countryId     = $area?->country_id ?? $singleCountry?->id;

        // Pre-load divisions for the resolved country (existing area OR single country)
        $divisions = $countryId
            ? Division::where('country_id', $countryId)->orderBy('name')->get()
            : collect();

        $districts = $area
            ? District::where('division_id', $area->division_id)->orderBy('name')->get()
            : collect();

        $areas = $area
            ? Area::where('district_id', $area->district_id)->orderBy('name')->get()
            : collect();

        return view('auth.onboarding.provider-service-area', [
            'area'          => $area,
            'countries'     => $countries,
            'singleCountry' => $singleCountry,
            'divisions'     => $divisions,
            'districts'     => $districts,
            'areas'         => $areas,
        ]);
    }

    public function serviceAreaStore(Request $request): RedirectResponse
    {
        $user    = Auth::user();
        $profile = $user->providerProfile;

        if (!$profile) {
            return redirect()->route('provider.onboarding.profile');
        }

        $request->validate([
            'country_id'  => ['required', 'exists:countries,id'],
            'division_id' => ['required', 'exists:divisions,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'area_id'     => ['required', 'exists:areas,id'],
            'address'     => ['nullable', 'string', 'max:500'],
            'latitude'    => ['nullable', 'numeric'],
            'longitude'   => ['nullable', 'numeric'],
            'radius_km'   => ['nullable', 'numeric', 'min:1', 'max:50'],
        ]);

        // One area per provider — replace if exists
        ProviderServiceArea::updateOrCreate(
            ['provider_profile_id' => $profile->id],
            [
                'country_id'  => $request->country_id,
                'division_id' => $request->division_id,
                'district_id' => $request->district_id,
                'area_id'     => $request->area_id,
                'address'     => $request->address,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                'radius_km'   => $request->radius_km ?? 5,
            ]
        );

        return redirect()->route('provider.onboarding.documents');
    }

    public function documents(): View|RedirectResponse
    {
        $user    = Auth::user();
        $profile = $user->providerProfile;

        if (!$profile) {
            return redirect()->route('provider.onboarding.profile');
        }

        $providerType  = $user->hasRole('freelancer') ? 'freelancer' : 'business';
        $documentTypes = DocumentType::where(function ($q) use ($providerType) {
            $q->where('provider_type', $providerType)->orWhere('provider_type', 'both');
        })->get();

        $uploadedDocs = $profile->documents->keyBy('document_type_id');

        return view('auth.onboarding.provider-documents', [
            'documentTypes' => $documentTypes,
            'uploadedDocs'  => $uploadedDocs,
            'profile'       => $profile,
        ]);
    }

    public function documentsStore(Request $request): RedirectResponse
    {
        $user    = Auth::user();
        $profile = $user->providerProfile;

        if (!$profile) {
            return redirect()->route('provider.onboarding.profile');
        }

        $providerType  = $user->hasRole('freelancer') ? 'freelancer' : 'business';
        $documentTypes = DocumentType::where(function ($q) use ($providerType) {
            $q->where('provider_type', $providerType)->orWhere('provider_type', 'both');
        })->get();

        $existingDocs = $profile->documents->keyBy('document_type_id');

        $rules = [];
        foreach ($documentTypes as $docType) {
            $existing = $existingDocs->get($docType->id);
            if ($existing && $existing->verification_status === 'approved') {
                continue;
            }
            $field      = 'doc_' . $docType->id;
            $isRequired = !$existing || $existing->verification_status === 'rejected';
            $rules[$field]                       = $isRequired
                ? ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120']
                : ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
            $rules['doc_number_' . $docType->id] = ['required', 'string', 'max:100'];
        }

        $request->validate($rules);

        foreach ($documentTypes as $docType) {
            $existing = $existingDocs->get($docType->id);
            if ($existing && $existing->verification_status === 'approved') {
                continue;
            }
            $field = 'doc_' . $docType->id;
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('provider-documents', 'public');
                ProviderDocument::updateOrCreate(
                    ['provider_profile_id' => $profile->id, 'document_type_id' => $docType->id],
                    [
                        'document_file'       => $path,
                        'document_number'     => $request->input('doc_number_' . $docType->id),
                        'verification_status' => 'pending',
                        'rejection_reason'    => null,
                    ]
                );
            }
        }

        $profile->update(['verification_status' => 'in_review']);

        Mail::to($user->email)->queue(new ProviderSubmittedMail($user));

        return redirect()->route('provider.onboarding.pending');
    }

    public function pending(): View
    {
        $user    = Auth::user();
        $profile = $user->providerProfile?->load('documents.documentType');

        return view('auth.onboarding.pending-verification', [
            'profile' => $profile,
        ]);
    }

    private function loadTaglineSuggestions(): array
    {
        $path = public_path('tagline.txt');
        if (!file_exists($path)) {
            return [];
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_values(array_filter(array_map('trim', $lines)));
    }
}
