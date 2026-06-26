<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;

/**
 * Custom Filament authentication middleware.
 *
 * Instead of redirecting unauthenticated users to the login page,
 * this returns a 404 to hide the existence of the admin panel entirely.
 * The only way to access the login form is via the secret URL /giaphuoc57hv.
 */
class AdminAuthenticate extends FilamentAuthenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function unauthenticated($request, array $guards): void
    {
        abort(404);
    }
}
