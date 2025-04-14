<?php

namespace Infra\Router;

use Domain\Usecases\FindLocalZones;
use Domain\Usecases\FindRemoteZones;
use Domain\Usecases\FindZonesParams;
use Infra\Di\Container;
use Pecee\SimpleRouter\SimpleRouter;

/**
 * @static
 * Full static functions to build route relatives to cities.
 */
class ZoneRouter
{
    public static function defineRoutes(): void
    {
        SimpleRouter::group(['prefix' => '/zones'], function () {
            SimpleRouter::get("/remote", function () {
                /** @var FindLocalZones */
                $findLocalZones = Container::get()->resolve(FindLocalZones::class);
                $result = $findLocalZones->perform(
                    new FindZonesParams(
                        id: $_GET["id"] ?? null,
                        name: $_GET["name"] ?? null,
                    )
                );
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });

            SimpleRouter::get("/local", function () {
                $findRemoteZones = Container::get()->resolve(FindRemoteZones::class);
                $result = $findRemoteZones->perform(
                    new FindZonesParams(
                        id: $_GET["id"] ?? null,
                        name: $_GET["name"] ?? null,
                    )
                );
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });
        });
    }
}
