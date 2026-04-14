<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

$defaultPanel = config('admin.default_panel', 'filament');

if ($defaultPanel === 'filament' && config('admin.panels.filament.enabled', true)) {
    $filamentRoutes = base_path('routes/admin/filament.php');

    if (file_exists($filamentRoutes)) {
        require $filamentRoutes;
    }
}

if ($defaultPanel === 'inertia' && config('admin.panels.inertia.enabled', true)) {
    $inertiaRoutes = base_path('routes/admin/inertia.php');

    if (file_exists($inertiaRoutes)) {
        require $inertiaRoutes;
    }
}

