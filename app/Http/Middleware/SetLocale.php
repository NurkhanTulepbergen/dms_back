<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['kk', 'ru', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    private function resolveLocale(Request $request): ?string
    {
        $requestedLocale = $request->header('Accept-Language')
            ?: $request->query('locale')
            ?: $request->input('locale');

        if (! is_string($requestedLocale) || $requestedLocale === '') {
            return null;
        }

        $locale = strtolower(strtok($requestedLocale, ',') ?: '');
        $locale = preg_split('/[-;]/', $locale)[0] ?? $locale;

        return in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : null;
    }
}
