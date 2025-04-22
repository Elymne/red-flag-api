<?php

declare(strict_types=1);

namespace Infra\Router;

use Core\Result;
use Core\Container;
use Domain\Gateways\RouterGateway;
use Domain\Usecases\RunMigrations;
use Pecee\SimpleRouter\SimpleRouter;
use OpenApi\Attributes as OA;

#[OA\Info(title: "Red-flags API", version: "0.1", description: "API REST for RedFlags mobile apps (Android).")]
#[OA\Contact(email: "sacha.djurdjevic@gmail.com")]
class Router implements RouterGateway
{
    public function start(): void
    {
        SimpleRouter::group([
            "middleware" => CustomMiddleware::class,
            "exceptionHandler" => ExceptionHandler::class,
        ], function () {
            // * Routing group api.
            SimpleRouter::group(
                ["prefix" => "/api",],
                function () {
                    ZoneRouter::defineRoutes();
                    PersonRouter::defineRoutes();
                    ActivityRouter::defineRoutes();
                    CompanyRouter::defineRoutes();
                    SimpleRouter::get("/swagger", function () {
                        readfile(ROOT_PATH . "/public/docs/index.html");
                        exit;
                    });
                    SimpleRouter::get("/", function () {
                        http_response_code(200);
                        echo "You're hiting Redflags API.";
                        exit;
                    });
                }
            );
        });

        // * Migrate Dev database (dev only).
        SimpleRouter::get("/migrations", function () {
            if ($_ENV["MODE"] == "develop") {
                /** @var Result */
                $result = Container::get()->resolve(RunMigrations::class)->perform();
                header("Content-Type: application/json");
                http_response_code($result->code);
                echo json_encode($result->response);
                exit;
            }
            http_response_code(404);
            echo "This route does not exists.";
            exit;
        });

        // * Start the routing
        SimpleRouter::start();
    }
}
