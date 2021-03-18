<?php

return [
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
    'email_domain' => env('BRANDING_EMAIL_DOMAIN', '@nottinghack.org.uk'),

    /*
    |--------------------------------------------------------------------------
    | Address details
    |--------------------------------------------------------------------------
    */
    'address_1' => env('BRANDING_ADDRESS_1', 'Unit F6 Roden House'),
    'address_2' => env('BRANDING_ADDRESS_2', 'Roden Street'),
    'address_3' => env('BRANDING_ADDRESS_3', null),
    'city' => env('BRANDING_CITY', 'Nottingham'),
    'county' => env('BRANDING_COUNTY', null),
    'postcode' => env('BRANDING_POSTCODE', 'NG3 1JH'),

    /*
    |--------------------------------------------------------------------------
    | Social
    |--------------------------------------------------------------------------
    | Setting env var to equal null will hide link
    */
    'social_networks' => [
        [
            'link' => env('SOCIAL_TWITTER', 'https://twitter.com/HSNOTTS'),
            'icon' => 'fab fa-twitter',
        ],
        [
            'link' => env('SOCIAL_FACEBOOK', 'https://www.facebook.com/nottinghack/'),
            'icon' => 'fab fa-facebook',
        ],
        [
            'link' => env('SOCIAL_GOOGLE', 'https://groups.google.com/group/nottinghack?hl=en'),
            'icon' => 'fab fa-google',
        ],
        [
            'link' => env('SOCIAL_FLICKR', 'https://www.flickr.com/photos/nottinghack'),
            'icon' => 'fab fa-flickr',
        ],
        [
            'link' => env('SOCIAL_YOUTUBE', 'https://www.youtube.com/user/nottinghack'),
            'icon' => 'fab fa-youtube',
        ],
    ],

];
