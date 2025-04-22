<?php

namespace Domain\Repositories;

use Domain\Models\Zone;

interface RemoteZoneRepository
{
    /**
     * Find all zones that could correspond to the name given in argument.
     * 
     * @param string $name
     * @return Zone[] - List of cities
     */
    function findMany(
        string $name
    ): array;

    /**
     * Fetch the unique zone given the code (id).
     * 
     * @param string $ID
     * @return Zone|null - The unique zone
     */
    function findUnique(
        string $ID
    ): Zone|null;
}
