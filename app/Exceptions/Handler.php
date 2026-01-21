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
        $this->renderable(function (BusinessException $e, $request) {
            return result(
                null,
                $e->status_code,
                $e->getMessage()
            );
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            return result(
                null,
                401,
                'Неавторизованный пользователь'
            );
        });

        $this->renderable(function (ModelNotFoundException $e) {
            return result(
                null,
                404,
                'Объект не найден'
            );
        });
    }
}
