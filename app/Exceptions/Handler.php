<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\BusinessException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        // Business логика → только для API
        $this->renderable(function (BusinessException $e, $request) {
            if ($request->is('api/*')) {
                return result(
                    null,
                    $e->status_code,
                    $e->getMessage()
                );
            }

            return null; // для web пусть Laravel обработает сам
        });

        // Неавторизованный → API JSON, Web стандартный redirect
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return result(
                    null,
                    401,
                    'Неавторизованный пользователь'
                );
            }

            return null;
        });

        // Модель не найдена → только для API
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return result(
                    null,
                    404,
                    'Объект не найден'
                );
            }

            return null;
        });
    }
}
