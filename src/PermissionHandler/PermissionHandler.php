<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\PermissionHandler;

interface PermissionHandler
{
    public function check(): bool;
}
