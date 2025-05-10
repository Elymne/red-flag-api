<?php

namespace Domain\Repositories;

use Domain\Models\PersonRemoteData;

interface RemotePersonRepository
{
    /**
     * Find remote data about a person given his fullname for example.
     * 
     * @param string $fullname
     * @return PersonRemoteData
     */
    function findPerson(string $fullname): PersonRemoteData|null;
}
