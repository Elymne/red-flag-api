<?php

namespace Domain\Repositories;

use Domain\Models\City;

interface RemoteCityRepository
{
    /**
     * Fetch the list of cities that can correspond to the name or the id.
     * 
     * @param string|null $name
     * @param string|null $id
     * @return City[] - List of cities
     */
    function findMany(string|null $name = null, string|null $id = null): array;

    /**
     * Fetch the unique city given the code (id).
     * 
     * @param string $id
     * @return City|null - The unique city
     */
    function findUnique(string $id): City|null;
}
