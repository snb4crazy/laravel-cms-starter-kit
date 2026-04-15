<?php

use Illuminate\Support\Facades\Route;

// Filament-specific admin routes can be registered here when Filament is installed.
Route::prefix(config('admin.panels.filament.path', 'admin'))
    ->middleware(['web'])
    ->group(function (): void {
        // Intentionally left empty in the boilerplate scaffold.
    });

