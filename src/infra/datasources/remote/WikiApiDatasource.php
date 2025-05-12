<?php

declare(strict_types=1);

namespace Infra\Datasources;


use Domain\Models\PersonRemoteData;
use Domain\Repositories\RemotePersonRepository;

class WikiApiDatasource implements RemotePersonRepository
{

    function findAdditionalData(string $fullname): PersonRemoteData|null
    {
        $url = "https://en.wikipedia.org/api/rest_v1/page/summary/" . $fullname;

        $context = stream_context_create([
            "http" => [
                "method"  => "GET",
                "header"  => "Content-type: application/x-www-form-urlencoded",
            ]
        ]);

        $response = file_get_contents(filename: $url, context: $context);
        $rawZone = json_decode($response, true);

        return new PersonRemoteData(
            portrait: $rawZone["thumbnail"]["source"],
            description: $rawZone["extract"]
        );
    }
}
