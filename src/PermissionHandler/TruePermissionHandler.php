<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\PermissionHandler;

final class TruePermissionHandler implements PermissionHandler
{
    public function check(): bool
    {
        return true;
    }
}
