<?php

declare(strict_types=1);

namespace Infra\Router;

use Core\ApiResponse;
use Core\Result;
use Core\Container;
use Domain\Usecases\FindActivities;
use Domain\Usecases\FindActivitiesParams;
use Domain\Usecases\FindActivityByID;
use Domain\Usecases\FindActivityByIDParams;
use Pecee\SimpleRouter\SimpleRouter;
use OpenApi\Attributes as OA;

class ActivityRouter
{
    #[OA\Get(path: "/api/activities")]
    #[OA\Response(response: "200", description: "Action success : list of activities.")]
    #[OA\Response(response: "400", description: "Action failure : the data send from body is not correct. Should be a FindActivitiesParams structure.")]
    #[OA\Response(response: "500", description: "Action failure : internal Server Error.")]
    public static function getActivities()
    {
        if (!isset($_GET["name"])) {
            http_response_code(406);
            echo json_encode(new ApiResponse(
                success: false,
                message: "You must provide a name through query params."
            ));
            exit;
        }
        /** @var FindActivities */
        $findActivity = Container::get()->resolve(FindActivities::class);
        /** @var Result */
        $result = $findActivity->perform(new FindActivitiesParams($_GET["name"]));
        header("Content-Type: application/json");
        http_response_code($result->code);
        echo json_encode($result->response);
        exit;
    }

    #[OA\Get(path: "/activities/{id}")]
    #[OA\Response(response: "200", description: "Action success : unique activity.")]
    #[OA\Response(response: "400", description: "Action failure : the data send from body is not correct. Should be a FindActivityByIDParams structure.")]
    #[OA\Response(response: "404", description: "Action failure : this activty does not exists.")]
    #[OA\Response(response: "500", description: "Action failure : Internal Server Error.")]
    public static function getActivityByID($id)
    {
        /** @var FindActivityByID */
        $findActivity = Container::get()->resolve(FindActivityByID::class);
        /** @var Result */
        $result = $findActivity->perform(new FindActivityByIDParams(ID: $id));
        header("Content-Type: application/json");
        http_response_code($result->code);
        echo json_encode($result->response);
        exit;
    }

    // * ACTIVITY ROUTER.
    public static function defineRoutes(): void
    {
        SimpleRouter::group(["prefix" => "/activities"], function () {
            SimpleRouter::get("/",  [self::class, "getActivities"]);
            SimpleRouter::get("/{id}", [self::class, "getActivityByID"]);
        });
    }
}
