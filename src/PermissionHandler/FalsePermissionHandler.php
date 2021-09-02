<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\PermissionHandler;

final class FalsePermissionHandler implements PermissionHandler
{
    public function check(): bool
    {
        return false;
    }
}
