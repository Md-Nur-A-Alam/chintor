<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureMarketingAttribution
{
    public const SessionKey = 'attribution';

    private const UTM_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];

    /**
     * Remember the first landing page, referrer and UTM tags of a visit so
     * every lead records which campaign brought it (requirements §7).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && ! $request->session()->has(self::SessionKey)) {
            $request->session()->put(self::SessionKey, [
                'landing_page' => mb_substr($request->fullUrl(), 0, 255),
                'referrer' => mb_substr((string) $request->headers->get('referer'), 0, 255) ?: null,
                ...collect(self::UTM_KEYS)
                    ->mapWithKeys(fn (string $key) => [$key => mb_substr((string) $request->query($key), 0, 255) ?: null])
                    ->all(),
            ]);
        }

        return $next($request);
    }
}
