<?php

namespace Domain\Repositories;

interface LocalDomainRepository
{
    /**
     * Find all domains.
     * 
     * @return string[] List of all domain's name.
     */
    function findAll(): array;

    /**
     * Check that the domain given to params exists or not.
     * 
     * @param string $domainName
     * @return bool
     */
    function doesExists(
        string $domainName
    ): bool;
}
