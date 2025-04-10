<?php

namespace Infra\Datasources;

use Domain\Models\PersonDetailed;
use Domain\Repositories\LocalPersonRepository;
use Infra\Data\DBConnect;

class PersonMysqlDatasource implements LocalPersonRepository
{
    private DBConnect $_db;

    public function __construct(DBConnect $db)
    {
        $this->_db = $db;
    }

    public function findMany(
        string|null $id = null,
        string|null $firstname = null,
        string|null $surname = null,
        string|null $fullname = null,
        array|null $city = null
    ): array {
        // Implement the logic to find and return multiple records
        return [];
    }

    public function findUnique(string $id): PersonDetailed|null
    {
        // Implement the logic to find and return a unique record by ID
        return null;
    }


    function createOne(PersonDetailed $personDetailed): PersonDetailed
    {
        throw "Not implemented";
    }

    function addMessage(string $id, string $message): PersonDetailed
    {
        throw "Not implemented";
    }

    function addLink(string $id, string $message): PersonDetailed
    {
        throw "Not implemented";
    }
}
