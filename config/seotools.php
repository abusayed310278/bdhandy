<?php

/**
 * @see https://github.com/artesaos/seotools
 */

return [
    'inertia' => env('SEO_TOOLS_INERTIA', false),

    'meta' => [
        'defaults' => [
            'title'       => false,
            'titleBefore' => false,
            'description' => false,
            'separator'   => ' — ',
            'keywords'    => [],
            'canonical'   => false,
            'robots'      => 'index, follow',
        ],
        'webmaster_tags' => [
            'google'    => env('SEO_GOOGLE_VERIFY', null),
            'bing'      => env('SEO_BING_VERIFY', null),
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => env('SEO_YANDEX_VERIFY', null),
            'norton'    => null,
        ],
        'add_notranslate_class' => false,
    ],

    'opengraph' => [
        'defaults' => [
            'title'       => false,
            'description' => false,
            'url'         => null,
            'type'        => 'website',
            'site_name'   => env('APP_NAME', 'BDHandy'),
            'images'      => [],
        ],
    ],

    'twitter' => [
        'defaults' => [
            'card' => 'summary_large_image',
        ],
    ],

    'json-ld' => [
        'defaults' => [
            'title'       => false,
            'description' => false,
            'url'         => null,
            'type'        => false,
            'images'      => [],
        ],
    ],
];
