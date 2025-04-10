<?php

namespace Infra\Router;

use Domain\Gateways\RouterGateway;
use Pecee\SimpleRouter\SimpleRouter;

class Router implements RouterGateway
{
    public function start(): void
    {
        // Group all routes to trigger my middleware and catch errors.
        SimpleRouter::group([
            "middleware" => AuthMiddleware::class,
            "exceptionHandler" => ExceptionHandler::class,
        ], function () {
            // Route : /
            // Simple entry route that signal that client has reach Redflags API.
            SimpleRouter::get('/', function () {
                http_response_code(200);
                echo "You're hiting Redflags API.";
                exit;
            });

            // Implements all City routes.
            CityRouter::defineRoutes();

            // Implements all Persons routes.
            PersonRouter::defineRoutes();
        });

        // Start the routing
        SimpleRouter::start();
    }
}
