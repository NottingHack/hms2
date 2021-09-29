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
