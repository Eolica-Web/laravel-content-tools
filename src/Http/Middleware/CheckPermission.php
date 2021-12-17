<?php

declare(strict_types=1);

namespace Eolica\LaravelContentTools\Http\Middleware;

use Closure;
use Eolica\LaravelContentTools\PermissionHandler\PermissionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class CheckPermission
{
    public function __construct(private PermissionHandler $permissionHandler)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->permissionHandler->check()) {
            throw new AccessDeniedHttpException('');
        }

        return $next($request);
    }
}
