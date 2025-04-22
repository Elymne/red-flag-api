<?php

declare(strict_types=1);

namespace Infra\Router;

use Domain\Usecases\FindCompanies;
use Domain\Usecases\FindCompaniesParams;
use Domain\Usecases\FindCompanyByID;
use Domain\Usecases\FindCompanyByIDParams;
use Pecee\SimpleRouter\SimpleRouter;
use Core\Container;

class CompanyRouter
{
    public function getCompanies(): void
    {
        /** @var FindCompanies */
        $findCompanies = Container::get()->resolve(FindCompanies::class);
        /** @var Result */
        $result = $findCompanies->perform(new FindCompaniesParams(name: $_GET["name"]));
        header("Content-Type: application/json");
        http_response_code($result->code);
        echo json_encode($result->response);
        exit;
    }

    public function getCompanyByID($id): void
    {
        /** @var FindCompanyByID */
        $findCompany = Container::get()->resolve(FindCompanyByID::class);
        /** @var Result */
        $result = $findCompany->perform(new FindCompanyByIDParams(ID: $id));
        header("Content-Type: application/json");
        http_response_code($result->code);
        echo json_encode($result->response);
        exit;
    }

    public static function defineRoutes(): void
    {
        SimpleRouter::group(["prefix" => "/companies"], function () {
            SimpleRouter::get("/",  [self::class, "getCompanies"]);
            SimpleRouter::get("/{id}",  [self::class, "getCompanyByID"]);
        });
    }
}
