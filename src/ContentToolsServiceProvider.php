<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools;

use Eolica\LaravelContentTools\Loader\WithTranslationRepositoryLoader;
use Eolica\LaravelContentTools\PermissionHandler\PermissionHandler;
use Eolica\LaravelContentTools\PermissionHandler\TruePermissionHandler;
use Eolica\LaravelContentTools\Repository\FileTranslationRepository;
use Eolica\LaravelContentTools\Repository\TranslationRepository;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\ServiceProvider;

final class ContentToolsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/content-tools.php', 'content-tools');

        $this->registerTranslationLoader();

        $this->registerTranslationRepository();

        $this->registerPermissionHandler();

        $this->app->register(RouteServiceProvider::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->registerViews();
    }

    private function registerTranslationLoader(): void
    {
        $this->app->extend('translation.loader', function (Loader $loader, $app): Loader {
            return new WithTranslationRepositoryLoader($app->make(TranslationRepository::class), $loader);
        });
    }

    private function registerTranslationRepository(): void
    {
        $this->app->bindIf(TranslationRepository::class, function ($app): TranslationRepository {
            $path = $app->storagePath() . DIRECTORY_SEPARATOR . 'app'. str_replace($app->basePath(), '', $app->langPath());

            return new FileTranslationRepository($app->make('files'), $path);
        });
    }

    private function registerPermissionHandler(): void
    {
        $this->app->bindIf(PermissionHandler::class, function (): PermissionHandler {
            return new TruePermissionHandler();
        });
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'content-tools');

        $this->loadViewComponentsAs('content-tools', [
            'translation' => View\Components\Translation::class,
        ]);
    }

    private function bootForConsole(): void
    {
        $this->publishes([
            __DIR__ . '/../config/content-tools.php' => config_path('content-tools.php'),
        ], ['config', 'content-tools', 'content-tools:config']);

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/content-tools'),
        ], ['assets', 'content-tools', 'content-tools:assets']);

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/content-tools'),
        ], ['views', 'content-tools', 'content-tools:views']);
    }
}
