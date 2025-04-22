<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Models\Zone;
use Domain\Repositories\RemoteZoneRepository;

class GeoApiDatasource implements RemoteZoneRepository
{
    public function findMany(string|null $name = null, string|null $id = null): array
    {
        $url = "https://geo.api.gouv.fr/communes?";
        if ($name) $url = $url . "&nom=" . $name;
        if ($id) $url = $url . "&code=" . $id;

        $context = stream_context_create([
            "http" => [
                "method"  => "GET",
                "header"  => "Content-type: application/x-www-form-urlencoded",
            ]
        ]);

        $response = file_get_contents(filename: $url, context: $context);
        $rawCities = json_decode($response, true);

        $result = [];
        for ($i = 0; $i < 100 && $i < count($rawCities); $i++) {
            $rawZone = $rawCities[$i];
            array_push($result, new Zone(
                id: $rawZone["code"],
                name: $rawZone["nom"]
            ));
        }

        return $result;
    }

    public function findUnique(string $id): Zone|null
    {
        $url = "https://geo.api.gouv.fr/communes/" . $id;

        $context = stream_context_create([
            "http" => [
                "method"  => "GET",
                "header"  => "Content-type: application/x-www-form-urlencoded",
            ]
        ]);

        $response = file_get_contents(filename: $url, context: $context);
        $rawZone = json_decode($response, true);

        return new Zone(
            id: $rawZone["code"],
            name: $rawZone["nom"]
        );
    }
}
