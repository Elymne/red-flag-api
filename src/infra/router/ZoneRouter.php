<?php

declare(strict_types=1);

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
            SimpleRouter::get("/local", function () {
                /** @var FindLocalZones */
                $findLocalZones = Container::get()->resolve(FindLocalZones::class);
                $result = $findLocalZones->perform(
                    new FindZonesParams(
                        name: $_GET["name"],
                    )
                );
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });

            SimpleRouter::get("/remote", function () {
                $findRemoteZones = Container::get()->resolve(FindRemoteZones::class);
                $result = $findRemoteZones->perform(
                    new FindZonesParams(
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
