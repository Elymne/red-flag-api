<?php

declare(strict_types=1);

namespace Infra\Router;

use Core\Container;
use Core\Result;
use Domain\Usecases\FindZones;
use Domain\Usecases\FindZonesParams;
use Pecee\SimpleRouter\SimpleRouter;
use OpenApi\Attributes as OA;

class ZoneRouter
{
    #[OA\Get(path: "/api/zones")]
    #[OA\Response(response: "200", description: "Action success : list of zones.")]
    #[OA\Response(response: "400", description: "Action failure : no query param (name).")]
    #[OA\Response(response: "500", description: "Action failure : Internal Server Error.")]
    public static function getZones(): void
    {
        header("Content-Type: application/json");
        if (!isset($_GET["name"])) {
            http_response_code(400);
            echo json_encode("The query param [name] have to be set");
            exit;
        }
        $nameParam = $_GET["name"];
        /** @var FindZones */
        $findRemoteZones = Container::get()->resolve(FindZones::class);
        /** @var Result */
        $result = $findRemoteZones->perform(new FindZonesParams(name: $nameParam));
        http_response_code($result->code);
        echo json_encode($result->response);
        exit;
    }

    // * ZONE ROUTER.
    public static function defineRoutes(): void
    {
        SimpleRouter::group(["prefix" => "/zones"], function () {
            SimpleRouter::get("/", [self::class, "getZones"]);
        });
    }
}
