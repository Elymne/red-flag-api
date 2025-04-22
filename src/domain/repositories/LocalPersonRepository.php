<?php

namespace Domain\Repositories;

use Domain\Models\Person;
use Domain\Models\Link;

interface LocalPersonRepository
{
    /**
     * Find all the persons from our database that could correspond to the description here.
     * When no params are given, we should return an empty array.
     * 
     * @param string|null $firstname
     * @param string|null $lastname
     * @param int|null $birthDate
     * @param string|null $activityName
     * @param string|null $companyName
     * @param string|null $zoneName
     * @return Person[]
     */
    function findMany(
        string|null $firstname = null,
        string|null $lastname = null,
        int|null $birthDate = null,
        string|null $activityID = null,
        string|null $companyID = null,
        string|null $zoneID = null,
    ): array;

    /**
     * Find all the persons from our database that could correspond to the description here.
     * When no params are given, we should return an empty array.
     * 
     * @param string $firstname
     * @param string $lastname
     * @param int $birthDate
     * @param string $activityID
     * @param string $companyID
     * @param string $zoneID
     * @return bool
     */
    function doesExists(
        string $firstname,
        string $lastname,
        int $birthDate,
        string $activityID,
        string $companyID,
        string $zoneID,
    ): bool;

    /**
     * Find the person given his ID
     *  
     * @param string $id UUID bytes of person.
     * @return Person|null
     */
    function findUnique(
        string $ID
    ): Person|null;

    /**
     * Insert a new person in local database.
     * If a person with the same data exists, we return an exception.
     * 
     * @param Person $person Data of the person entry that will be added to database.
     */
    function createOne(
        Person $person
    ): void;

    /**
     * Add a link (resource, article) about a person in database.
     * 
     * @param string $personID UUID bytes of person related to the link.
     * @param Link $link The new message to add to the person.
     */
    function addLink(
        string $personID,
        Link $link
    ): void;
}
