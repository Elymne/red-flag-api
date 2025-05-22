<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Models\Activity;
use Domain\Models\Company;
use Domain\Models\Person;
use Domain\Models\Link;
use Domain\Models\Zone;
use Domain\Repositories\LocalPersonRepository;
use mysqli_result;

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
        int|null $birthDate = null,
        string|null $activityID = null,
        string|null $companyID = null,
        string|null $zoneID = null,
    ): array {
        // * Prepare the statement.
        /** @var string */
        $query = "SELECT HEX(person.id) as id, first_name, last_name, birth_date, id_zone, id_company, id_activity, created_at FROM person WHERE 1=1";
        $types = "";
        $params = [];
        if (isset($firstname)) {
            $query .= " AND first_name = ?";
            $types .= "s";
            array_push($params, $firstname);
        }
        if (isset($lastname)) {
            $query .= " AND last_name = ?";
            $types .= "s";
            array_push($params, $lastname);
        }
        if (isset($birthDate)) {
            $query .= " AND birth_date = ?";
            $types .= "i";
            array_push($params, $birthDate);
        }
        if (!is_null($activityID)) {
            $query .= " AND id_activity = ?";
            $types .= "s";
            array_push($params, $activityID);
        }
        if (!is_null($companyID)) {
            $query .= " AND id_company = ?";
            $types .= "s";
            array_push($params, $companyID);
        }
        if (!is_null($zoneID)) {
            $query .= " AND id_zone = ?";
            $types .= "s";
            array_push($params, $zoneID);
        }
        $stmt = $this->_db->getMysqli()->prepare($query);

        // * Inject values.
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // * Run SQL Command and fetch result.
        $stmt->execute();
        $result = $stmt->get_result();

        // * Parse persons.
        /** @var Person[] */
        $persons = $this->_parsePersons($result);
        return $persons;
    }

    public function doesExists(
        string $firstname,
        string $lastname,
        int $birthDate,
        string $zoneID,
        string|null $companyID,
        string|null $activityID,
    ): bool {
        // * Prepare the statement.
        /** @var string */
        $query = "SELECT count(*) as nb FROM person WHERE first_name = ? AND last_name = ? AND birth_date = ? AND id_zone = ?";
        /** @var string */
        $types = "ssis";
        /** @var mixed[] */
        $params = [
            $firstname,
            $lastname,
            $birthDate,
            $zoneID
        ];
        if (isset($companyID)) {
            $query .= " AND id_company = ?";
            $types .= "s";
            array_push($params, $companyID);
        }
        if (isset($activityID)) {
            $query .= " AND id_activity = ?";
            $types .= "s";
            array_push($params, $activityID);
        }

        $stmt = $this->_db->getMysqli()->prepare($query);

        // * Inject values.
        $stmt->bind_param($types, ...$params);

        // * Run SQL Command and fetch result.
        $stmt->execute();
        $result = $stmt->get_result();

        // * Get the first line only.
        $nb = mysqli_fetch_assoc($result)["nb"];

        // * Check if ppl exists or not.
        return $nb > 0;
    }

    public function findUnique(string $ID): Person|null
    {
        // * Prapare the statement for link data.
        $query = "SELECT HEX(id_person) as id_person, HEX(id) as link_id, source, created_at FROM link WHERE id_person = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);

        // * Inject the value.
        $stmt->bind_param("s", $ID);

        // * Using bytes force me to use this.
        $stmt->send_long_data(0, $ID);

        // * Run SQL Command and fetch result.
        $stmt->execute();
        $linksResult = $stmt->get_result();

        // * Parse links.
        /** @var Link[] */
        $links = $this->_parseLinks($linksResult);

        // * Prapare the statement for person data.
        /** @var string */
        $query = "SELECT HEX(person.id) as id, first_name, last_name, birth_date, id_zone, id_company, id_activity, created_at FROM person WHERE id = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);

        // * Inject the value.
        $stmt->bind_param("s", $ID);

        // * Using bytes force me to use this.
        $stmt->send_long_data(0, $ID);

        // * Run SQL Command and fetch result.
        $stmt->execute();
        $PersonsResult = $stmt->get_result();

        // * Parse persons.
        /** @var Person[] */
        $persons = $this->_parsePersons($PersonsResult, $links);

        // * Check if data exists, return null when it's not the case.
        if (count($persons) == 0) {
            return null;
        }

        // * Return the first value.
        return $persons[0];
    }


    public function createOne(Person $person): void
    {
        // * Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO person (id, first_name, last_name, birth_date, id_zone, id_company, id_activity, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);

        // ? bind_param apply changes to binded params.
        $uuid = $person->ID;
        $firstname = $person->firstname;
        $lastName = $person->lastname;
        $birthDate = $person->birthDate;
        $zoneID = $person->zone->ID;
        $companyID = $person->company->ID;
        $activityID = $person->activity->ID;
        $created_at =  $person->createdAt;

        // * Inject params.
        $stmt->bind_param(
            "bssisssi",
            $uuid,
            $firstname,
            $lastName,
            $birthDate,
            $zoneID,
            $companyID,
            $activityID,
            $created_at
        );

        // * I need to use this because I'm using Bytes type for UUID.
        $stmt->send_long_data(0, $uuid);

        // * Run SQL Command.
        $stmt->execute();
    }

    public function addLink(string $personID, Link $link): void
    {
        // * Prepare statement for person.
        /** @var string */
        $query = "INSERT INTO link (id, source, created_at, id_person) VALUES (?, ?, ?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);

        // ? bind_param apply changes to binded params.
        $linkID = $link->ID;
        $linkSource = $link->source;
        $linkCreatedAt = $link->createdAt;

        // * Inject params.
        $stmt->bind_param("bsib", $linkID, $linkSource, $linkCreatedAt, $personID);

        // * I need to use this because I'm using Bytes type for UUID.
        $stmt->send_long_data(0, $linkID);
        $stmt->send_long_data(3, $personID);

        // * Run SQL Command and fetch result.
        $stmt->execute();
    }

    /**
     * @param mysqli_result $result
     * @return Link[]
     */
    private function _parseLinks(mysqli_result $result): array
    {
        /** @var Link[] */
        $links = [];
        while ($row = $result->fetch_assoc()) {
            array_push($links, new Link(
                ID: $row["id"],
                source: $row["source"],
                createdAt: intval($row["created_at"]),

            ));
        }
        return $links;
    }

    /**
     * @param mysqli_result $result
     * @return Person[]
     */
    private function _parsePersons(mysqli_result $result, array $links = []): array
    {
        /** @var Person[] */
        $persons = [];
        while ($row = $result->fetch_assoc()) {
            array_push($persons, new Person(
                ID: $row["id"],
                firstname: $row["first_name"],
                lastname: $row["last_name"],
                pseudonym: null,
                birthDate: intval($row["birth_date"]),
                zone: new Zone(
                    ID: $row["id_zone"],
                    name: "",
                ),
                activity: new Activity(
                    ID: $row["id_activity"],
                    name: "",
                ),
                company: new Company(
                    ID: $row["id_company"],
                    name: "",
                    address: "",
                ),
                links: $links,
                createdAt: intval($row["created_at"]),
                portrait: null,
                description: null,
            ));
        }
        return $persons;
    }
}
