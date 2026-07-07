<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────────────
// Public Website
// ──────────────────────────────────────────────
Route::get('/',              [App\Http\Controllers\WebController::class, 'home'])->name('home');
Route::get('/categories',   [App\Http\Controllers\WebController::class, 'categories'])->name('categories');
Route::get('/how-it-works', [App\Http\Controllers\WebController::class, 'howItWorks'])->name('how-it-works');
Route::get('/providers',          [App\Http\Controllers\WebController::class, 'providers'])->name('providers');
Route::get('/providers/{slug}',   [App\Http\Controllers\WebController::class, 'providerProfile'])->name('provider.profile.public');
Route::post('/providers/{slug}/contact', [App\Http\Controllers\WebController::class, 'viewContact'])->name('provider.profile.contact');
Route::post('/providers/{provider}/request', [App\Http\Controllers\Provider\PublicRequestController::class, 'store'])->name('provider.profile.request')->middleware('auth');
Route::post('/providers/{provider}/message', [App\Http\Controllers\Provider\PublicRequestController::class, 'sendMessage'])->name('provider.profile.message')->middleware('auth');
Route::get('/providers/{provider}/messages', [App\Http\Controllers\Provider\PublicRequestController::class, 'getMessages'])->name('provider.profile.messages.get')->middleware('auth');
Route::get('/pricing',      [App\Http\Controllers\WebController::class, 'pricing'])->name('pricing');
Route::get('/post-a-need',  [App\Http\Controllers\WebController::class, 'postANeed'])->name('post-a-need');
Route::get('/about',        [App\Http\Controllers\WebController::class, 'about'])->name('about');
Route::get('/careers',      [App\Http\Controllers\WebController::class, 'careers'])->name('careers');
Route::get('/privacy',      [App\Http\Controllers\WebController::class, 'privacy'])->name('privacy');
Route::get('/terms',        [App\Http\Controllers\WebController::class, 'terms'])->name('terms');
Route::get('/cookies',      [App\Http\Controllers\WebController::class, 'cookies'])->name('cookies');
Route::get('/help',         [App\Http\Controllers\WebController::class, 'help'])->name('help');
Route::get('/safety',       [App\Http\Controllers\WebController::class, 'safety'])->name('safety');
Route::get('/contact',      [App\Http\Controllers\WebController::class, 'contact'])->name('contact');
Route::post('/contact',     [App\Http\Controllers\WebController::class, 'submitContact'])->name('contact.submit');
Route::get('/affiliate-program', [App\Http\Controllers\WebController::class, 'affiliateInfo'])->name('affiliate-info');
Route::get('/resources',    [App\Http\Controllers\WebController::class, 'resources'])->name('resources');
Route::post('/lang/{code}', [App\Http\Controllers\WebController::class, 'setLanguage'])->name('lang.switch');

