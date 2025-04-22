<?php

namespace Domain\Repositories;

use Domain\Models\Zone;

interface RemoteZoneRepository
{
    /**
     * Fetch the list of cities that can correspond to the name or the id.
     * 
     * @param string|null $name
     * @param string|null $id
     * @return Zone[] - List of cities
     */
    function findMany(string|null $name = null, string|null $id = null): array;

    /**
     * Fetch the unique zone given the code (id).
     * 
     * @param string $id
     * @return Zone|null - The unique zone
     */
    function findUnique(string $id): Zone|null;
}
