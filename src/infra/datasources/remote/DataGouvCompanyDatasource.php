<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Models\Company;
use Domain\Repositories\RemoteCompanyRepository;

class DataGouvCompanyDatasource implements RemoteCompanyRepository
{
    function findMany(string $name): array
    {
        // * Prepare Request.
        $nameEncoded = urlencode($name);
        $ch = curl_init("https://recherche-entreprises.api.gouv.fr/search?q=$nameEncoded");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
        ]);

        // * On dev env, we remove SSL checker.
        if ($_ENV["MODE"] == "develop") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // * Send request and check for errors.
        $response = curl_exec($ch);
        curl_close($ch);

        // * Decode json data.
        $rawCompanies = json_decode($response, true);

        /** @var Company[] */
        $companies = [];

        // * Parse data.
        foreach ($rawCompanies["results"] as $companyData) {
            // * Check raw data. Skip on odd data.
            if (
                !isset($companyData["siren"]) ||
                !isset($companyData["nom_complet"]) ||
                !isset($companyData["siege"]["geo_adresse"])
            ) continue;

            // * Parsing.
            array_push($companies, new Company(
                ID: $companyData["siren"],
                name: $companyData["nom_complet"],
                address: $companyData["siege"]["geo_adresse"],
            ));
        }

        // * Return my decoded data.
        return  $companies;
    }

    function findUnique(string $ID): Company|null
    {
        // * Prepare Request.
        $ch = curl_init("https://recherche-entreprises.api.gouv.fr/search?q=" . $ID);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
        ]);

        // * On dev env, we remove SSL checker.
        if ($_ENV["MODE"] == "develop") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // * Send request and close curl prog.
        $response = curl_exec($ch);
        curl_close($ch);

        // * Decode json data.
        $rawCompanies = json_decode($response, true)["results"];

        // * When no array, it mean company wasn't found.
        if (!is_array($rawCompanies)) {
            return null;
        }

        // * Return my decoded data.
        $firstCompany = $rawCompanies[0];
        return new Company(
            ID: $firstCompany["siren"],
            name: $firstCompany["nom_complet"],
            address: $firstCompany["siege"]["geo_adresse"],
        );
    }
}
