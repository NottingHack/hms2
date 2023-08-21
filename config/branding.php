<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    | Theme for CSS and logo/favicon
    */
    'theme' => env('THEME', 'nottinghack'),
    'theme_email' => env('THEME_EMAIL', 'hms'),

    /*
    |--------------------------------------------------------------------------
    | Company info
    |--------------------------------------------------------------------------
    */
    'space_name' => env('BRANDING_SPACE_NAME', 'Nottingham Hackspace'),
    'community_name' => env('BRANDING_COMMUNITY_NAME', 'Nottinghack'),
    // hackspace, hackerspace, makerspace - Lowercase to be used in a sentence
    'space_type' => env('BRANDING_SPACE_TYPE', 'hackspace'),
    'company_name' => env('BRANDING_COMPANY_NAME', 'Nottingham Hackspace Ltd'),
    'company_number' => env('BRANDING_COMPANY_NUMBER', '07766826'),
    'main_domain' => env('BRANDING_MAIN_DOMAIN', 'nottinghack.org.uk'),
    'email_domain' => env('BRANDING_EMAIL_DOMAIN', '@' . env('BRANDING_MAIN_DOMAIN', 'nottinghack.org.uk')),

    /*
    |--------------------------------------------------------------------------
    | Registered Address details
    |--------------------------------------------------------------------------
    | Registered address of the company
    */
    'registered_address_1' => env('BRANDING_REGISTERED_ADDRESS_1', 'Unit F6 Roden House'),
    'registered_address_2' => env('BRANDING_REGISTERED_ADDRESS_2', 'Roden Street'),
    'registered_address_3' => env('BRANDING_REGISTERED_ADDRESS_3', null),
    'registered_city' => env('BRANDING_REGISTERED_CITY', 'Nottingham'),
    'registered_county' => env('BRANDING_REGISTERED_COUNTY', null),
    'registered_postcode' => env('BRANDING_REGISTERED_POSTCODE', 'NG3 1JH'),

    /*
    |--------------------------------------------------------------------------
    | Space Address details
    |--------------------------------------------------------------------------
    | If the space address is different form the above registered company address
    */
    'space_address_1' => env('BRANDING_SPACE_ADDRESS_1', env('BRANDING_REGISTERED_ADDRESS_1', 'Unit F6 Roden House')),
    'space_address_2' => env('BRANDING_SPACE_ADDRESS_2', env('BRANDING_REGISTERED_ADDRESS_2', 'Roden Street')),
    'space_address_3' => env('BRANDING_SPACE_ADDRESS_3', env('BRANDING_REGISTERED_ADDRESS_3', null)),
    'space_city' => env('BRANDING_SPACE_CITY', env('BRANDING_REGISTERED_CITY', 'Nottingham')),
    'space_county' => env('BRANDING_SPACE_COUNTY', env('BRANDING_REGISTERED_COUNTY', null)),
    'space_postcode' => env('BRANDING_SPACE_POSTCODE', env('BRANDING_REGISTERED_POSTCODE', 'NG3 1JH')),
    'space_latitude' => env('BRANDING_LATITUDE', 52.9557),
    'space_longitude' => env('BRANDING_LONGITUDE', -1.1350),

    /*
    |--------------------------------------------------------------------------
    | Social
    |--------------------------------------------------------------------------
    | Setting env var to equal null will hide link
    */
    'social_networks' => [
        'twitter' => [
            'link' => 'https://twitter.com/' . env('SOCIAL_TWITTER', 'HSNOTTS'),
            'icon' => 'fab fa-twitter',
            'handle' => '@' . env('SOCIAL_TWITTER', 'HSNOTTS'),
        ],
        'mastodon' => [
            'link' => env('SOCIAL_MASTODON_USERNAME', 'nottinghack') ? 'https://' . env('SOCIAL_MASTODON_DOMAIN', 'hachyderm.io') . '/@' . env('SOCIAL_MASTODON_USERNAME', 'nottinghack') : null,
            'icon' => 'fab fa-mastodon',
            'handle' =>'@' . env('SOCIAL_MASTODON_USERNAME', 'nottinghack') . '@' . env('SOCIAL_MASTODON_DOMAIN', 'hachyderm.io'),,
        ],
        'facebook' => [
            'link' => env('SOCIAL_FACEBOOK', 'https://www.facebook.com/nottinghack/'),
            'icon' => 'fab fa-facebook',
        ],
        'google_groups' => [
            'link' => 'https://groups.google.com/group/' . env('SOCIAL_GOOGLE', 'nottinghack') . '?hl=en',
            'icon' => 'fab fa-google',
            'email' => env('SOCIAL_GOOGLE', 'nottinghack') . '@googlegroups.com',
        ],
        'flickr' => [
            'link' => env('SOCIAL_FLICKR', 'https://www.flickr.com/photos/nottinghack'),
            'icon' => 'fab fa-flickr',
        ],
        'youtube' => [
            'link' => env('SOCIAL_YOUTUBE', 'https://www.youtube.com/user/nottinghack'),
            'icon' => 'fab fa-youtube',
        ],
    ],

];
