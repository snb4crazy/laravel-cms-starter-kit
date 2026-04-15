<?php

use Illuminate\Support\Facades\Route;

// Inertia-specific admin routes can be registered here when the Inertia stack is installed.
Route::prefix(config('admin.panels.inertia.path', 'dashboard'))
    ->middleware(['web'])
    ->group(function (): void {
        // Intentionally left empty in the boilerplate scaffold.
    });

