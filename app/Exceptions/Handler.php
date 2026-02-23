<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\BusinessException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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

        $this->renderable(function (AuthorizationException $e, $request) {
            Log::channel('forensics')->error('FILAMENT_AUTHZ_FAIL', [
                'path' => $request->path(),
                'method' => $request->method(),
                'user_id' => optional($request->user())->id,
                'user_class' => $request->user() ? get_class($request->user()) : null,
                'ability' => method_exists($e, 'ability') ? $e->ability() : null,
                'arguments' => method_exists($e, 'arguments') ? $e->arguments() : null,
                'message' => $e->getMessage(),
            ]);

            return response('Forbidden (see logs)', 403);
        });

        $this->renderable(function (HttpExceptionInterface $e, $request) {
            if ($e->getStatusCode() !== 403) {
                return null;
            }

            $trace = $e->getTrace();
            $top = $trace[0] ?? [];

            Log::channel('forensics')->error('HTTP_403_ABORT', [
                'path' => $request->path(),
                'method' => $request->method(),
                'user_id' => optional($request->user())->id,
                'user_class' => $request->user() ? get_class($request->user()) : null,
                'message' => $e->getMessage(),
                'top_file' => $top['file'] ?? null,
                'top_line' => $top['line'] ?? null,
                'top_function' => $top['function'] ?? null,
            ]);

            return null;
        });
    }
}
