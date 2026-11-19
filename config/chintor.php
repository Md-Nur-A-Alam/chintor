<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Bangla is the default locale and is served without a URL prefix. Every
    | other locale is served under its own prefix (e.g. "/en/courses").
    |
    */

    'default_locale' => 'bn',

    'locales' => [
        'bn' => [
            'name' => 'বাংলা',
            'short' => 'বাং',
            'hreflang' => 'bn-BD',
            'og_locale' => 'bn_BD',
        ],
        'en' => [
            'name' => 'English',
            'short' => 'EN',
            'hreflang' => 'en',
            'og_locale' => 'en_US',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Legal Identifiers
    |--------------------------------------------------------------------------
    |
    | The only business details kept in .env. Contact channels, staff and
    | media storage credentials live in the database and are managed from
    | the admin panel (see App\Services\SiteSettings).
    |
    */

    'legal' => [
        'trade_license' => env('CHINTOR_TRADE_LICENSE'),
        'tin' => env('CHINTOR_TIN'),
    ],

];
