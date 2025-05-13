<?php

namespace Domain\Repositories;

use Domain\Models\Person;
use Domain\Models\PersonDetailed;
use Domain\Models\PersonRemoteData;

interface RemotePersonRepository
{
    /**
     * Find remote data about a person given his fullname for example.
     * 
     * @param string $fullname
     * @return PersonRemoteData
     */
    function findAdditionalData(Person|PersonDetailed $person): PersonRemoteData|null;
}
