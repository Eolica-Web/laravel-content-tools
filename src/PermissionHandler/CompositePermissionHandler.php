<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\PermissionHandler;

final class CompositePermissionHandler implements PermissionHandler
{
    /** @var PermissionHandler[] $handlers */
    private array $handlers;

    public function __construct(PermissionHandler ...$handlers)
    {
        $this->handlers = $handlers;
    }

    public function check(): bool
    {
        foreach ($this->handlers as $handler) {
            if (!$handler->check()) {
                return false;
            }
        }

        return true;
    }
}
