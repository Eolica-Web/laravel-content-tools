<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\Tests;

use Eolica\LaravelContentTools\ContentToolsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ContentToolsServiceProvider::class,
        ];
    }
}
