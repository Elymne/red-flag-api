<?php

namespace Infra\Router;

use Domain\Usecases\FindPersons;
use Domain\Usecases\FindPersonsParams;
use Domain\Usecases\FindUniquePerson;
use Domain\Usecases\FindUniquePersonParams;
use Domain\Usecases\InsertLink;
use Domain\Usecases\InsertLinkParams;
use Domain\Usecases\InsertMessage;
use Domain\Usecases\InsertMessageParams;
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
        SimpleRouter::group(['prefix' => '/persons'], function () {
            SimpleRouter::get("/", function () {
                /** @var FindPersons */
                $findPersons = Container::get()->resolve(FindPersons::class);
                // Fetch persons.
                $result = $findPersons->perform(new FindPersonsParams(
                    firstname: $_GET["firstname"],
                    lastname: $_GET["lastname"],
                    zonename: $_GET["zonename"],
                    jobname: $_GET["jobname"],
                ));
                // send response.
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });

            SimpleRouter::get("/{id}", function ($id) {
                /** @var FindUniquePerson */
                $findUniquePerson = Container::get()->resolve(FindUniquePerson::class);
                // Fetch unique person.
                $result = $findUniquePerson->perform(new FindUniquePersonParams($id));
                // send response.
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });

            SimpleRouter::post("/", function () {
                /** @var InsertPerson */
                $insertPerson = Container::get()->resolve(InsertPerson::class);
                // Insert person.
                $result = $insertPerson->perform(new InsertPersonParams(
                    firstname: $_POST["firstname"],
                    lastname: $_POST["firstname"],
                    zoneID: $_GET["zoneid"]
                ));
                // send response.
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });

            SimpleRouter::post("/messages", function () {
                /** @var InsertMessage */
                $findMessage = Container::get()->resolve(InsertMessage::class);
                // Insert message.
                $result = $findMessage->perform(new InsertMessageParams(
                    personID: $_POST["personid"],
                    message: $_POST["message"],
                ));
                // send response.
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });

            SimpleRouter::post("/links", function () {
                /** @var InsertLink */
                $findMessage = Container::get()->resolve(InsertLink::class);
                // Insert link.
                $result = $findMessage->perform(new InsertLinkParams(
                    personID: $_POST["personid"],
                    link: $_POST["link"],
                ));
                // send response.
                http_response_code($result->code);
                echo json_encode($result->data);
                exit;
            });
        });
    }
}
