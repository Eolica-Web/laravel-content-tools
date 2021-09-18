# Laravel Content Tools

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eolica/laravel-content-tools.svg?style=flat-square)](https://packagist.org/packages/eolica/laravel-content-tools) [![Total Downloads](https://img.shields.io/packagist/dt/eolica/laravel-content-tools.svg?style=flat-square)](https://packagist.org/packages/eolica/laravel-content-tools)

A Laravel package for editing your web frontend translations directly on the page. It uses [ContentTools](https://getcontenttools.com/), a Javascript library that provides a WYSIWYG editor that can be added to any page so we can edit the HTML content inline.

## Installation

You can install the package via [Composer](https://getcomposer.org/):

``` bash
composer require eolica/laravel-content-tools
```

Once installed, if you are not using automatic [package discovery](https://laravel.com/docs/8.x/packages#package-discovery), then you need to register the `Eolica\LaravelContentTools\ContentToolsServiceProvider` service provider in your `config/app.php`.

``` php
'providers' => [
    ...

    /*
    * Package Service Providers...
    */
    Eolica\LaravelContentTools\ContentToolsServiceProvider::class,

    ...
],
```

Or, alternatively, you may register it in your `App\Providers\AppServiceProvider`:

``` php
namespace App\Providers;

use Eolica\LaravelContentTools\ContentToolsServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(ContentToolsServiceProvider::class);
    }

    ...
}
```

### Public assets

Since this package uses public assets you must publish them by using the following artisan command:

``` bash
php artisan vendor:publish --tag=content-tools:assets --force
```

To keep the assets up-to-date and avoid issues in future updates, we recommend adding the command to the `post-autoload-dump` scripts in your composer.json file:

``` json
"scripts": {
    "post-autoload-dump": [
        "@php artisan vendor:publish --tag=content-tools:assets --force --ansi"
    ]
}
```

The package will inject the published assets into the `styles` and `scripts` stacks, so you must add these to your layout:

```blade
<html>
<head>
    ...
    @stack('styles')
</head>
<body>
    ...
    @stack('scripts')
</body>
</html>
```

## Configuration

You may publish the `content-tools` config file with the following command:

``` bash
php artisan vendor:publish --tag=content-tools:config
```

This will create a `config/content-tools.php` file in your app that you can modify to set your configuration.

This is the config file with the default values:

``` php
<?php

return [
    'routes' => [
        'prefix' => 'content-tools',
        'middleware' => ['web'],
    ],

    'editor' => [
        'default_tools' => [
            [
                'bold',
                'italic',
                'link',
                'align-left',
                'align-center',
                'align-right',
            ],
            [
                'heading',
                'subheading',
                'paragraph',
                'unordered-list',
                'ordered-list',
                'table',
                'indent',
                'unindent',
                'line-break',
            ],
            [
                'preformatted',
            ],
            [
                'undo',
                'redo',
                'remove',
            ],
        ],

        'default_video_width'   => 400,
        'default_video_height'  => 300,

        'highlight_hold_duration' => 2000,

        'min_crop' => 10,

        'restricted_attributes' => [
            '*'         => [],
            'img'       => ['height', 'width', 'src', 'data-ce-max-width', 'data-ce-min-width'],
            'iframe'    => ['height', 'width'],
        ],
    ],
];
```

##### Routes
The `routes` option is where you may specify the prefix applied to the package routes if for some reason conflicts with other routes in your application, also you may specify a list of middleware for these routes, by default the `web` group middleware is applied.

##### Editor
The `editor` option is where you may specify the ContentTools [settings](https://getcontenttools.com/api/content-tools#settings).

## Usage

All you need to do is to add the `content-tools-translation` component to your Blade view.

``` blade
<x-content-tools-translation key="web.pages.home.content" />
```

The `key` property maps to a [short key](https://laravel.com/docs/8.x/localization#using-short-keys) defined in your translation files.

You may forward any [extra attributes](https://laravel.com/docs/8.x/blade#component-attributes) to the component, for example, we could add the `class` attribute to style the content:

``` blade
<x-content-tools-translation key="web.pages.home.content" class="content" />
```

### Fixtures

Imagine we have a home page in our website with a title inside an `h1` html tag, we want this title to be translatable but we don't want our users to potentially break the `h1` tag, affecting SEO negatively.

This is what [fixtures](https://getcontenttools.com/api/content-edit#fixture) allow us to do. Just add the `fixture` attribute with the html tag that you want to be fixed as the value:

``` blade
<x-content-tools-translation key="web.pages.home.title" fixture="h1" />
```

> We recommend always using fixtures to prevent users from breaking the page layout, and use non-fixture translations only when necessary.

### Permissions

By default this package uses the `Eolica\LaravelContentTools\PermissionHandler\TruePermissionHandler` that allows anyone that enters your website to edit your translations. This is very useful in local development but in a production environment you will most likely need some kind of authentication check.

This package ships with the `Eolica\LaravelContentTools\PermissionHandler\AuthGuardCheckPermissionHandler` that checks if a user of a particular authentication guard is authenticated.

With that in mind, we could bind this implementation in our `App\Providers\AppServiceProvider` only when the application is not running in the local environment:

``` php
namespace App\Providers;

use Eolica\LaravelContentTools\PermissionHandler\AuthGuardCheckPermissionHandler;
use Eolica\LaravelContentTools\PermissionHandler\PermissionHandler;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (!$this->app->isLocal()) {
            $this->app->bind(PermissionHandler::class, function ($app): PermissionHandler {
                return new AuthGuardCheckPermissionHandler(
                    $app->make('auth')->guard('backend')
                );
            });
        }
    }

    ...
}
```

Now, in production, only users authenticated via the `backend` guard will be able to edit translations. In local development, it will fallback to the `Eolica\LaravelContentTools\PermissionHandler\TruePermissionHandler` implementation so we can test easily without the need of authenticating.

## Creating your own permission handler

If the `Eolica\LaravelContentTools\PermissionHandler\AuthGuardCheckPermissionHandler` is not enough for your use case, you can make your own permission handler implementation.

A permission handler can be any class that implements the `Eolica\LaravelContentTools\PermissionHandler\PermissionHandler` interface:

``` php
namespace Eolica\LaravelContentTools\PermissionHandler;

interface PermissionHandler
{
    public function check(): bool;
}
```

For registering your own permission handler, you may override our default implementation from the service container in your `App\Providers\AppServiceProvider`:

``` php
namespace App\Providers;

use App\ContentTools\PermissionHandler\YourOwnPermissionHandler;
use Eolica\LaravelContentTools\PermissionHandler\PermissionHandler;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PermissionHandler::class, function ($app): PermissionHandler {
            return new YourOwnPermissionHandler();
        });
    }

    ...
}
```

Since our package uses the `bindIf` method that registers a binding if it hasn't already been registered, your custom permission handler will take precedence over our default implementation.

## Creating your own translation repository

This package ships with the `Eolica\LaravelContentTools\Repository\FileTranslationRepository` that persists and loads the translations from php files inside the `storage/app/resources/lang` folder. If you want to persist your translations in another source (database, csv, yaml, etc...), you can create your own translation repository.

A translation repository can be any class that implements the `Eolica\LaravelContentTools\Repository\TranslationRepository` interface:

``` php
namespace Eolica\LaravelContentTools\Repository;

interface TranslationRepository
{
    public function save(string $locale, string $group, string $key, string $value): void;

    public function load(string $locale, string $group): array;
}
```

For registering your own translation repository, you may override our default implementation from the service container in your `App\Providers\AppServiceProvider`:

``` php
namespace App\Providers;

use App\ContentTools\Repository\YourOwnTranslationRepository;
use Eolica\LaravelContentTools\Repository\TranslationRepository;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TranslationRepository::class, function ($app): TranslationRepository {
            return new YourOwnTranslationRepository();
        });
    }

    ...
}
```

Since our package uses the `bindIf` method that registers a binding if it hasn't already been registered, your custom repository will take precedence over our default implementation.

## Currently missing features

* Translations with [parameters](https://laravel.com/docs/8.x/localization#replacing-parameters-in-translation-strings) support, we still have to come with a good solution for this, any help would be appreciated
* Image upload support for more complex translations

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover a security vulnerability within this package, please send an email at info@eolicadigital.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
