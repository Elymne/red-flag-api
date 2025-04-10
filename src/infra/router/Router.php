<?php

namespace Infra\Router;

use Core\Result;
use Domain\Gateways\RouterGateway;
use Domain\Usecases\RunMigrations;
use Infra\Di\Container;
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

        SimpleRouter::get('/migrations', function () {
            if ($_ENV["MODE"] == "develop") {
                /** @var Result */
                $result = Container::get()->resolve(RunMigrations::class)->perform();
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            }

            http_response_code(404);
            echo "This route does not exists.";
            exit;
        });

        // Start the routing
        SimpleRouter::start();
    }
}
