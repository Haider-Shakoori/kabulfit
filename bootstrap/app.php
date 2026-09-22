<?php

use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\RequirePermission;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'locale' => SetLocale::class,
            'active.user' => EnsureActiveUser::class,
            'permission' => RequirePermission::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request): string => route('login', [
            'locale' => $request->route('locale') ?? config('kabulfit.default_locale'),
        ]));

        $middleware->redirectUsersTo(fn (Request $request): string => route('account', [
            'locale' => $request->route('locale') ?? config('kabulfit.default_locale'),
        ]));

        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Central exception reporting/rendering will be expanded with domain workflows.
    })->create();
