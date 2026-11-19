<?php

namespace App\Http\Middleware;

use App\Support\Localization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the locale that the matched route group was registered for.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $locale = null): Response
    {
        $locale = $locale !== null && Localization::isSupported($locale)
            ? $locale
            : Localization::defaultLocale();

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
