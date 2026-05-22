<?php

if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth($string, $start, $width, $trimmarker = '', $encoding = null) {
        $string = (string)$string;
        $start = (int)$start;
        $width = (int)$width;
        $trimmarker = (string)$trimmarker;

        $len = strlen($string);
        if ($len <= $width) {
            return $string;
        }

        $sub = substr($string, $start, $width);
        return $sub . $trimmarker;
    }
}

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'branch.restriction' => \App\Http\Middleware\BranchRestriction::class,
            'clinic.role' => \App\Http\Middleware\EnsureClinicRole::class,
            'clinic.session' => \App\Http\Middleware\SyncClinicSession::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
