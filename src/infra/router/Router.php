<?php

declare(strict_types=1);

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
            "middleware" => CustomMiddleware::class,
            "exceptionHandler" => ExceptionHandler::class,
        ], function () {
            // Route : /
            // Simple entry route that signal that client has reach Redflags API.
            SimpleRouter::get('/', function () {
                http_response_code(200);
                echo "You're hiting Redflags API.";
                exit;
            });

            SimpleRouter::get('/test', function () {
                if ($_ENV["MODE"] == "develop") {
                }

                http_response_code(404);
                echo "This route does not exists.";
                exit;
            });

            // Implements all Zone routes.
            ZoneRouter::defineRoutes();

            // Implements all Persons routes.
            PersonRouter::defineRoutes();
        });

        SimpleRouter::get('/migrations', function () {
            if ($_ENV["MODE"] == "develop") {
                /** @var Result */
                $result = Container::get()->resolve(RunMigrations::class)->perform();
                header('Content-Type: application/json');
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
