<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Admin Panel
    |--------------------------------------------------------------------------
    |
    | The template defaults to Filament for speed. Switch to Inertia per
    | project by setting ADMIN_PANEL=inertia in the environment.
    |
    */
    'default_panel' => env('ADMIN_PANEL', 'filament'),

    /*
    |--------------------------------------------------------------------------
    | Panel Feature Flags
    |--------------------------------------------------------------------------
    |
    | These flags let the template load only the selected adapter in routes
    | and providers while still allowing both code paths to exist.
    |
    */
    'panels' => [
        'filament' => [
            'enabled' => env('ADMIN_FILAMENT_ENABLED', true),
            'path' => env('ADMIN_FILAMENT_PATH', 'admin'),
        ],
        'inertia' => [
            'enabled' => env('ADMIN_INERTIA_ENABLED', true),
            'path' => env('ADMIN_INERTIA_PATH', 'dashboard'),
        ],
    ],
];

