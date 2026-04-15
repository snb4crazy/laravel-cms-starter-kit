<?php

use Spatie\MediaLibrary\MediaCollections\Models\Media;

return [
    /*
    |--------------------------------------------------------------------------
    | Media disk
    |--------------------------------------------------------------------------
    |
    | Keep the boilerplate default explicit and conservative. The current admin
    | media workflow uses the same disk for post uploads, while projects can
    | override this later with MEDIA_DISK without changing application code.
    |
    */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | File size
    |--------------------------------------------------------------------------
    */
    'max_file_size' => 1024 * 1024 * 10, // 10 MB

    /*
    |--------------------------------------------------------------------------
    | Queue behavior
    |--------------------------------------------------------------------------
    |
    | These values mirror the package defaults so publishing this config does
    | not change runtime behavior for existing projects.
    |
    */
    'queue_connection_name' => env('QUEUE_CONNECTION', 'sync'),
    'queue_name' => env('MEDIA_QUEUE', ''),
    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),
    'queue_conversions_after_database_commit' => env('QUEUE_CONVERSIONS_AFTER_DB_COMMIT', true),

    /*
    |--------------------------------------------------------------------------
    | Model + delivery
    |--------------------------------------------------------------------------
    */
    'media_model' => Media::class,
    'version_urls' => env('MEDIA_VERSION_URLS', false),
    'image_driver' => env('IMAGE_DRIVER', 'gd'),
    'temporary_url_default_lifetime' => env('MEDIA_TEMPORARY_URL_DEFAULT_LIFETIME', 5),
    'enable_vapor_uploads' => env('ENABLE_MEDIA_LIBRARY_VAPOR_UPLOADS', false),
    'prefix' => env('MEDIA_PREFIX', ''),
    'force_lazy_loading' => env('FORCE_MEDIA_LIBRARY_LAZY_LOADING', true),
];

