<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS feature flags
    |--------------------------------------------------------------------------
    |
    | Keep optional editorial capabilities opt-in so the boilerplate stays
    | conservative. Individual projects can enable features as needed.
    |
    */
    'features' => [
        'media' => [
            'post_gallery' => env('CMS_POST_GALLERY_ENABLED', false),
        ],
    ],
];

