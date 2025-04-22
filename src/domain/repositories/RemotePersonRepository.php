<?php

namespace Domain\Repositories;

use Domain\Models\PersonRemoteData;

interface RemotePersonRepository
{
    /**
     * Find remote data about a person given his fullname for example.
     * 
     * @param string $firstname
     * @param string $lastname
     * @param int|null $birthDate Timestamp
     * @param  string|null $activity
     * @return PersonRemoteData|null
     */
    function findRemoteData(
        string $firstname,
        string $lastname,
        int|null $birthDate = null,
        string|null $activity = null
    ): PersonRemoteData|null;
}
