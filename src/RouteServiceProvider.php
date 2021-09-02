<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * @property \Illuminate\Foundation\Application $app
 */
final class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!$this->app->routesAreCached()) {
            $this->registerRoutes();
        }
    }

    private function registerRoutes(): void
    {
        Route::name('content_tools.')->group(function () {
            Route::group(
                $this->routeConfiguration(),
                fn () => $this->loadRoutesFrom(__DIR__ . '/../routes/web.php')
            );
        });
    }

    private function routeConfiguration()
    {
        $config = $this->app->make('config')->get('content-tools.routes');

        return [
            'prefix'        => $config['prefix'],
            'middleware'    => $config['middleware'],
        ];
    }
}
