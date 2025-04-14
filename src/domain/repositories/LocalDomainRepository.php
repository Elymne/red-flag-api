<?php

namespace Domain\Repositories;

interface LocalDomainRepository
{
    /**
     * Find all domain that can correspond to the domain from arg1.
     * 
     * @param string $value
     * @return string
     */
    function findUnique(string $value): string|null;
}
