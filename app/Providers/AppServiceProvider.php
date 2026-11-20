<?php

namespace App\Providers;

use App\Models\Program;
use App\Models\User;
use App\Services\SiteSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const NAV_PROGRAMS_CACHE_KEY = 'nav.programs';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SiteSettings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admins and developers may do everything; everyone else needs a matching permission.
        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->hasFullAccess()) {
                return true;
            }

            return $user->hasPermission($ability) ? true : null;
        });

        // Public forms: enough for a family filling forms honestly, too few for spam bots.
        RateLimiter::for('public-forms', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

        // Program list for the header menu; cleared whenever a program changes.
        Program::saved(fn () => Cache::forget(self::NAV_PROGRAMS_CACHE_KEY));
        Program::deleted(fn () => Cache::forget(self::NAV_PROGRAMS_CACHE_KEY));

        View::composer('components.site.header', function (\Illuminate\View\View $view): void {
            $view->with('navPrograms', Cache::remember(
                self::NAV_PROGRAMS_CACHE_KEY,
                now()->addHour(),
                fn () => Program::query()->published()->orderBy('sort_order')->get(['id', 'slug', 'name', 'icon']),
            ));
        });

        // Public layout pieces read contact channels and legal details from settings (cached).
        View::composer(
            ['components.layouts.app', 'components.site.*', 'site.*'],
            function (\Illuminate\View\View $view): void {
                $settings = app(SiteSettings::class);

                $view->with([
                    'siteContact' => $settings->contact(),
                    'siteLegal' => $settings->legal(),
                ]);
            },
        );
    }
}
