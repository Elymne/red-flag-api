<?php

namespace Infra;

use Domain\City;
use Domain\RemoteCityRepository;

class GeoApiDatasource implements RemoteCityRepository
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
            $rawCity = $rawCities[$i];
            array_push($result, new City(
                id: $rawCity["code"],
                name: $rawCity["nom"]
            ));
        }

        return $result;
    }

    public function findUnique(string $id): City|null
    {
        $url = "https://geo.api.gouv.fr/communes/" . $id;

        $context = stream_context_create([
            "http" => [
                "method"  => "GET",
                "header"  => "Content-type: application/x-www-form-urlencoded",
            ]
        ]);

        $response = file_get_contents(filename: $url, context: $context);
        $rawCity = json_decode($response, true);

        return new City(
            id: $rawCity["code"],
            name: $rawCity["nom"]
        );
    }
}
