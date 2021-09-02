<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\View\Components;

use Eolica\LaravelContentTools\PermissionHandler\PermissionHandler;
use Illuminate\View\Component;

final class Translation extends Component
{
    private const EDITABLE_VIEW = 'editable';
    private const STATIC_VIEW   = 'static';

    public function __construct(
        private PermissionHandler $permissionHandler,
        public string $key,
        public ?string $fixture = null
    ) {
    }

    public function render()
    {
        $view = $this->permissionHandler->check() ? self::EDITABLE_VIEW : self::STATIC_VIEW;

        return view("content-tools::components.translation.$view");
    }
}
