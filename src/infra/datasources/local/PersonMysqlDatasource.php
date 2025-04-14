<?php

namespace Infra\Datasources;

use Domain\Models\Person;
use Domain\Models\PersonDetailed;
use Domain\Models\RedFlagLink;
use Domain\Models\RedFlagMessage;
use Domain\Models\Zone;
use Domain\Repositories\LocalPersonRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Exception;

class PersonMysqlDatasource implements LocalPersonRepository
{
    private DBConnect $_db;

    public function __construct(DBConnect $db)
    {
        $this->_db = $db;
    }

    public function findMany(
        string|null $firstname = null,
        string|null $lastName = null,
        string|null $zoneName = null
    ): array {
        // Prapare the statement.
        /** @var string */
        $query = "SELECT HEX(id) as id, first_name, last_name, id_zone, created_at, updated_at, zone.id, zone.name FROM person INNER JOIN zone ON zone.id = id_zone WHERE 1=1";
        $params = [];
        if (!is_null($firstname)) {
            $query .= " AND first_name = ?";
            $params[] = $firstname;
        }
        if (!is_null($lastName)) {
            $query .= " AND last_name = ?";
            $params[] = $lastName;
        }
        if (!is_null($zoneName)) {
            $query .= " AND zone.name = ?";
            $params[] = $zoneName;
        }
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }
        // Run SQL Command and fetch result.
        $stmt->execute();
        $result = $stmt->get_result();
        // Parse persons.
        /** @var Person[] */
        $persons = [];
        while ($row = $result->fetch_assoc()) {
            array_push($persons, new Person(
                id: Uuid::fromString($row["id"]),
                firstName: $row["first_name"],
                lastName: $row["last_name"],

                zone: new Zone(
                    id: $row["zone.id"],
                    name: $row["zone.name"],
                ),

                createdAt: $row["created_at"],
                updatedAt: $row["updated_at"]
            ));
        }
        return $persons;
    }

    /**
     * @param UuidInterface $id
     */
    public function findUnique(UuidInterface $id): PersonDetailed|null
    {
        // Prapare the statement for link data.
        $query =
            "SELECT HEX(id_person) as id_person, HEX(id_link) as id_link, l.value, l.created_at, l.updated_at 
            FROM person_link
            INNER JOIN link as l ON l.id = id_link 
            WHERE id_person = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        $stmt->bind_param("s", $id->getBytes());
        // Run SQL Command and fetch result.
        $stmt->execute();
        $linksResult = $stmt->get_result();
        // Parse links.
        /** @var RedFlagLink[] */
        $links = [];
        while ($row = $linksResult->fetch_assoc()) {
            array_push($links, new RedFlagLink(
                id: Uuid::fromString($row["id_link"]),
                value: $row["l.value"],
                createdAt: $row["l.created_at"],
                updatedAt: $row["l.updated_at"],
            ));
        }

        // Prapare the statement for message data.
        $query =
            "SELECT HEX(id_person) as id_person, HEX(id_message) as id_message, m.value, m.created_at, m.updated_at 
            FROM person_message
            INNER JOIN message as m ON m.id = id_message
            WHERE id_person = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        $stmt->bind_param("s", [$id->getBytes()]);
        // Run SQL Command and fetch result.
        $stmt->execute();
        $messagesResult = $stmt->get_result();
        // Parse messages.
        /** @var RedFlagMessage[] */
        $messages = [];
        while ($row = $messagesResult->fetch_assoc()) {
            array_push($messages, new RedFlagMessage(
                id: Uuid::fromString($row["id_message"]),
                value: $row["m.value"],
                createdAt: $row["m.created_at"],
                updatedAt: $row["m.updated_at"],
            ));
        }

        // Prapare the statement for person data.
        /** @var string */
        $query =
            "SELECT HEX(id) as id, first_name, last_name, id_zone, created_at, updated_at, zone.id, zone.name 
            FROM person 
            INNER JOIN zone ON zone.id = id_zone 
            WHERE id = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        $stmt->bind_param("s", [$id->getBytes()]);
        // Run SQL Command and fetch result.
        $stmt->execute();
        $PersonsResult = $stmt->get_result();
        // Parse persons.
        /** @var PersonDetailed[] */
        $persons = [];
        while ($row = $PersonsResult->fetch_assoc()) {
            array_push($persons, new PersonDetailed(
                id: Uuid::fromString($row["id"]),
                firstName: $row["first_name"],
                lastName: $row["last_name"],

                zone: new Zone(
                    id: $row["zone.id"],
                    name: $row["zone.name"],
                ),

                links: $links,
                messages: $messages,

                createdAt: $row["created_at"],
                updatedAt: $row["updated_at"]
            ));
        }
        // Check if data exists, return null when it's not the case.
        if (count($persons) == 0) {
            return null;
        }
        //Return the first value.
        return $persons[0];
    }


    function createOne(Person $person): void
    {
        // Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO person (id, first_name, last_name, created_at, updated_at, id_zone) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $stmt->bind_param("isssss", $person->id->getBytes(), $person->firstName, $person->lastName, $person->createdAt, $person->updatedAt, $person->zone->id);
        // Run SQL Command and fetch result.
        $stmt->execute();
    }

    /**
     * @param UuidInterface $id
     */
    function addMessage(UuidInterface $id, string $value): void
    {
        // Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO message (id, value) VALUES (?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $stmt->bind_param("is", $id->getBytes(), $value);
        // Run SQL Command and fetch result.
        $stmt->execute();
    }


    /**
     * @param UuidInterface $id
     */
    function addLink(UuidInterface $id, string $value): void
    {
        // Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO link (id, value) VALUES (?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $stmt->bind_param("is", $id->getBytes(), $value);
        // Run SQL Command and fetch result.
        $stmt->execute();
    }
}
