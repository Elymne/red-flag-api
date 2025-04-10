<?php

namespace Infra\Router;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

/**
 * Manage token generation etc etc.
 * @link https://www.reddit.com/r/golang/comments/1e5ox6y/best_authentication_method_for_both_web_and/
 */
class AuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void {}
}
