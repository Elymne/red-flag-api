<?php

namespace Infra\Datasources;

use Domain\Models\Person;
use Domain\Models\PersonDetailed;
use Domain\Models\RedFlagLink;
use Domain\Models\RedFlagMessage;
use Domain\Models\Zone;
use Domain\Repositories\LocalPersonRepository;
use Exception;
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
        string|null $lastName = null,
        string|null $zoneName = null
    ): array {
        // Prapare the statement.
        /** @var string */
        $query = "SELECT HEX(id) as id, first_name, last_name, id_zone, created_at, updated_at, zone.id, zone.name FROM person INNER JOIN zone ON zone.id = id_zone WHERE 1=1";
        if (!is_null($firstname)) {
            $query .= " AND first_name = ?";
        }
        if (!is_null($lastName)) {
            $query .= " AND last_name = ?";
        }
        if (!is_null($zoneName)) {
            $query .= " AND zone.name = ?";
        }
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        $params = [];
        if (!is_null($firstname)) {
            $params[] = $firstname;
        }
        if (!is_null($lastName)) {
            $params[] = $lastName;
        }
        if (!is_null($zoneName)) {
            $params[] = $zoneName;
        }
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

    public function findUnique(string $id): PersonDetailed|null
    {
        // Prapare the statement for link data.
        $query =
            "SELECT HEX(id_person) as id_person, HEX(id_red_flag_link) as id_red_flag_link, l.value, l.created_at, l.updated_at 
            FROM red_flag_link_join
            INNER JOIN red_flag_link as l ON l.id = id_red_flag_link 
            WHERE id_person = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        $stmt->bind_param("s", [$id]);
        // Run SQL Command and fetch result.
        $stmt->execute();
        $linksResult = $stmt->get_result();
        // Parse links.
        /** @var RedFlagLink[] */
        $links = [];
        while ($row = $linksResult->fetch_assoc()) {
            array_push($links, new RedFlagLink(
                id: Uuid::fromString($row["id_red_flag_link"]),
                value: $row["l.value"],
                createdAt: $row["l.created_at"],
                updatedAt: $row["l.updated_at"],
            ));
        }

        // Prapare the statement for message data.
        $query =
            "SELECT HEX(id_person) as id_person, HEX(id_red_flag_message) as id_red_flag_message, m.value, m.created_at, m.updated_at 
            FROM red_flag_message_join
            INNER JOIN red_flag_message as m ON m.id = id_red_flag_message
            WHERE id_person = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        $stmt->bind_param("s", [$id]);
        // Run SQL Command and fetch result.
        $stmt->execute();
        $messagesResult = $stmt->get_result();
        // Parse messages.
        /** @var RedFlagMessage[] */
        $messages = [];
        while ($row = $messagesResult->fetch_assoc()) {
            array_push($messages, new RedFlagMessage(
                id: Uuid::fromString($row["id_red_flag_message"]),
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
        $stmt->bind_param("s", [$id]);
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


    function createOne(Person $person): Person
    {
        // Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO person (id, first_name, last_name, created_at, updated_at, id_zone) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $stmt->bind_param("isssss", $person->id->getBytes(), $person->firstName, $person->lastName, $person->createdAt, $person->updatedAt, $person->zone->id);
        // Run SQL Command and fetch result.
        $stmt->execute();
        // Check if result is a success or not.
        if (!$stmt->get_result()) {
            throw new Exception("CityMysqlDatasource Exception : Failure on creating a new person.");
        }
        // Return the created value.
        return $person;
    }

    function addMessage(string $id, string $value): RedFlagMessage
    {
        throw "Not implemented";
    }

    function addLink(string $id, string $value): RedFlagLink
    {
        throw "Not implemented";
    }
}
