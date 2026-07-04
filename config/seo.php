<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active Country
    |--------------------------------------------------------------------------
    | Controls which country's config drives platform-level SEO defaults.
    | To launch in a new region, set SEO_ACTIVE_COUNTRY=AE (etc.) in .env —
    | no controller changes required.
    */
    'active_country' => env('SEO_ACTIVE_COUNTRY', 'BD'),

    /*
    |--------------------------------------------------------------------------
    | Per-Country Configs
    |--------------------------------------------------------------------------
    | Add a new entry here for each new region. Keep one entry per country code.
    */
    'countries' => [

        'BD' => [
            'name'            => 'Bangladesh',
            'locale'          => 'bn',
            'fallback_locale' => 'en',
            'currency'        => 'BDT',
            'currency_symbol' => '৳',
            'primary_city'    => 'Dhaka',
            'timezone'        => 'Asia/Dhaka',
            'phone_code'      => '+880',
            'twitter_handle'  => env('SEO_TWITTER_BD', '@bdhandy'),
            'fb_page_url'     => env('SEO_FB_PAGE_BD', ''),
            'fb_app_id'       => env('FACEBOOK_APP_ID', ''),
            'address' => [
                'street'  => '',
                'city'    => 'Dhaka',
                'state'   => 'Dhaka Division',
                'country' => 'Bangladesh',
                'postal'  => '1212',
            ],
            'geo' => [
                'latitude'  => '23.8103',
                'longitude' => '90.4125',
            ],
        ],

        'AE' => [
            'name'            => 'United Arab Emirates',
            'locale'          => 'ar',
            'fallback_locale' => 'en',
            'currency'        => 'AED',
            'currency_symbol' => 'د.إ',
            'primary_city'    => 'Dubai',
            'timezone'        => 'Asia/Dubai',
            'phone_code'      => '+971',
            'twitter_handle'  => env('SEO_TWITTER_AE', '@bdhandy_ae'),
            'fb_page_url'     => env('SEO_FB_PAGE_AE', ''),
            'fb_app_id'       => env('FACEBOOK_APP_ID', ''),
            'address' => [
                'street'  => '',
                'city'    => 'Dubai',
                'state'   => 'Dubai',
                'country' => 'United Arab Emirates',
                'postal'  => '',
            ],
            'geo' => [
                'latitude'  => '25.2048',
                'longitude' => '55.2708',
            ],
        ],

        'UZ' => [
            'name'            => 'Uzbekistan',
            'locale'          => 'uz',
            'fallback_locale' => 'ru',
            'currency'        => 'UZS',
            'currency_symbol' => 'UZS',
            'primary_city'    => 'Tashkent',
            'timezone'        => 'Asia/Tashkent',
            'phone_code'      => '+998',
            'twitter_handle'  => env('SEO_TWITTER_UZ', '@bdhandy_uz'),
            'fb_page_url'     => env('SEO_FB_PAGE_UZ', ''),
            'fb_app_id'       => env('FACEBOOK_APP_ID', ''),
            'address' => [
                'street'  => '',
                'city'    => 'Tashkent',
                'state'   => 'Tashkent Region',
                'country' => 'Uzbekistan',
                'postal'  => '100000',
            ],
            'geo' => [
                'latitude'  => '41.2995',
                'longitude' => '69.2401',
            ],
        ],

        'SA' => [
            'name'            => 'Saudi Arabia',
            'locale'          => 'ar',
            'fallback_locale' => 'en',
            'currency'        => 'SAR',
            'currency_symbol' => '﷼',
            'primary_city'    => 'Riyadh',
            'timezone'        => 'Asia/Riyadh',
            'phone_code'      => '+966',
            'twitter_handle'  => env('SEO_TWITTER_SA', '@bdhandy_sa'),
            'fb_page_url'     => env('SEO_FB_PAGE_SA', ''),
            'fb_app_id'       => env('FACEBOOK_APP_ID', ''),
            'address' => [
                'street'  => '',
                'city'    => 'Riyadh',
                'state'   => 'Riyadh Province',
                'country' => 'Saudi Arabia',
                'postal'  => '11564',
            ],
            'geo' => [
                'latitude'  => '24.7136',
                'longitude' => '46.6753',
            ],
        ],

        'QA' => [
            'name'            => 'Qatar',
            'locale'          => 'ar',
            'fallback_locale' => 'en',
            'currency'        => 'QAR',
            'currency_symbol' => '﷼',
            'primary_city'    => 'Doha',
            'timezone'        => 'Asia/Qatar',
            'phone_code'      => '+974',
            'twitter_handle'  => env('SEO_TWITTER_QA', '@bdhandy_qa'),
            'fb_page_url'     => env('SEO_FB_PAGE_QA', ''),
            'fb_app_id'       => env('FACEBOOK_APP_ID', ''),
            'address' => [
                'street'  => '',
                'city'    => 'Doha',
                'state'   => 'Ad Dawhah',
                'country' => 'Qatar',
                'postal'  => '',
            ],
            'geo' => [
                'latitude'  => '25.2854',
                'longitude' => '51.5310',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph Image Defaults
    |--------------------------------------------------------------------------
    */
    'og_image'        => '/images/og-default.jpg',
    'og_image_width'  => 1200,
    'og_image_height' => 630,

    /*
    |--------------------------------------------------------------------------
    | Twitter Card Type
    |--------------------------------------------------------------------------
    */
    'twitter_card' => 'summary_large_image',

    /*
    |--------------------------------------------------------------------------
    | Webmaster Verification (Google Search Console, Bing Webmaster Tools)
    |--------------------------------------------------------------------------
    */
    'webmaster' => [
        'google' => env('SEO_GOOGLE_VERIFY', null),
        'bing'   => env('SEO_BING_VERIFY', null),
        'yandex' => env('SEO_YANDEX_VERIFY', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Keywords Per Locale
    |--------------------------------------------------------------------------
    | Used as a fallback on pages that do not override keywords.
    */
    'keywords' => [
        'en' => [
            'home repair', 'plumber', 'electrician', 'AC technician', 'cleaner',
            'verified service providers', 'local services', 'service marketplace',
        ],
        'bn' => [
            'হোম রিপেয়ার', 'প্লাম্বার', 'ইলেকট্রিশিয়ান', 'এসি টেকনিশিয়ান', 'ক্লিনার',
            'যাচাইকৃত সেবাদাতা', 'স্থানীয় সেবা', 'সার্ভিস মার্কেটপ্লেস',
        ],
        'ar' => [
            'إصلاح المنزل', 'سباك', 'كهربائي', 'تقني تكييف', 'عامل نظافة',
            'مزودو خدمات موثوقون', 'الخدمات المحلية', 'سوق الخدمات',
        ],
        'uz' => [
            'uy ta\'mirlash', 'santexnik', 'elektrik', 'konditsioner ustasi', 'tozalovchi',
            'tasdiqlangan xizmat ko\'rsatuvchilar', 'mahalliy xizmatlar',
        ],
        'ru' => [
            'ремонт дома', 'сантехник', 'электрик', 'мастер по кондиционерам', 'уборщик',
            'проверенные исполнители', 'локальные услуги',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale → OG Locale Mapping
    |--------------------------------------------------------------------------
    */
    'og_locale_map' => [
        'en' => 'en_US',
        'bn' => 'bn_BD',
        'ar' => 'ar_AE',
        'uz' => 'uz_UZ',
        'ru' => 'ru_RU',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hreflang Locales
    |--------------------------------------------------------------------------
    | The locales exposed via <link rel="alternate" hreflang="..."> tags.
    | Expand this list as new languages go live.
    */
    'hreflang_locales' => [
        'en' => 'en',
        'bn' => 'bn',
    ],

];
