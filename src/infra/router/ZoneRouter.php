<?php

declare(strict_types=1);

namespace Infra\Router;

use Core\ApiResponse;
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
        /** @var ApiResponse */
        $response = Cache::run($_SERVER["REQUEST_URI"], 86_400, function () {
            if (!isset($_GET["name"])) {
                return new ApiResponse(
                    success: false,
                    code: 406,
                    message: "The query param [name] have to be set",

                );
            }
            /** @var FindZones */
            $findRemoteZones = Container::get()->resolve(FindZones::class);
            /** @var Result */
            $result = $findRemoteZones->perform(new FindZonesParams(name: $_GET["name"]));
            return $result->response;
        });
        header("Content-Type: application/json");
        http_response_code($response->code);
        echo json_encode($response);
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