// ──────────────────────────────────────────────
// Sitemaps
// ──────────────────────────────────────────────
Route::get('/sitemap.xml',             [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap/pages.xml',       [App\Http\Controllers\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap/providers.xml',   [App\Http\Controllers\SitemapController::class, 'providers'])->name('sitemap.providers');
Route::get('/sitemap/categories.xml',  [App\Http\Controllers\SitemapController::class, 'categories'])->name('sitemap.categories');

Route::middleware(['auth', 'verified'])->group(function () {

    // ──────────────────────────────────────────────
    // Geo cascading AJAX lookups
    // ──────────────────────────────────────────────
    Route::prefix('geo')->name('geo.')->group(function () {
        Route::get('divisions', fn() => \App\Models\Division::when(
            request('country_id'),
            fn($q) => $q->where('country_id', request('country_id'))
        )->orderBy('name')->get(['id', 'name']))->name('divisions');

        Route::get('districts', fn() => \App\Models\District::when(
            request('division_id'),
            fn($q) => $q->where('division_id', request('division_id'))
        )->orderBy('name')->get(['id', 'name']))->name('districts');

        Route::get('areas', fn() => \App\Models\Area::when(
            request('district_id'),
            fn($q) => $q->where('district_id', request('district_id'))
        )->orderBy('name')->get(['id', 'name', 'latitude', 'longitude']))->name('areas');
    });

    // Central dispatcher — routes each role to its own dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole(['super_admin', 'admin', 'moderator', 'support'])) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('team_member')) {
            return redirect()->route('tech.schedule.today');
        }
        if ($user->hasRole(['freelancer', 'business'])) {
            return redirect()->route('provider.dashboard');
        }
        return redirect()->route('customer.dashboard');
    })->name('dashboard');

    

    // ──────────────────────────────────────────────
    // Customer
    // ──────────────────────────────────────────────
    Route::middleware(['role:customer', 'customer.address'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'index'])
            ->name('dashboard');

        // Requests
        Route::get('/requests',                            [App\Http\Controllers\Customer\RequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/{request}',                  [App\Http\Controllers\Customer\RequestController::class, 'show'])->name('requests.show');
        Route::post('/requests/{request}/cancel',          [App\Http\Controllers\Customer\RequestController::class, 'cancel'])->name('requests.cancel');

        // Invoices (customer — read-only)
        Route::get('/invoices/{invoice}', [App\Http\Controllers\Customer\InvoiceController::class, 'show'])->name('invoices.show');

        // Requirements
        Route::get('/requirements',                        [App\Http\Controllers\Customer\RequirementController::class, 'index'])->name('requirements.index');
        Route::get('/requirements/create',                 [App\Http\Controllers\Customer\RequirementController::class, 'create'])->name('requirements.create');
        Route::post('/requirements',                       [App\Http\Controllers\Customer\RequirementController::class, 'store'])->name('requirements.store');
        Route::get('/requirements/{requirement}',          [App\Http\Controllers\Customer\RequirementController::class, 'show'])->name('requirements.show');
        Route::post('/requirements/{requirement}/cancel',  [App\Http\Controllers\Customer\RequirementController::class, 'cancel'])->name('requirements.cancel');
        Route::post('/requirements/{requirement}/proposals/{proposal}/accept', [App\Http\Controllers\Customer\RequirementController::class, 'acceptProposal'])->name('requirements.proposals.accept');
        Route::post('/requirements/{requirement}/proposals/{proposal}/reject', [App\Http\Controllers\Customer\RequirementController::class, 'rejectProposal'])->name('requirements.proposals.reject');

        // Addresses
        Route::get('/addresses',                           [App\Http\Controllers\Customer\AddressController::class, 'index'])->name('addresses.index');
        Route::get('/addresses/create',                    [App\Http\Controllers\Customer\AddressController::class, 'create'])->name('addresses.create');
        Route::post('/addresses',                          [App\Http\Controllers\Customer\AddressController::class, 'store'])->name('addresses.store');
        Route::get('/addresses/{address}/edit',            [App\Http\Controllers\Customer\AddressController::class, 'edit'])->name('addresses.edit');
        Route::put('/addresses/{address}',                 [App\Http\Controllers\Customer\AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{address}',              [App\Http\Controllers\Customer\AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::post('/addresses/{address}/primary',        [App\Http\Controllers\Customer\AddressController::class, 'setPrimary'])->name('addresses.primary');

        // Reviews
        Route::get('/reviews',                             [App\Http\Controllers\Customer\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews',                            [App\Http\Controllers\Customer\ReviewController::class, 'store'])->name('reviews.store');

        // Conversations
        Route::get('/conversations',                       [App\Http\Controllers\Customer\ConversationController::class, 'index'])->name('conversations.index');
        Route::get('/conversations/{conversation}',        [App\Http\Controllers\Customer\ConversationController::class, 'show'])->name('conversations.show');
        Route::post('/conversations/{conversation}/messages', [App\Http\Controllers\Customer\ConversationController::class, 'sendMessage'])->name('conversations.message');

        // Support Tickets
        Route::get('/tickets',                             [App\Http\Controllers\Customer\SupportTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/create',                      [App\Http\Controllers\Customer\SupportTicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets',                            [App\Http\Controllers\Customer\SupportTicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/{ticket}',                    [App\Http\Controllers\Customer\SupportTicketController::class, 'show'])->name('tickets.show');
        Route::post('/tickets/{ticket}/reply',             [App\Http\Controllers\Customer\SupportTicketController::class, 'reply'])->name('tickets.reply');

        // Saved Providers
        Route::post('/saved/{provider}',                   [App\Http\Controllers\Customer\SavedProviderController::class, 'toggle'])->name('saved.toggle');
        Route::get('/saved',                               [App\Http\Controllers\Customer\SavedProviderController::class, 'index'])->name('saved.index');
    });

    // Customer onboarding (verified + customer role, no subscription check)
    Route::prefix('customer/onboarding')->name('customer.onboarding.')->middleware('role:customer')->group(function () {
        Route::get('/profile', [App\Http\Controllers\Customer\OnboardingController::class, 'profile'])->name('profile');
        Route::post('/profile', [App\Http\Controllers\Customer\OnboardingController::class, 'profileStore'])->name('profile.store');
        Route::get('/address', [App\Http\Controllers\Customer\OnboardingController::class, 'address'])->name('address');
        Route::post('/address', [App\Http\Controllers\Customer\OnboardingController::class, 'addressStore'])->name('address.store');
    });

    // ──────────────────────────────────────────────
    // Provider
    // ──────────────────────────────────────────────
    Route::middleware(['role:freelancer|business', 'provider.approved'])
        ->prefix('provider')
        ->name('provider.')
        ->group(function () {
            // Subscription page & checkout — no subscription check
            Route::get('/subscription', [App\Http\Controllers\Provider\SubscriptionController::class, 'index'])
                ->name('subscription.index');
            Route::post('/subscription/checkout', [App\Http\Controllers\Provider\SubscriptionCheckoutController::class, 'checkout'])
                ->name('subscription.checkout');
            Route::get('/subscription/success', [App\Http\Controllers\Provider\SubscriptionCheckoutController::class, 'success'])
                ->name('subscription.checkout.success');
            Route::get('/subscription/cancel', [App\Http\Controllers\Provider\SubscriptionCheckoutController::class, 'cancel'])
                ->name('subscription.checkout.cancel');

            // Wallet — accessible even when past_due, so a provider can top up to regain access
            Route::get('/wallet', [App\Http\Controllers\Provider\WalletController::class, 'index'])
                ->name('wallet.index');
            Route::post('/wallet/topup', [App\Http\Controllers\Provider\WalletController::class, 'topup'])
                ->name('wallet.topup');
            Route::get('/wallet/topup/success', [App\Http\Controllers\Provider\WalletController::class, 'success'])
                ->name('wallet.topup.success');

            // All provider pages behind active subscription
            Route::middleware('provider.subscription')->group(function () {
                Route::get('/dashboard', [App\Http\Controllers\Provider\DashboardController::class, 'index'])
                    ->name('dashboard');

                // Provider Profile
                Route::get('/profile/edit', [App\Http\Controllers\Provider\ProviderProfileController::class, 'edit'])->name('profile.edit');
                Route::put('/profile', [App\Http\Controllers\Provider\ProviderProfileController::class, 'update'])->name('profile.update');

                // Provider Hub
                Route::resource('services', App\Http\Controllers\Provider\ServicesController::class)->except(['show']);
                Route::resource('areas', App\Http\Controllers\Provider\ServiceAreaController::class)->except(['show']);
                Route::get('/hours', [App\Http\Controllers\Provider\HoursHolidayController::class, 'index'])->name('hours.index');
                Route::post('/hours', [App\Http\Controllers\Provider\HoursHolidayController::class, 'updateHours'])->name('hours.update');
                Route::post('/holidays', [App\Http\Controllers\Provider\HoursHolidayController::class, 'storeHoliday'])->name('holidays.store');
                Route::delete('/holidays/{holiday}', [App\Http\Controllers\Provider\HoursHolidayController::class, 'destroyHoliday'])->name('holidays.destroy');
                Route::get('/gallery', [App\Http\Controllers\Provider\GalleryController::class, 'index'])->name('gallery.index');
                Route::post('/gallery', [App\Http\Controllers\Provider\GalleryController::class, 'store'])->name('gallery.store');
                Route::post('/gallery/reorder', [App\Http\Controllers\Provider\GalleryController::class, 'reorder'])->name('gallery.reorder');
                Route::delete('/gallery/{gallery}', [App\Http\Controllers\Provider\GalleryController::class, 'destroy'])->name('gallery.destroy');

                // Business
                Route::get('/leads', [App\Http\Controllers\Provider\LeadsController::class, 'index'])->name('leads.index');
                Route::get('/leads/{requirement}', [App\Http\Controllers\Provider\LeadsController::class, 'show'])->name('leads.show');
                Route::post('/leads/{requirement}/propose', [App\Http\Controllers\Provider\LeadsController::class, 'propose'])->name('leads.propose');
                Route::get('/requests', [App\Http\Controllers\Provider\RequestController::class, 'index'])->name('requests.index');
                Route::get('/requests/{serviceRequest}', [App\Http\Controllers\Provider\RequestController::class, 'show'])->name('requests.show');
                Route::post('/requests/{serviceRequest}/status', [App\Http\Controllers\Provider\RequestController::class, 'updateStatus'])->name('requests.status');

                // Invoices
                Route::get('/requests/{serviceRequest}/invoice/create', [App\Http\Controllers\Provider\InvoiceController::class, 'create'])->name('invoices.create');
                Route::post('/requests/{serviceRequest}/invoice', [App\Http\Controllers\Provider\InvoiceController::class, 'store'])->name('invoices.store');
                Route::get('/invoices/{invoice}', [App\Http\Controllers\Provider\InvoiceController::class, 'show'])->name('invoices.show');
                Route::get('/invoices/{invoice}/edit', [App\Http\Controllers\Provider\InvoiceController::class, 'edit'])->name('invoices.edit');
                Route::put('/invoices/{invoice}', [App\Http\Controllers\Provider\InvoiceController::class, 'update'])->name('invoices.update');
                Route::get('/conversations', [App\Http\Controllers\Provider\ConversationController::class, 'index'])->name('conversations.index');
                Route::get('/conversations/{conversation}', [App\Http\Controllers\Provider\ConversationController::class, 'show'])->name('conversations.show');
                Route::post('/conversations/{conversation}/messages', [App\Http\Controllers\Provider\ConversationController::class, 'sendMessage'])->name('conversations.message');
                Route::get('/reviews', [App\Http\Controllers\Provider\ReviewController::class, 'index'])->name('reviews.index');
                Route::post('/reviews/{review}/reply', [App\Http\Controllers\Provider\ReviewController::class, 'reply'])->name('reviews.reply');

                // Account
                Route::get('/affiliate', [App\Http\Controllers\Provider\AffiliateController::class, 'index'])->name('affiliate.index');
                Route::get('/analytics', [App\Http\Controllers\Provider\AnalyticsController::class, 'index'])->name('analytics.index');
                Route::get('/tickets', [App\Http\Controllers\Provider\SupportTicketController::class, 'index'])->name('tickets.index');
                Route::get('/tickets/create', [App\Http\Controllers\Provider\SupportTicketController::class, 'create'])->name('tickets.create');
                Route::post('/tickets', [App\Http\Controllers\Provider\SupportTicketController::class, 'store'])->name('tickets.store');
                Route::get('/tickets/{ticket}', [App\Http\Controllers\Provider\SupportTicketController::class, 'show'])->name('tickets.show');
                Route::post('/tickets/{ticket}/reply', [App\Http\Controllers\Provider\SupportTicketController::class, 'reply'])->name('tickets.reply');
            });
        });

    // OTP Phone Verification (accessible during onboarding & from settings)
    Route::middleware(['role:freelancer|business'])
        ->prefix('provider')
        ->name('provider.')
        ->group(function () {
            Route::post('/otp/send',   [App\Http\Controllers\Provider\OtpController::class, 'send'])->name('otp.send');
            Route::post('/otp/verify', [App\Http\Controllers\Provider\OtpController::class, 'verify'])->name('otp.verify');
        });

    // ──────────────────────────────────────────────
    // Business Team Management (business role only)
    // ──────────────────────────────────────────────
    Route::middleware(['role:business', 'provider.approved', 'provider.subscription'])
        ->prefix('business')
        ->name('business.')
        ->group(base_path('routes/business.php'));

    // ──────────────────────────────────────────────
    // Technician Portal (team members logged in via team_member role)
    // ──────────────────────────────────────────────
    Route::middleware(['role:team_member'])
        ->prefix('tech')
        ->name('tech.')
        ->group(base_path('routes/tech.php'));

    // ──────────────────────────────────────────────
    // Admin
    // ──────────────────────────────────────────────
    Route::middleware(['role:admin|super_admin|moderator|support'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        // Provider Verification Queue
        Route::get('providers', [App\Http\Controllers\Admin\ProviderVerificationController::class, 'index'])
            ->name('providers.index');
        Route::get('providers/{provider}', [App\Http\Controllers\Admin\ProviderVerificationController::class, 'show'])
            ->name('providers.show');
        Route::post('providers/{provider}/approve', [App\Http\Controllers\Admin\ProviderVerificationController::class, 'approve'])
            ->name('providers.approve');
        Route::post('providers/{provider}/reject', [App\Http\Controllers\Admin\ProviderVerificationController::class, 'reject'])
            ->name('providers.reject');
        Route::post('providers/{provider}/documents/{document}/approve', [App\Http\Controllers\Admin\ProviderVerificationController::class, 'approveDocument'])
            ->name('providers.documents.approve');
        Route::post('providers/{provider}/documents/{document}/reject', [App\Http\Controllers\Admin\ProviderVerificationController::class, 'rejectDocument'])
            ->name('providers.documents.reject');

        // Lookup / CRUD
        Route::resource('roles',              App\Http\Controllers\Admin\RoleController::class);
        Route::resource('currencies',         App\Http\Controllers\Admin\CurrencyController::class);
        Route::resource('countries',          App\Http\Controllers\Admin\CountryController::class);
        Route::resource('divisions',          App\Http\Controllers\Admin\DivisionController::class);
        Route::resource('districts',          App\Http\Controllers\Admin\DistrictController::class);
        Route::resource('areas',              App\Http\Controllers\Admin\AreaController::class);
        Route::resource('categories',         App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('faqs',              App\Http\Controllers\Admin\FaqController::class);
        Route::resource('banners',            App\Http\Controllers\Admin\BannerController::class);
        Route::resource('document_types',     App\Http\Controllers\Admin\DocumentTypeController::class);
        Route::resource('services',           App\Http\Controllers\Admin\ServiceController::class);
        Route::resource('subscription_plans', App\Http\Controllers\Admin\SubscriptionPlanController::class);
        Route::resource('coupons',            App\Http\Controllers\Admin\CouponController::class);
        Route::resource('languages',          App\Http\Controllers\Admin\LanguageController::class);

        // Affiliate Referral Payouts (non-provider affiliates only — providers are auto-credited to wallet)
        Route::get('referrals', [App\Http\Controllers\Admin\ReferralController::class, 'index'])->name('referrals.index');
        Route::post('referrals/{referral}/mark-paid', [App\Http\Controllers\Admin\ReferralController::class, 'markPaid'])->name('referrals.mark_paid');

        Route::get('settings',  [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

        // Support Tickets
        Route::get('tickets', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('tickets.index');
        Route::get('tickets/{ticket}', [App\Http\Controllers\Admin\SupportController::class, 'show'])->name('tickets.show');
        Route::post('tickets/{ticket}/reply', [App\Http\Controllers\Admin\SupportController::class, 'reply'])->name('tickets.reply');
        Route::post('tickets/{ticket}/status', [App\Http\Controllers\Admin\SupportController::class, 'updateStatus'])->name('tickets.status');
        Route::post('tickets/{ticket}/assign', [App\Http\Controllers\Admin\SupportController::class, 'assign'])->name('tickets.assign');
    });

    // ──────────────────────────────────────────────
    // Notifications (bell icon + device token)
    // ──────────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',               [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('{id}/read',      [App\Http\Controllers\NotificationController::class, 'markRead'])->name('read');
        Route::post('read-all',       [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('read-all');
        Route::delete('{id}',         [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::post('device-token',   [App\Http\Controllers\NotificationController::class, 'updateDeviceToken'])->name('device-token');
    });

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Guide dismiss (AJAX)
Route::post('/guide/dismiss', function () {
    auth()->user()->update(['guide_dismissed' => true]);
    return response()->json(['ok' => true]);
})->middleware('auth')->name('guide.dismiss');

require __DIR__.'/auth.php';
