<?php

declare(strict_types=1);

namespace Infra\Router;

use Infra\Di\Container;
use Domain\Usecases\FindPersons;
use Domain\Usecases\FindPersonsParams;
use Domain\Usecases\FindUniquePerson;
use Domain\Usecases\FindUniquePersonParams;
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
    public static function defineRoutes(): void
    {
        SimpleRouter::group(["prefix" => "/persons"], function () {
            SimpleRouter::get("/", function () {
                /** @var string|null */
                $firstname = null;
                if (isset($_GET["firstname"])) {
                    $firstname = $_GET["firstname"];
                }
                /** @var string|null */
                $lastname = null;
                if (isset($_GET["lastname"])) {
                    $lastname = $_GET["lastname"];
                }
                /** @var int|null */
                $birthday = null;
                if (isset($_GET["birthday"])) {
                    $birthday = intval($_GET["birthday"]);
                }
                /** @var string|null */
                $zonename = null;
                if (isset($_GET["zonename"])) {
                    $zonename = $_GET["zonename"];
                }
                /** @var string|null */
                $jobname = null;
                if (isset($_GET["jobname"])) {
                    $jobname = $_GET["jobname"];
                }

                /** @var FindPersons */
                $findPersons = Container::get()->resolve(FindPersons::class);
                // * Fetch persons.
                $result = $findPersons->perform(new FindPersonsParams(
                    firstname: $firstname,
                    lastname: $lastname,
                    birthday: $birthday,
                    zonename: $zonename,
                    jobname: $jobname,
                ));
                // * send response.
                header("Content-Type: application/json");
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });


            SimpleRouter::get("/{id}", function ($id) {
                /** @var FindUniquePerson */
                $findUniquePerson = Container::get()->resolve(FindUniquePerson::class);
                // * Fetch unique person.
                $result = $findUniquePerson->perform(new FindUniquePersonParams($id));
                // * send response.
                header("Content-Type: application/json");
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });

            SimpleRouter::post("/", function () {
                // * get body post.
                $data = json_decode(file_get_contents("php://input"), true);
                // * Check that each value from body exists.
                if (
                    !isset($data["firstname"]) ||
                    !isset($data["lastname"]) ||
                    !isset($data["birthday"]) ||
                    !isset($data["zoneID"]) ||
                    !isset($data["jobname"])
                ) {
                    // * send input error response.
                    header("Content-Type: application/json");
                    http_response_code(400);
                    echo json_encode("Action failure : Body imcomplete.");
                    exit;
                }
                /** @var InsertPerson */
                $insertPerson = Container::get()->resolve(InsertPerson::class);
                // * Insert person.
                $result = $insertPerson->perform(new InsertPersonParams(
                    firstname: $data["firstname"],
                    lastname: $data["lastname"],
                    birthday: intval($data["birthday"]),
                    zoneID: $data["zoneID"],
                    jobname: $data["jobname"],
                ));
                // * send response.
                header("Content-Type: application/json");
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });

            SimpleRouter::post("/links", function () {
                // * Get body post.
                $data = json_decode(file_get_contents("php://input"), true);
                // * Check that each value from body exists.
                if (
                    !isset($data["personID"]) ||
                    !isset($data["link"])
                ) {
                    // * send input error response.
                    header("Content-Type: application/json");
                    http_response_code(400);
                    echo json_encode("Action failure : Body imcomplete.");
                    exit;
                }
                /** @var InsertLink */
                $insertLink = Container::get()->resolve(InsertLink::class);
                // * Insert link.
                $result = $insertLink->perform(new InsertLinkParams(
                    personID: $data["personID"],
                    link: $data["link"],
                ));
                // * send response.
                header("Content-Type: application/json");
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });
        });
    }
}
