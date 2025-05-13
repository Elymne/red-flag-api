<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Models\Person;
use Domain\Models\PersonDetailed;
use Domain\Models\Link;
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
        int|null $birthday = null,
        string|null $zonename = null,
        string|null $jobname = null,
    ): array {
        // * Prapare the statement.
        /** @var string */
        $query = "SELECT HEX(person.id) as id, first_name, last_name, job_name, birthday, id_zone, created_at, updated_at, zone.id as zone_id, zone.name as zone_name 
        FROM person INNER JOIN zone ON zone.id = id_zone 
        WHERE 1=1";
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
        // * Inject the value.
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }
        // * Run SQL Command and fetch result.
        $stmt->execute();
        $result = $stmt->get_result();
        // * Parse persons.
        /** @var Person[] */
        $persons = [];
        while ($row = $result->fetch_assoc()) {
            array_push($persons, new Person(
                id: $row["id"],
                firstName: $row["first_name"],
                lastName: $row["last_name"],
                jobName: $row["job_name"],
                birthday: $row["birthday"],

                // * Data from Remote API or nothing.
                portrait: null,

                zone: new Zone(
                    id: $row["zone_id"],
                    name: $row["zone_name"],
                ),

                createdAt: $row["created_at"],
                updatedAt: $row["updated_at"]
            ));
        }
        return $persons;
    }

    public function findUnique(string $id): PersonDetailed|null
    {
        // * Generate bytes from UUID for Mysql version used.
        $uuid = Uuid::fromString($id)->getBytes();
        // * Prapare the statement for link data.
        $query =
            "SELECT HEX(id_person) as id_person, HEX(id) as id, l.value, l.created_at, l.updated_at 
            FROM link
            WHERE id_person = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // * Inject the value.
        $stmt->bind_param("s", $uuid);
        // * Run SQL Command and fetch result.
        $stmt->execute();
        $linksResult = $stmt->get_result();
        // * Parse links.
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

        // * Prapare the statement for person data.
        /** @var string */
        $query =
            "SELECT HEX(person.id) as id, first_name, last_name, job_name, birthday, id_zone, created_at, updated_at, zone.id, zone.name 
            FROM person 
            INNER JOIN zone ON zone.id = id_zone 
            WHERE id = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // * Inject the value.
        $stmt->bind_param("s", [$uuid]);
        // * Run SQL Command and fetch result.
        $stmt->execute();
        $PersonsResult = $stmt->get_result();
        // * Parse persons.
        /** @var PersonDetailed[] */
        $persons = [];
        while ($row = $PersonsResult->fetch_assoc()) {
            array_push($persons, new PersonDetailed(
                id: $row["id"],
                firstName: $row["first_name"],
                lastName: $row["last_name"],
                jobName: $row["job_name"],
                birthday: $row["birthday"],

                // * Data from Remote API or nothing.
                portrait: null,
                description: null,

                zone: new Zone(
                    id: $row["zone.id"],
                    name: $row["zone.name"],
                ),

                links: $links,

                createdAt: $row["created_at"],
                updatedAt: $row["updated_at"]
            ));
        }
        // * Check if data exists, return null when it's not the case.
        if (count($persons) == 0) {
            return null;
        }
        // * Return the first value.
        return $persons[0];
    }


    function createOne(Person $person): void
    {
        // * Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO person (id, first_name, last_name, job_name, birthday, created_at, updated_at, id_zone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // * Inject values. It's PHP so look at this, that's insane.
        $uuid = $person->id;
        $firstname = $person->firstName;
        $lastName = $person->lastName;
        $jobName = $person->jobName;
        $birthday = $person->birthday;
        $createdAt = $person->createdAt;
        $updatedAt = $person->updatedAt;
        $zoneID =  $person->zone->id;
        // * Bind aprams.
        $stmt->bind_param(
            "bsssiiis",
            $uuid,
            $firstname,
            $lastName,
            $jobName,
            $birthday,
            $createdAt,
            $updatedAt,
            $zoneID
        );
        // * Using bytes force me to use this.
        $stmt->send_long_data(0, $uuid);
        // * Run SQL Command and fetch result.
        $stmt->execute();
    }

    function addLink(string $id, Link $link): void
    {
        // * Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO link (id, value) VALUES (?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // * Inject values.
        $person_id = Uuid::fromString($id)->getBytes();
        $link_id = Uuid::fromString($link->id)->getBytes();
        $stmt->bind_param("issii", [$link_id, $link->value, $person_id, $link->createdAt, $link->updatedAt]);
        // * Run SQL Command and fetch result.
        $stmt->execute();
    }
}
