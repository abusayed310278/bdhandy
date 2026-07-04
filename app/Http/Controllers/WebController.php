<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\CustomerRequirement;
use App\Models\ProviderProfile;
use App\Models\ProviderServiceArea;
use App\Models\Review;
use App\Models\SavedProvider;
use App\Models\ServiceRequest;
use App\Models\SubscriptionPlan;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class WebController extends Controller
{
    public function __construct(protected SeoService $seo) {}

    public function home()
    {
        $this->seo->home();

        $categories = Category::withCount(['services' => fn($q) => $q->where('status', 'active')])
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $recentRequirements = CustomerRequirement::with(['category', 'currency'])
            ->where('status', 'open')
            ->where(fn($q) => $q->whereNull('expiry_at')->orWhere('expiry_at', '>', now()))
            ->latest()
            ->take(3)
            ->get();

        $featuredProviders = ProviderProfile::with(['user', 'services.service', 'serviceAreas'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 'active')
            ->where('verification_status', 'approved')
            ->where('is_featured', true)
            ->take(4)
            ->get();

        if ($featuredProviders->count() < 4) {
            $ids   = $featuredProviders->pluck('id');
            $extra = ProviderProfile::with(['user', 'services.service', 'serviceAreas'])
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->where('status', 'active')
                ->where('verification_status', 'approved')
                ->whereNotIn('id', $ids)
                ->take(4 - $featuredProviders->count())
                ->get();
            $featuredProviders = $featuredProviders->concat($extra);
        }

        $banner = Banner::where('status', 'active')
            ->where('position', 'homepage_top')
            ->where(fn($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->first();

        $totalApproved  = ProviderProfile::where('verification_status', 'approved')->count();
        $verifiedCount  = ProviderProfile::where('verification_status', 'approved')->where('is_verified', true)->count();
        $distinctCities = ProviderServiceArea::whereHas('providerProfile', fn($q) =>
            $q->where('verification_status', 'approved')->where('status', 'active')
        )->distinct('district_id')->count('district_id');

        $stats = [
            'providers'     => $totalApproved,
            'cities'        => max($distinctCities, 1),
            'verified_rate' => $totalApproved > 0 ? round(($verifiedCount / $totalApproved) * 100) : 98,
            'avg_rating'    => round(Review::where('is_approved', true)->avg('rating') ?? 4.8, 1),
        ];

        $featuredPlan = SubscriptionPlan::where('status', 'active')
            ->orderByDesc('duration_months')
            ->first();

        $latestProviders = ProviderProfile::with(['user', 'services.service', 'serviceAreas'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 'active')
            ->where('verification_status', 'approved')
            ->latest()
            ->take(4)
            ->get();

        $topFreelancers = ProviderProfile::with(['user', 'services.service', 'serviceAreas'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 'active')
            ->where('verification_status', 'approved')
            ->where('provider_type', 'freelancer')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->take(4)
            ->get();

        $topBusinesses = ProviderProfile::with(['user', 'services.service', 'serviceAreas'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 'active')
            ->where('verification_status', 'approved')
            ->where('provider_type', 'business')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->take(4)
            ->get();

        // Ticker: recent open leads + recent service requests
        $tickerLeads = CustomerRequirement::with(['customer.customerAddresses.area', 'service'])
            ->where('status', 'open')
            ->where(fn($q) => $q->whereNull('expiry_at')->orWhere('expiry_at', '>', now()))
            ->latest()
            ->take(10)
            ->get();

        $tickerRequests = ServiceRequest::with(['customer.customerAddresses.area', 'service'])
            ->whereIn('request_status', ['pending', 'accepted', 'in_progress', 'completed'])
            ->latest()
            ->take(10)
            ->get();

        return view('website.home', compact(
            'categories', 'recentRequirements', 'featuredProviders', 'banner',
            'stats', 'featuredPlan', 'latestProviders', 'topFreelancers', 'topBusinesses',
            'tickerLeads', 'tickerRequests'
        ));
    }

    public function categories()
    {
        $this->seo->categories();

        $categories = Category::withCount(['services' => fn($q) => $q->where('status', 'active')])
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        return view('website.categories', compact('categories'));
    }

    public function howItWorks()
    {
        $this->seo->howItWorks();

        return view('website.how-it-works');
    }

    public function providers(Request $request)
    {
        $categories = Category::where('status', 'active')->orderBy('sort_order')->get();

        // Resolve SEO-friendly names for title/description
        $searchKeyword = (string) $request->input('q', '');
        $categoryName  = null;
        if ($request->filled('category')) {
            $locale       = app()->getLocale();
            $cat          = $categories->firstWhere('id', $request->input('category'));
            $categoryName = $cat
                ? ($cat->getTranslation('translations', $locale) ?: $cat->slug)
                : null;
        }
        $this->seo->providers($searchKeyword, $categoryName);

        $query = ProviderProfile::with(['serviceAreas', 'services.service'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 'active')
            ->where('verification_status', 'approved');

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('services.service', fn($q) => $q->where('category_id', $request->category));
        }

        // Text search
        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(fn($q) => $q
                ->where('business_name', 'like', "%{$term}%")
                ->orWhere('tagline', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
            );
        }

        // Rating filter via correlated subquery
        if ($request->filled('rating')) {
            $minRating = (float) $request->rating;
            $query->whereRaw("(
                SELECT AVG(r.rating) FROM reviews r
                WHERE r.provider_id = provider_profiles.user_id
                  AND r.is_approved = 1
            ) >= ?", [$minRating]);
        }

        // Verified only
        if ($request->boolean('verified')) {
            $query->where('is_verified', true);
        }

        // Emergency available
        if ($request->boolean('emergency')) {
            $query->where('emergency_available', true);
        }

        // Provider type
        if ($request->filled('type') && in_array($request->type, ['freelancer', 'business'])) {
            $query->whereHas('user', fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $request->type)));
        }

        // Geo filter using bounding box from request lat/lng, then sort by distance
        $lat = $request->float('lat');
        $lng = $request->float('lng');
        $hasGeo = $lat !== 0.0 && $lng !== 0.0;

        if ($hasGeo) {
            // Filter providers who have a service area within 50 km
            $query->whereHas('serviceAreas', function ($q) use ($lat, $lng) {
                $q->whereRaw("(
                    6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?))
                        + sin(radians(?)) * sin(radians(latitude))
                    )
                ) <= radius_km", [$lat, $lng, $lat]);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'rating');
        match ($sort) {
            'newest' => $query->latest(),
            default  => $query->orderByDesc('reviews_avg_rating')->orderByDesc('reviews_count'),
        };

        // Featured first
        $query->orderByDesc('is_featured');

        $providers = $query->paginate(12)->withQueryString();

        // Compute distance for display
        if ($hasGeo && $providers->count()) {
            foreach ($providers as $profile) {
                $nearest = $profile->serviceAreas
                    ->map(fn($area) => $area->latitude && $area->longitude
                        ? $this->haversine($lat, $lng, $area->latitude, $area->longitude)
                        : null)
                    ->filter()
                    ->min();
                $profile->distance_km = $nearest ? round($nearest, 1) : null;
                $profile->area_label  = $profile->serviceAreas->first()?->area?->name
                                     ?? $profile->serviceAreas->first()?->district?->name
                                     ?? null;
            }
        } else {
            foreach ($providers as $profile) {
                $profile->distance_km = null;
                $profile->area_label  = $profile->serviceAreas->first()?->area?->name
                                     ?? $profile->serviceAreas->first()?->district?->name
                                     ?? null;
            }
        }

        return view('website.providers', compact('providers', 'categories', 'hasGeo', 'lat', 'lng'));
    }

    public function providerProfile(string $slug)
    {
        $profile = ProviderProfile::with([
            'user',
            'services'           => fn($q) => $q->where('status', 'active'),
            'services.service',
            'services.currency',
            'serviceAreas.area',
            'serviceAreas.district',
            'serviceAreas.division',
            'serviceAreas.country',
            'gallery'            => fn($q) => $q->orderBy('sort_order'),
            'businessHours.dayOfWeek',
            'reviews'            => fn($q) => $q->with(['customer', 'reply'])->latest()->take(12),
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->where('slug', $slug)
        ->where('status', 'active')
        ->where('verification_status', 'approved')
        ->firstOrFail();

        $this->seo->providerProfile($profile);

        $isSaved = Auth::check() && Auth::user()->isCustomer()
            ? SavedProvider::where('customer_id', Auth::id())
                ->where('provider_id', $profile->user_id)
                ->exists()
            : false;

        // Fetch 8 similar providers randomly, freelancer or business, preferred nearby location
        $districtIds = $profile->serviceAreas->pluck('district_id')->filter()->unique();
        $areaIds = $profile->serviceAreas->pluck('area_id')->filter()->unique();

        $similarQuery = ProviderProfile::with([
            'user',
            'serviceAreas.area',
            'serviceAreas.district',
            'services.service',
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->where('id', '!=', $profile->id)
        ->where('status', 'active')
        ->where('verification_status', 'approved');

        if ($districtIds->isNotEmpty() || $areaIds->isNotEmpty()) {
            $similarQuery->where(function ($query) use ($districtIds, $areaIds) {
                $query->whereHas('serviceAreas', function ($q) use ($districtIds, $areaIds) {
                    $q->whereIn('district_id', $districtIds)
                      ->orWhereIn('area_id', $areaIds);
                });
            });
        }

        $similarProviders = $similarQuery->inRandomOrder()->take(8)->get();

        if ($similarProviders->count() < 8) {
            $excludeIds = $similarProviders->pluck('id')->push($profile->id);
            $extra = ProviderProfile::with([
                'user',
                'serviceAreas.area',
                'serviceAreas.district',
                'services.service',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereNotIn('id', $excludeIds)
            ->where('status', 'active')
            ->where('verification_status', 'approved')
            ->inRandomOrder()
            ->take(8 - $similarProviders->count())
            ->get();
            $similarProviders = $similarProviders->concat($extra);
        }

        $customerAddresses = (Auth::check() && Auth::user()->isCustomer())
            ? Auth::user()->customerAddresses
            : collect();

        // True only when the user has finished their respective onboarding
        $profileComplete = false;
        if ($user = Auth::user()) {
            if ($user->isCustomer()) {
                $profileComplete = $user->onboarding_profile_done && $customerAddresses->isNotEmpty();
            } elseif ($user->isProvider()) {
                $profileComplete = $user->providerProfile && $user->providerProfile->verification_status === 'approved';
            }
        }

        return view('website.provider-profile', compact('profile', 'isSaved', 'similarProviders', 'customerAddresses', 'profileComplete'));
    }

    public function pricing()
    {
        $this->seo->pricing();

        $user = auth()->user();
        $plans = SubscriptionPlan::with('currency')
            ->where('status', 'active')
            ->when($user && $user->hasRole('freelancer'), fn($q) => $q->whereIn('target', ['provider', 'both']))
            ->when($user && $user->hasRole('business'), fn($q) => $q->whereIn('target', ['business', 'both']))
            ->orderBy('price')
            ->get();

        return view('website.pricing', compact('plans'));
    }

    public function setLanguage(Request $request, string $code)
    {
        $supported = ['en', 'bn', 'ar', 'uz', 'ru'];
        if (!in_array($code, $supported)) {
            return back();
        }

        // Save to user record if authenticated
        if (Auth::check()) {
            Auth::user()->update(['preferred_language' => $code]);
        }

        // Set cookie (30-day)
        Cookie::queue('app_locale', $code, 60 * 24 * 30);

        return back()->withCookie(Cookie::make('app_locale', $code, 60 * 24 * 30));
    }

    public function postANeed()
    {
        if (Auth::check() && Auth::user()->hasRole('customer')) {
            return redirect()->route('customer.requirements.create');
        }
        return redirect()->route('register');
    }

    public function about()
    {
        $this->seo->about();
        return view('website.about');
    }

    public function careers()
    {
        $this->seo->careers();
        return view('website.careers');
    }

    public function privacy()
    {
        $this->seo->privacy();
        return view('website.privacy');
    }

    public function terms()
    {
        $this->seo->terms();
        return view('website.terms');
    }

    public function cookies()
    {
        $this->seo->cookies();
        return view('website.cookies');
    }

    public function help()
    {
        $this->seo->help();
        return view('website.help');
    }

    public function safety()
    {
        $this->seo->safety();
        return view('website.safety');
    }

    public function contact()
    {
        $this->seo->contact();
        return view('website.contact');
    }

    public function submitContact(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = \App\Models\ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'new',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "You have received a new contact message.\n\n" .
                "Name: {$contact->name}\n" .
                "Email: {$contact->email}\n" .
                "Phone: " . ($contact->phone ?? 'N/A') . "\n" .
                "Subject: {$contact->subject}\n\n" .
                "Message:\n{$contact->message}",
                function ($message) use ($contact) {
                    $message->to('sahirabd@gmail.com')
                        ->subject("New Contact Message: {$contact->subject}")
                        ->from(config('mail.from.address'), config('mail.from.name') ?? config('app.name'));
                }
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Contact form mail failure: " . $e->getMessage());
        }

        return back()->with('success', 'Your message has been sent successfully!');
    }
    public function affiliateInfo()
    {
        $this->seo->affiliate();
        return view('website.affiliate-info');
    }

    public function resources()
    {
        return view('website.resources');
    }

    public function viewContact(string $slug)
    {
        $profile = ProviderProfile::where('slug', $slug)
            ->where('status', 'active')
            ->where('verification_status', 'approved')
            ->firstOrFail();

        $ip = request()->ip();
        // If developing locally (127.0.0.1 or ::1), mock a public IP for testing geo IP lookup
        if ($ip === '127.0.0.1' || $ip === '::1') {
            $ip = '103.145.228.1'; 
        }

        $loc = null;
        try {
            $loc = \Stevebauman\Location\Facades\Location::get($ip);
        } catch (\Exception $e) {
            // Ignore location errors
        }

        $agent = new \Jenssegers\Agent\Agent();
        $deviceType = $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop');
        $browser = $agent->browser();
        $platform = $agent->platform();
        $userAgent = $agent->getUserAgent() ?? request()->header('User-Agent');

        \App\Models\ProviderContactView::create([
            'provider_profile_id' => $profile->id,
            'user_id' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null,
            'ip_address' => request()->ip(),
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'browser' => $browser,
            'platform' => $platform,
            'country' => $loc ? $loc->countryName : null,
            'city' => $loc ? $loc->cityName : null,
            'region' => $loc ? $loc->regionName : null,
            'latitude' => $loc ? $loc->latitude : null,
            'longitude' => $loc ? $loc->longitude : null,
        ]);

        return response()->json([
            'ok' => true,
            'primary_phone' => $profile->primary_phone,
            'whatsapp_number' => $profile->whatsapp_number,
            'website' => $profile->website,
            'facebook_url' => $profile->facebook_url,
            'instagram_url' => $profile->instagram_url,
            'youtube_url' => $profile->youtube_url,
        ]);
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R  = 6371;
        $dL = deg2rad($lat2 - $lat1);
        $dG = deg2rad($lng2 - $lng1);
        $a  = sin($dL/2) * sin($dL/2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dG/2) * sin($dG/2);
        return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
    }
}
