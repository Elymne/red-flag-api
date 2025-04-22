<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Core\NetworkException;
use Domain\Models\Zone;
use Domain\Repositories\RemoteZoneRepository;

class GeoApiDatasource implements RemoteZoneRepository
{
    public function findMany(string $name): array
    {
        // * Prepare Request.
        $ch = curl_init("https://geo.api.gouv.fr/communes?nom=" . $name);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);

        // * On dev env, we remove SSL checker.
        if ($_ENV["MODE"] == "develop") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // * Send request and close curl prog.
        $response = curl_exec($ch);
        curl_close($ch);

        // * If 404 or server error or whatever.
        if (!$response) {
            throw new NetworkException("France Travail Activity error while fetching");
        }

        // * Decode json data.
        $rawCities = json_decode($response, true);

        // * Parse zones.
        $result = [];
        for ($i = 0; $i < 100 && $i < count($rawCities); $i++) {
            $rawZone = $rawCities[$i];
            array_push($result, new Zone(
                ID: $rawZone["code"],
                name: $rawZone["nom"]
            ));
        }

        // * Return result.
        return $result;
    }

    public function findUnique(string $ID): Zone|null
    {
        // * Prepare Request.
        $ch = curl_init("https://geo.api.gouv.fr/communes/" . $ID);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);

        // * On dev env, we remove SSL checker.
        if ($_ENV["MODE"] == "develop") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // * Send request and close curl prog.
        $response = curl_exec($ch);
        curl_close($ch);

        // * Decode.
        $rawZone = json_decode($response, true);

        // * Return response.
        return new Zone(
            ID: $rawZone["code"],
            name: $rawZone["nom"]
        );
    }
}
