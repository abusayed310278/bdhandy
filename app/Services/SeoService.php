<?php

namespace App\Services;

use App\Models\ProviderProfile;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Str;

class SeoService
{
    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function country(): array
    {
        $code = config('seo.active_country', 'BD');
        return config("seo.countries.{$code}", config('seo.countries.BD'));
    }

    private function ogLocale(): string
    {
        return config('seo.og_locale_map.' . app()->getLocale(), 'en_US');
    }

    private function keywords(string $locale): array
    {
        return config("seo.keywords.{$locale}", config('seo.keywords.en', []));
    }

    private function ogImage(): string
    {
        return url(config('seo.og_image', '/images/og-default.jpg'));
    }

    // ─── Core setter — called by every page method ─────────────────────────

    private function apply(string $title, string $desc, string $url, string $type = 'website', ?string $image = null): void
    {
        $app     = config('app.name');
        $country = $this->country();
        $img     = $image ?? $this->ogImage();

        SEOMeta::setTitle($title);
        SEOMeta::setDescription($desc);
        SEOMeta::setCanonical($url);
        SEOMeta::addMeta('robots', 'index, follow');

        OpenGraph::setTitle($title);
        OpenGraph::setDescription($desc);
        OpenGraph::setUrl($url);
        OpenGraph::setType($type);
        OpenGraph::setSiteName($app);
        OpenGraph::addProperty('locale', $this->ogLocale());
        OpenGraph::addImage($img, [
            'width'  => config('seo.og_image_width', 1200),
            'height' => config('seo.og_image_height', 630),
        ]);
        if (!empty($country['fb_app_id'])) {
            OpenGraph::addProperty('fb:app_id', $country['fb_app_id']);
        }

        TwitterCard::setType(config('seo.twitter_card', 'summary_large_image'));
        if (!empty($country['twitter_handle'])) {
            TwitterCard::setSite($country['twitter_handle']);
        }
        TwitterCard::setTitle($title);
        TwitterCard::setDescription($desc);
        TwitterCard::setImage($img);
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public function home(): void
    {
        $locale  = app()->getLocale();
        $app     = config('app.name');
        $country = $this->country();
        $city    = $country['primary_city'];

        $title = __('seo.home.title', ['app' => $app, 'city' => $city]);
        $desc  = __('seo.home.description', ['app' => $app, 'city' => $city, 'country' => $country['name']]);

        $this->apply($title, $desc, url('/'));
        SEOMeta::setKeywords($this->keywords($locale));

        // WebSite schema with SearchAction (sitelinks search box)
        JsonLdMulti::newJsonLd();
        JsonLdMulti::setType('WebSite');
        JsonLdMulti::setTitle($app);
        JsonLdMulti::setDescription($desc);
        JsonLdMulti::setUrl(url('/'));
        JsonLdMulti::addValue('potentialAction', [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => url('/providers') . '?q={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ]);

        // Organization schema
        JsonLdMulti::newJsonLd();
        $this->organizationSchema();
    }

    public function categories(): void
    {
        $locale = app()->getLocale();
        $app    = config('app.name');
        $city   = $this->country()['primary_city'];

        $title = __('seo.categories.title', ['app' => $app]);
        $desc  = __('seo.categories.description', ['app' => $app, 'city' => $city]);

        $this->apply($title, $desc, route('categories'));
        SEOMeta::setKeywords($this->keywords($locale));

        JsonLdMulti::newJsonLd();
        $this->breadcrumbSchema([
            ['name' => __('web.nav.categories'), 'item' => route('categories')],
        ]);
    }

    public function providers(string $keyword = '', ?string $categoryName = null): void
    {
        $locale = app()->getLocale();
        $app    = config('app.name');
        $city   = $this->country()['primary_city'];

        if ($keyword) {
            $title = __('seo.providers.title_search', ['app' => $app, 'keyword' => $keyword]);
            $desc  = __('seo.providers.description_search', ['app' => $app, 'keyword' => $keyword, 'city' => $city]);
        } elseif ($categoryName) {
            $title = __('seo.providers.title_category', ['app' => $app, 'category' => $categoryName]);
            $desc  = __('seo.providers.description_category', ['app' => $app, 'category' => $categoryName, 'city' => $city]);
        } else {
            $title = __('seo.providers.title', ['app' => $app]);
            $desc  = __('seo.providers.description', ['app' => $app, 'city' => $city]);
        }

        $this->apply($title, $desc, request()->fullUrl());
        SEOMeta::setKeywords($this->keywords($locale));

        JsonLdMulti::newJsonLd();
        $this->breadcrumbSchema([
            ['name' => __('web.nav.find_providers'), 'item' => route('providers')],
        ]);
    }

    public function providerProfile(ProviderProfile $profile): void
    {
        $app     = config('app.name');
        $country = $this->country();
        $city    = $country['primary_city'];

        $name    = $profile->business_name ?? $profile->user->name ?? $app;
        $type    = ucfirst($profile->provider_type ?? 'provider');
        $tagline = Str::limit(strip_tags($profile->tagline ?? $profile->description ?? ''), 100);
        $area    = $profile->serviceAreas->first()?->area?->name
                ?? $profile->serviceAreas->first()?->district?->name
                ?? $city;

        $profileImage = $profile->logo
            ? asset('storage/' . $profile->logo)
            : $this->ogImage();

        $title = $area
            ? __('seo.provider_profile.title', ['name' => $name, 'type' => $type, 'area' => $area, 'app' => $app])
            : __('seo.provider_profile.title_no_area', ['name' => $name, 'type' => $type, 'app' => $app]);

        $desc = $tagline
            ? __('seo.provider_profile.description', ['name' => $name, 'app' => $app, 'tagline' => $tagline, 'type' => $type, 'area' => $area])
            : __('seo.provider_profile.description_no_tagline', ['name' => $name, 'app' => $app, 'type' => $type, 'area' => $area]);

        $desc = Str::limit(strip_tags($desc), 155);
        $url  = route('provider.profile.public', $profile->slug);

        $this->apply($title, $desc, $url, 'profile', $profileImage);
        SEOMeta::setKeywords([
            $type . ' near me',
            $name . ' ' . $city,
            'hire ' . strtolower($type),
            $city . ' ' . strtolower($type),
        ]);

        // LocalBusiness / Person schema
        JsonLdMulti::newJsonLd();
        $schemaType = $profile->provider_type === 'business' ? 'LocalBusiness' : 'Person';
        JsonLdMulti::setType($schemaType);
        JsonLdMulti::setTitle($name);
        JsonLdMulti::setDescription(Str::limit(strip_tags($profile->description ?? ''), 200));
        JsonLdMulti::setUrl($url);
        JsonLdMulti::addImage($profileImage);
        if ($profile->primary_phone) {
            JsonLdMulti::addValue('telephone', $profile->primary_phone);
        }
        if (!empty($area)) {
            JsonLdMulti::addValue('areaServed', $area);
        }
        if (!is_null($profile->reviews_avg_rating) && $profile->reviews_count > 0) {
            JsonLdMulti::addValue('aggregateRating', [
                '@type'       => 'AggregateRating',
                'ratingValue' => round((float) $profile->reviews_avg_rating, 1),
                'reviewCount' => (int) $profile->reviews_count,
                'bestRating'  => 5,
                'worstRating' => 1,
            ]);
        }

        // Breadcrumb schema
        JsonLdMulti::newJsonLd();
        $this->breadcrumbSchema([
            ['name' => __('web.nav.find_providers'), 'item' => route('providers')],
            ['name' => $name, 'item' => $url],
        ]);
    }

    public function pricing(): void
    {
        $app  = config('app.name');

        $title = __('seo.pricing.title', ['app' => $app]);
        $desc  = __('seo.pricing.description', ['app' => $app]);

        $this->apply($title, $desc, route('pricing'));

        JsonLdMulti::newJsonLd();
        $this->breadcrumbSchema([
            ['name' => __('web.nav.pricing'), 'item' => route('pricing')],
        ]);
    }

    public function howItWorks(): void
    {
        $app   = config('app.name');
        $title = __('seo.how_it_works.title', ['app' => $app]);
        $desc  = __('seo.how_it_works.description', ['app' => $app]);

        $this->apply($title, $desc, route('how-it-works'));

        JsonLdMulti::newJsonLd();
        $this->breadcrumbSchema([
            ['name' => __('web.nav.how_it_works'), 'item' => route('how-it-works')],
        ]);
    }

    public function about(): void
    {
        $app = config('app.name');
        $this->apply(
            __('seo.about.title', ['app' => $app]),
            __('seo.about.description', ['app' => $app]),
            route('about'),
            'website'
        );
        JsonLdMulti::newJsonLd();
        JsonLdMulti::setType('AboutPage');
        JsonLdMulti::setTitle(__('seo.about.title', ['app' => $app]));
        JsonLdMulti::setUrl(route('about'));
    }

    public function contact(): void
    {
        $app = config('app.name');
        $this->apply(
            __('seo.contact.title', ['app' => $app]),
            __('seo.contact.description', ['app' => $app]),
            route('contact'),
            'website'
        );
        JsonLdMulti::newJsonLd();
        JsonLdMulti::setType('ContactPage');
        JsonLdMulti::setTitle(__('seo.contact.title', ['app' => $app]));
        JsonLdMulti::setUrl(route('contact'));
    }

    public function careers(): void
    {
        $app = config('app.name');
        $this->apply(
            __('seo.careers.title', ['app' => $app]),
            __('seo.careers.description', ['app' => $app]),
            route('careers')
        );
    }

    public function help(): void
    {
        $app = config('app.name');
        $this->apply(
            __('seo.help.title', ['app' => $app]),
            __('seo.help.description', ['app' => $app]),
            route('help')
        );
    }

    public function safety(): void
    {
        $app = config('app.name');
        $this->apply(
            __('seo.safety.title', ['app' => $app]),
            __('seo.safety.description', ['app' => $app]),
            route('safety')
        );
    }

    public function privacy(): void
    {
        $app = config('app.name');
        $this->apply(
            __('seo.privacy.title', ['app' => $app]),
            __('seo.privacy.description', ['app' => $app]),
            route('privacy')
        );
        SEOMeta::addMeta('robots', 'noindex, follow');
    }

    public function terms(): void
    {
        $app = config('app.name');
        $this->apply(
            __('seo.terms.title', ['app' => $app]),
            __('seo.terms.description', ['app' => $app]),
            route('terms')
        );
        SEOMeta::addMeta('robots', 'noindex, follow');
    }

    public function cookies(): void
    {
        $app = config('app.name');
        $this->apply(
            __('seo.cookies.title', ['app' => $app]),
            __('seo.cookies.description', ['app' => $app]),
            route('cookies')
        );
        SEOMeta::addMeta('robots', 'noindex, follow');
    }

    public function affiliate(): void
    {
        $app = config('app.name');
        $this->apply(
            __('seo.affiliate.title', ['app' => $app]),
            __('seo.affiliate.description', ['app' => $app]),
            route('affiliate-info')
        );
    }

    // ─── JSON-LD helpers ──────────────────────────────────────────────────────

    private function organizationSchema(): void
    {
        $country = $this->country();
        $app     = config('app.name');

        JsonLdMulti::setType('Organization');
        JsonLdMulti::setTitle($app);
        JsonLdMulti::setUrl(url('/'));
        JsonLdMulti::addImage($this->ogImage());

        $sameAs = array_filter([
            $country['fb_page_url'] ?? null,
        ]);
        if (!empty($sameAs)) {
            JsonLdMulti::addValue('sameAs', array_values($sameAs));
        }

        JsonLdMulti::addValue('address', [
            '@type'           => 'PostalAddress',
            'addressLocality' => $country['address']['city'],
            'addressRegion'   => $country['address']['state'] ?? '',
            'addressCountry'  => $country['address']['country'],
            'postalCode'      => $country['address']['postal'] ?? '',
        ]);

        JsonLdMulti::addValue('geo', [
            '@type'     => 'GeoCoordinates',
            'latitude'  => $country['geo']['latitude'],
            'longitude' => $country['geo']['longitude'],
        ]);
    }

    private function breadcrumbSchema(array $items): void
    {
        $list = [];
        foreach ($items as $i => $item) {
            $list[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $item['name'],
                'item'     => $item['item'],
            ];
        }

        JsonLdMulti::setType('BreadcrumbList');
        JsonLdMulti::addValue('itemListElement', $list);
    }
}
