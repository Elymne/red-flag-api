<?php

namespace Infra;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Infra\GeoApiDatasource;
use Throwable;

use function FastRoute\simpleDispatcher;

class Router
{
    static public function useRoutes()
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $route) {
            // Simple entry route.
            $route->addRoute("GET", "/", function () {
                http_response_code(200);
                echo json_encode("This is Red Flag API.");
                exit;
            });

            // Route : /redflags
            // Fetch all redflags, you can use body query like theses ones :
            // - firstname | The fristname only.
            // - surname | The surname only.
            // - fullname | The full name lenght.
            // - cities | Must be separated by "," if more than one.
            $route->addRoute("GET", "/redflags", function () {
                $firstname = $_GET["firstname"];
                $surname = $_GET["surname"];
                $fullname = $_GET["fullname"];
                $cities = $_GET["cities"];

                echo json_encode("Return all red flags.");
                exit;
            });

            $route->addRoute("GET", "/redflags/{id}", function (mixed $vars) {
                echo $vars["id"] . "\n";
                echo json_encode("Return all red flags.");
                exit;
            });

            // Fetch all route from Remote repos.
            $route->addRoute("GET", "/cities", function () {
                $name =  $_GET["name"] ?? null;
                $id =  $_GET["id"] ?? null;

                try {
                    $remoteCityRepository = new GeoApiDatasource();
                    $result = $remoteCityRepository->findMany(name: $name, id: $id);
                    http_response_code(200);
                    echo json_encode($result);
                    exit;
                } catch (Throwable $err) {
                    http_response_code(500);

                    echo $err;
                    exit;

                    echo json_encode(array(
                        "message" => "Exception : An error occured while fetching cities.",
                        "trace" => $err
                    ));
                    exit;
                }
            });
        });

        $httpMethod = $_SERVER["REQUEST_METHOD"];
        $uri = $_SERVER["REQUEST_URI"];
        if (false !== $pos = strpos($uri, "?")) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                echo json_encode("Page not found");
                exit;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                echo json_encode("Not allowed. Check : " . $allowedMethods);
                exit;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $handler($vars);
        }
    }
}
