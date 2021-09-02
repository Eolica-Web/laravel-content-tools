<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\PermissionHandler;

use Illuminate\Contracts\Auth\Guard;

final class AuthGuardCheckPermissionHandler implements PermissionHandler
{
    public function __construct(private Guard $guard)
    {
    }

    public function check(): bool
    {
        return $this->guard->check();
    }
}
