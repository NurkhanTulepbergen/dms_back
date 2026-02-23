<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FilamentDebugHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $webUser = auth('web')->user();
        $filamentUser = Filament::auth()->user();

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Debug-App-Reached', '1');
        $response->headers->set('X-Debug-Route-Name', (string) optional($request->route())->getName());
        $response->headers->set('X-Debug-Web-User-Id', (string) ($webUser?->id ?? ''));
        $response->headers->set('X-Debug-Filament-User-Id', (string) ($filamentUser?->id ?? ''));
        $response->headers->set('X-Debug-Status', (string) $response->getStatusCode());

        Log::channel('forensics')->info('FILAMENT_DEBUG_HEADERS', [
            'path' => $request->path(),
            'route' => optional($request->route())->getName(),
            'status' => $response->getStatusCode(),
            'web_user_id' => $webUser?->id,
            'filament_user_id' => $filamentUser?->id,
        ]);

        return $response;
    }
}
