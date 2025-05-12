<?php

declare(strict_types=1);

namespace Infra\Router;

use Domain\Usecases\FindPersons;
use Domain\Usecases\FindPersonsParams;
use Domain\Usecases\FindUniquePerson;
use Domain\Usecases\FindUniquePersonParams;
use Domain\Usecases\InsertLink;
use Domain\Usecases\InsertLinkParams;
use Domain\Usecases\InsertPerson;
use Domain\Usecases\InsertPersonParams;
use Infra\Di\Container;
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
                /** @var string|null */
                $firstname = null;
                /** @var string|null */
                $lastname = null;
                /** @var string|null */
                $birthday = null;
                /** @var string|null */
                $zonename = null;
                /** @var string|null */
                $jobname = null;
                if (!isset($_POST["firstname"]) || isset($_POST["lastname"]) || isset($_POST["birthday"]) || isset($_POST["zonename"]) || isset($_POST["jobname"])) {
                    // * send input error response.
                    header("Content-Type: application/json");
                    http_response_code(400);
                    echo json_encode("Action failure : Wrong body data.");
                    exit;
                }
                // * Get POST body.
                $firstname = $_POST["firstname"];
                $lastname = $_POST["lastname"];
                $birthday = intval($_POST["birthday"]);
                $zonename = $_POST["zonename"];
                $jobname = $_POST["jobname"];
                /** @var InsertPerson */
                $insertPerson = Container::get()->resolve(InsertPerson::class);
                // * Insert person.
                $result = $insertPerson->perform(new InsertPersonParams(
                    firstname: $firstname,
                    lastname: $lastname,
                    birthday: $birthday,
                    jobname: $zonename,
                    zoneID: $jobname,
                ));
                // * send response.
                header("Content-Type: application/json");
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });

            SimpleRouter::post("/links", function () {
                /** @var InsertLink */
                $findMessage = Container::get()->resolve(InsertLink::class);
                // * Insert link.
                $result = $findMessage->perform(new InsertLinkParams(
                    personID: $_POST["personid"],
                    link: $_POST["link"],
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
