<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS if in staging or production
        if (App::environment(['staging', 'production'])) {
            URL::forceScheme('https');

            if (! app()->runningInConsole() && ! env('APP_URL')) {
                $host = request()->getSchemeAndHttpHost();

                config(['app.url' => $host]);
                config(['sanctum.stateful' => [parse_url($host, PHP_URL_HOST)]]);
            }
        }

        // Dynamically add current request host to Sanctum stateful domains
        if (! app()->runningInConsole()) {
            $currentHost = request()->getHost();
            $statefulDomains = config('sanctum.stateful', []);

            if (! in_array($currentHost, $statefulDomains)) {
                $statefulDomains[] = $currentHost;
                config(['sanctum.stateful' => $statefulDomains]);
            }
        }
    }
}
