<?php

require_once __DIR__.'/../app/Helpers/result.php';

use App\Exceptions\BusinessException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // BusinessException → только API
        $exceptions->renderable(function (BusinessException $e, $request) {
            if ($request->is('api/*')) {
                return result(
                    null,
                    $e->status_code,
                    $e->getMessage()
                );
            }

            return null;
        });

        // Authentication → API JSON, Web стандартный редирект Filament
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return result(null, 401, 'Неавторизованный пользователь');
            }

            return null;
        });

        // NotFound → JSON только для API
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {

            if ($request->is('api/*')) {
                if ($e->getPrevious() instanceof ModelNotFoundException) {
                    return result(null, 404, 'Объект не найден');
                }

                return result(null, 404, 'Маршрут не найден');
            }

            // для Filament / web — стандартный Laravel HTML 404
            return null;
        });

    })
    ->create();
