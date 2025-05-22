<?php

declare(strict_types=1);

namespace Infra\Router;

use Core\ApiResponse;
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
        /** @var ApiResponse */
        $response = Cache::run($_SERVER["REQUEST_URI"], 86_400, function () {
            if (!isset($_GET["name"])) {
                http_response_code();
                return new ApiResponse(
                    success: false,
                    code: 406,
                    message: "You must provide a name through query params."
                );
            }
            /** @var FindCompanies */
            $findCompanies = Container::get()->resolve(FindCompanies::class);
            /** @var Result */
            $result = $findCompanies->perform(new FindCompaniesParams(name: $_GET["name"]));
            return $result->response;
        });
        header("Content-Type: application/json");
        http_response_code($response->code);
        echo json_encode($response);
        exit;
    }

    public function getCompanyByID($id): void
    {
        /** @var ApiResponse */
        $response = Cache::run($_SERVER["REQUEST_URI"], 86_400, function () use ($id) {
            /** @var FindCompanyByID */
            $findCompany = Container::get()->resolve(FindCompanyByID::class);
            /** @var Result */
            $result = $findCompany->perform(new FindCompanyByIDParams(ID: $id));
            return $result->response;
        });
        header("Content-Type: application/json");
        http_response_code($response->code);
        echo json_encode($response);
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
