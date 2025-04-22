<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Models\PersonRemoteData;
use Domain\Repositories\RemotePersonRepository;

// TODO P'tit rework ici.
class WikiApiDatasource implements RemotePersonRepository
{
    function findRemoteData(
        string $firstname,
        string $lastname,
        int|null $birthDate = null,
        string|null $activity = null
    ): PersonRemoteData|null {
        //* Prepare request.
        $ch = curl_init("https://en.wikipedia.org/api/rest_v1/page/summary/" . $firstname . "_" . $lastname);
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

        // * Check curl result.
        if (!$response) return null;

        // * Decode json data.
        $decoded = json_decode($response, true);

        // * Check http code.
        if ($decoded["status"] !== 200) return null;

        // * Return my decoded data.
        return new PersonRemoteData(
            portrait: $decoded["thumbnail"]["source"],
            description: $decoded["extract"],
        );
    }
}
