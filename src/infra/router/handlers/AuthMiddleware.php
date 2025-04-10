<?php

namespace Infra\Router;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

/**
 * Manage token generation etc etc.
 */
class AuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        echo "OUI";
    }
}
