<?php

declare(strict_types=1);

namespace Infra\Router;

use Core\ApiResponse;
use Core\Result;
use Core\Container;
use Domain\Usecases\FindPersons;
use Domain\Usecases\FindPersonsParams;
use Domain\Usecases\FindPersonByID;
use Domain\Usecases\FindPersonByIDParams;
use Domain\Usecases\InsertLink;
use Domain\Usecases\InsertLinkParams;
use Domain\Usecases\InsertPerson;
use Domain\Usecases\InsertPersonParams;
use Pecee\SimpleRouter\SimpleRouter;

/**
 * @static
 * Full static functions to build route relatives to person.
 */
class PersonRouter
{
    public function getPersons(): void
    {
        /** @var ApiResponse */
        $response = Cache::run($_SERVER["REQUEST_URI"], 86_400, function () {
            /** @var string|null */
            $firstname = isset($_GET["firstname"]) ? $_GET["firstname"] : null;
            /** @var string|null */
            $lastname = isset($_GET["lastname"]) ? $_GET["lastname"] : null;
            /** @var int|null */
            $birthDate = isset($_GET["birthDate"]) ? intval($_GET["birthDate"])  : null;
            /** @var string|null */
            $zoneID = isset($_GET["zoneID"]) ? $_GET["zoneID"] : null;
            /** @var string|null */
            $companyID = isset($_GET["companyID"]) ? $_GET["companyID"] : null;
            /** @var string|null */
            $activityID = isset($_GET["activityID"]) ? $_GET["activityID"] : null;
            /** @var FindPersons */
            $findPersons = Container::get()->resolve(FindPersons::class);
            $result = $findPersons->perform(new FindPersonsParams(
                firstname: $firstname,
                lastname: $lastname,
                birthDate: $birthDate,
                zoneID: $zoneID,
                companyID: $companyID,
                activityID: $activityID,
            ));
            return $result->response;
        });
        header("Content-Type: application/json");
        http_response_code($response->code);
        echo json_encode($response);
        exit;
    }

    public function getPersonByID($id): void
    {
        /** @var ApiResponse */
        $response = Cache::run($_SERVER["REQUEST_URI"], 86_400, function () use ($id) {
            /** @var FindPersonByID */
            $FindLocalPerson = Container::get()->resolve(FindPersonByID::class);
            /** @var Result */
            $result = $FindLocalPerson->perform(new FindPersonByIDParams($id));
            return $result->response;
        });
        header("Content-Type: application/json");
        http_response_code($response->code);
        echo json_encode($response);
        exit;
    }

    public function createPerson(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (
            !isset($data["firstname"]) ||
            !isset($data["lastname"]) ||
            !isset($data["birthDate"]) ||
            !isset($data["zoneID"])
        ) {
            header("Content-Type: application/json");
            http_response_code(406);
            echo json_encode(new ApiResponse(
                success: false,
                code: 406,
                message: "Query missing : {firstname}, {lastname}, {birthDate}, {zoneID}, {companyID} and {activityID} should be provided.",
            ));
            exit;
        }
        /** @var InsertPerson */
        $insertPerson = Container::get()->resolve(InsertPerson::class);
        /** @var Result */
        $result = $insertPerson->perform(new InsertPersonParams(
            firstname: $data["firstname"],
            lastname: $data["lastname"],
            birthDate: intval($data["birthDate"]),
            zoneID: $data["zoneID"],
            companyID: $data["companyID"] ?? null,
            activityID: $data["activityID"] ?? null,
        ));
        header("Content-Type: application/json");
        http_response_code($result->response->code);
        echo json_encode($result->response);
        exit;
    }

    public function addLink(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["personID"]) || !isset($data["source"])) {
            header("Content-Type: application/json");
            http_response_code(406);
            echo json_encode(new ApiResponse(
                success: false,
                code: 406,
                message: "Query missing : {personID} and {source} should be provided.",
            ));
            exit;
        }
        /** @var InsertLink */
        $insertLink = Container::get()->resolve(InsertLink::class);
        /** @var Result */
        $result = $insertLink->perform(new InsertLinkParams(
            personID: $data["personID"],
            source: $data["source"],
        ));
        header("Content-Type: application/json");
        http_response_code($result->response->code);
        echo json_encode($result->response);
        exit;
    }


    public static function defineRoutes(): void
    {
        SimpleRouter::group(["prefix" => "/persons"], function () {
            SimpleRouter::get("/",  [self::class, "getPersons"]);
            SimpleRouter::get("/{id}",  [self::class, "getPersonByID"]);
            SimpleRouter::post("/",  [self::class, "createPerson"]);
            SimpleRouter::post("/links",  [self::class, "addLink"]);
        });
    }
}
