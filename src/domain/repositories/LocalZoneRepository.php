<?php

namespace Domain\Repositories;

use Domain\Models\Zone;

interface LocalZoneRepository
{
    /**
     * Find as many as possible cities depending of args given to the function.
     * If no args are given, we may return nothing.
     * 
     * @param string|null $name
     * @return Zone[]
     */
    function findMany(string|null $name = null, string|null $id = null): array;

    /**
     * Find a unique city given the id.
     *  
     * @param string $id
     * @return Zone
     */
    function findUnique(string $id): Zone|null;

    /**
     * Insert a new city to database.
     *  
     * @param Zone $city
     * The id is not generated from server. The id is just the "code commune" of the city.
     * @return Zone Inserted Zone data.
     */
    function createOne(Zone $city): void;
}
