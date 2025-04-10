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
        /** @var Person[] */
        $persons = [];

        /** @var mysqli_stmt|false */
        $stmt = $this->_db->getMysqli()->prepare(
            "SELECT HEX(a.id) as id, a.title, a.description, a.img_url, a.created_at, a.updated_at, a.state_id, HEX(u.id) as user_id, u.username, u.user_role_id
            FROM article as a
            INNER JOIN user as u ON a.user_id = u.id"
        );
        $stmt->execute();
        $result = $stmt->get_result();

        // Parser.
        // while ($row = $result->fetch_assoc()) {
        //     array_push($articles, new Article(
        //         Uuid::fromString($row["id"]),
        //         new User(
        //             Uuid::fromString($row["user_id"]),
        //             $row["username"],
        //             Role::from($row["user_role_id"])
        //         ),
        //         $row["created_at"],
        //         $row["updated_at"],
        //         ContentState::from($row["state_id"]),
        //         $row["title"],
        //         $row["description"],
        //         $row["img_url"],
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
