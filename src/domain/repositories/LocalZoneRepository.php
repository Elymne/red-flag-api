<?php

namespace Domain\Repositories;

use Domain\Models\Zone;

interface LocalZoneRepository
{
    /**
     * Find zones that exists in our database.
     * Can be useful to only get zones that are linked to our person stored in database.
     * 
     * @param string|null $name
     * @return Zone[]
     */
    function findMany(
        string|null $name = null,
    ): array;

    /**
     * Find a unique zone given the id.
     *  
     * @param string $ID
     * @return Zone|null
     */
    function findUnique(
        string $ID
    ): Zone|null;

    /**
     * Insert a new zone to database.
     *  
     * @param Zone $zone
     * The id is not generated from server. The id is just the "code" of the zone (depending of the region).
     */
    function createOne(Zone $zone): void;
}
