<?php

namespace Infra\Router;

use Infra\Datasources\GeoApiDatasource;
use Pecee\SimpleRouter\SimpleRouter;
use Throwable;

/**
 * @static
 * Full static functions to build route relatives to cities.
 */
class CityRouter
{
    public static function defineRoutes(): void
    {
        SimpleRouter::group(['prefix' => '/cities'], function () {
            SimpleRouter::get("/", function () {
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
                    echo "Exception : An error occured while fetching cities.\n";
                    print_r($err);
                    exit;
                }
            });

            SimpleRouter::get("/{id}", function ($id) {
                http_response_code(500);
                echo "Exception : Route not completed.\n";
                print_r($id);
                exit;
            });
        });
    }
}
