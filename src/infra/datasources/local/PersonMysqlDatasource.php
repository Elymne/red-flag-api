<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Models\Person;
use Domain\Models\PersonDetailed;
use Domain\Models\Link;
use Domain\Models\Message;
use Domain\Models\Zone;
use Domain\Repositories\LocalPersonRepository;
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
        string|null $lastname = null,
        string|null $zonename = null,
        string|null $jobname = null,
    ): array {
        // Prapare the statement.
        /** @var string */
        $query = "SELECT HEX(person.id) as id, first_name, last_name, job_name, id_zone, created_at, updated_at, zone.id, zone.name FROM person INNER JOIN zone ON zone.id = id_zone WHERE 1=1";
        $params = [];
        if (!is_null($firstname)) {
            $query .= " AND first_name = ?";
            $params[] = $firstname;
        }
        if (!is_null($lastname)) {
            $query .= " AND last_name = ?";
            $params[] = $lastname;
        }
        if (!is_null($jobname)) {
            $query .= " AND job_name = ?";
            $params[] = $jobname;
        }
        if (!is_null($zonename)) {
            $query .= " AND zone.name = ?";
            $params[] = $zonename;
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
                id: $row["person_id"],
                firstName: $row["first_name"],
                lastName: $row["last_name"],
                jobName: $row["job_name"],

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
        // Generate bytes from UUID for Mysql version used.
        $uuid = Uuid::fromString($id)->getBytes();
        // Prapare the statement for link data.
        $query =
            "SELECT HEX(id_person) as id_person, HEX(id) as id, l.value, l.created_at, l.updated_at 
            FROM link
            WHERE id_person = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        $stmt->bind_param("s", $uuid);
        // Run SQL Command and fetch result.
        $stmt->execute();
        $linksResult = $stmt->get_result();
        // Parse links.
        /** @var Link[] */
        $links = [];
        while ($row = $linksResult->fetch_assoc()) {
            array_push($links, new Link(
                id: $row["id"],
                value: $row["l.value"],
                createdAt: $row["l.created_at"],
                updatedAt: $row["l.updated_at"],
            ));
        }
        // Prapare the statement for message data.
        $query =
            "SELECT HEX(id_person) as id_person, HEX(id) as id, m.value, m.created_at, m.updated_at 
            FROM message
            WHERE id_person = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        $stmt->bind_param("s", [$uuid]);
        // Run SQL Command and fetch result.
        $stmt->execute();
        $messagesResult = $stmt->get_result();
        // Parse messages.
        /** @var Message[] */
        $messages = [];
        while ($row = $messagesResult->fetch_assoc()) {
            array_push($messages, new Message(
                id: $row["id"],
                value: $row["m.value"],
                createdAt: $row["m.created_at"],
                updatedAt: $row["m.updated_at"],
            ));
        }
        // Prapare the statement for person data.
        /** @var string */
        $query =
            "SELECT HEX(id) as id, first_name, last_name, job_name, id_zone, created_at, updated_at, zone.id, zone.name 
            FROM person 
            INNER JOIN zone ON zone.id = id_zone 
            WHERE id = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        $stmt->bind_param("s", [$uuid]);
        // Run SQL Command and fetch result.
        $stmt->execute();
        $PersonsResult = $stmt->get_result();
        // Parse persons.
        /** @var PersonDetailed[] */
        $persons = [];
        while ($row = $PersonsResult->fetch_assoc()) {
            array_push($persons, new PersonDetailed(
                id: $row["id"],
                firstName: $row["first_name"],
                lastName: $row["last_name"],
                jobName: $row["job_name"],

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
        $query = "INSERT INTO person (id, first_name, last_name, job_name, created_at, updated_at, id_zone) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $uuid = Uuid::fromString($person->id)->getBytes();
        $stmt->bind_param("issiis", [$uuid, $person->firstName, $person->lastName, $person->jobName, $person->createdAt, $person->updatedAt, $person->zone->id]);
        // Run SQL Command and fetch result.
        $stmt->execute();
    }

    function addMessage(string $id, Message $message): void
    {
        // Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO message (id, value, id_person, created_at, updated_at) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $person_id = Uuid::fromString($id)->getBytes();
        $message_id = Uuid::fromString($message->id)->getBytes();
        $stmt->bind_param("issii", [$message_id, $message->value, $person_id, $message->createdAt, $message->updatedAt]);
        // Run SQL Command and fetch result.
        $stmt->execute();
    }

    function addLink(string $id, Link $link): void
    {
        // Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO link (id, value) VALUES (?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $person_id = Uuid::fromString($id)->getBytes();
        $link_id = Uuid::fromString($link->id)->getBytes();
        $stmt->bind_param("issii", [$link_id, $link->value, $person_id, $link->createdAt, $link->updatedAt]);
        // Run SQL Command and fetch result.
        $stmt->execute();
    }
}
