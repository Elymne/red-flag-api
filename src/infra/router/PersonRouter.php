<?php

namespace Infra\Router;

use Pecee\SimpleRouter\SimpleRouter;

class PersonRouter
{
    private static string $base = "/persons";

    public static function defineRoutes(): void
    {
        SimpleRouter::get(self::$base . "/", function () {
            $firstname = $_GET["firstname"];
            $surname = $_GET["surname"];
            $fullname = $_GET["fullname"];
            $cities = $_GET["cities"];
            http_response_code(200);
            echo json_encode("Return many.");
            exit;
        });

        SimpleRouter::get(self::$base . "/{id}", function ($id) {
            echo $id;
            http_response_code(200);
            echo json_encode("Return unique person.");
            exit;
        });
    }
}
