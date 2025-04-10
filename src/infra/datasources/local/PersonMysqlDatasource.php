<?php

namespace Infra\Datasources;

use Domain\Models\Person;
use Domain\Models\PersonDetailed;
use Domain\Repositories\LocalPersonRepository;
use Infra\Data\DBConnect;
use Ramsey\Uuid\Uuid;

class PersonMysqlDatasource implements LocalPersonRepository
{
    private DBConnect $_db;

    public function __construct(DBConnect $db)
    {
        $this->_db = $db;
    }

    public function findMany(
        string|null $firstname = null,
        string|null $surname = null,
        string|null $cityName = null
    ): array {
        // Prapare the statement.
        /** @var string */
        $query = "SELECT HEX(id) as id, first_name, last_name, id_code, created_at, updated_at FROM person WHERE 1=1";
        if (!is_null($firstname)) {
            $query .= " AND first_name = ?";
        }
        if (!is_null($surname)) {
            $query .= " AND last_name = ?";
        }
        if (!is_null($cityName)) {
            $query .= " AND id_city = ?";
        }
        $stmt = $this->_db->getMysqli()->prepare($query);

        // Inject the value.
        $params = [];
        if (!is_null($firstname)) {
            $params[] = $firstname;
        }
        if (!is_null($surname)) {
            $params[] = $surname;
        }
        if (!is_null($cityName)) {
            $params[] = $cityName;
        }
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }

        /** @var Person[] */
        $persons = [];

        $stmt->execute();
        $result = $stmt->get_result();

        // while ($row = $result->fetch_assoc()) {
        //     array_push($articles, new Person(
        //        id: Uuid::fromString($row["id"]),
        //         firstName:$row["first_name"],
        //         lastName: $row["last_name"],
        //         city


        //     ));
        // }

        return $persons;
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
