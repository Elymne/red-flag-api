<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Models\Person;
use Domain\Models\PersonDetailed;
use Domain\Models\PersonRemoteData;
use Domain\Repositories\RemotePersonRepository;

class WikiApiDatasource implements RemotePersonRepository
{
    function findAdditionalData(Person|PersonDetailed $person): PersonRemoteData|null
    {
        //* Prepare request.
        $ch = curl_init("https://en.wikipedia.org/api/rest_v1/page/summary/" . $person->firstName . "_" . $person->lastName);
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
        // * If 404 or server error or whatever.
        if (!$response) return null;
        // * Decode json data.
        $rawZone = json_decode($response, true);
        // * Return my decoded data.
        return new PersonRemoteData(
            portrait: $rawZone["thumbnail"]["source"],
            description: $rawZone["extract"]
        );
    }
}
