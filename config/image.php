<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Image Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default image processing driver that will be
    | used by the framework. You may set this to any of the drivers defined
    | in the "drivers" array below.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => env('IMAGE_DRIVER', 'gd'),

    // index size
    'index-image-sizes' => [
        'large' => [
            'width' => 800,
            'height' => 450,
        ],
        'medium' => [
            'width' => 400,
            'height' => 300,
        ],
        'small' => [
            'width' => 80,
            'height' => 60,
        ],

    ],

    'default-current-index-image' => 'medium',

    'cache-image-sizes' => [
        'large' => [
            'width' => 800,
            'height' => 450,
        ],
        'medium' => [
            'width' => 400,
            'height' => 300,
        ],
        'small' => [
            'width' => 80,
            'height' => 60,
        ],
    ],

    'default-current-cache-image' => 'medium',

    'cache-life-time' => 43200,

    'default-profile-image' => 'https://cdn.varzeshpod.com/static/profile.png',
    'default-background-image' => 'https://cdn.varzeshpod.com/static/background.png',
];
