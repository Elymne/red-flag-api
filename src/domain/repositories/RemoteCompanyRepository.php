<?php

namespace Domain\Repositories;

use Domain\Models\Company;

interface RemoteCompanyRepository
{
    /**
     * Find all companies that could correspond to the name given in argument.
     * 
     * @param string $name
     * @return Company[] - List of companies.
     */
    function findMany(
        string $name
    ): array;

    /**
     * Fetch the unique company given the code (id).
     * 
     * @param string $ID
     * @return Company|null - The unique company.
     */
    function findUnique(
        string $ID
    ): Company|null;
}
