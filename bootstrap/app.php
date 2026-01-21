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
        $exceptions->renderable(function (BusinessException $e, $request) {
            return result(
                null,
                $e->status_code,
                $e->getMessage()
            );
        });

        $exceptions->renderable(function (AuthenticationException $e, $request) {
            return result(null, 401, 'Неавторизованный пользователь');
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($e->getPrevious() instanceof ModelNotFoundException) {
                return result(null, 404, 'Объект не найден');
            }

            return result(null, 404, 'Маршрут не найден');
        });
    }) 
    ->create();
