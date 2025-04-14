<?php

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Models\Person;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\LocalZoneRepository;
use Domain\Repositories\RemoteZoneRepository;
use Infra\Datasources\DBConnect;
use Ramsey\Uuid\Uuid;
use Throwable;

class InsertPerson extends Usecase
{
    private DBConnect $_db;
    private RemoteZoneRepository $_remoteZoneRepository;
    private LocalPersonRepository $_localPersonRepository;
    private LocalZoneRepository $_localZoneRepository;

    public function __construct(
        DBConnect $db,
        RemoteZoneRepository $remoteZoneRepository,
        LocalPersonRepository $localPersonRepository,
        LocalZoneRepository $localCityRepository
    ) {
        $this->_db = $db;
        $this->_remoteZoneRepository = $remoteZoneRepository;
        $this->_localPersonRepository = $localPersonRepository;
        $this->_localZoneRepository = $localCityRepository;
    }

    /**
     * @param InsertPersonParams $params
     */
    public function perform(mixed $params): Result
    {
        try {
            // Check $params type.
            if (!isset($params) || !($params instanceof InsertPersonParams)) {
                return new Result(code: 400, data: "Action failure : the data send from body is not correct. Should be a InsertPersonParams structure.");
            }

            // Start transaction
            $this->_db->getMysqli()->begin_transaction();

            // Check if the zone send from the params exists in our database. When user create a new person, the data that is used for city localisation is provided by a remote datassouce.
            $zone = $this->_localZoneRepository->findUnique($params->zoneID);
            if (!$zone) {
                // Trying to fetch the zone from the remote repo. If it doesn't exists, we just send an error response.
                $zone = $this->_remoteZoneRepository->findUnique($params->zoneID);
                if (!$zone) {
                    return new Result(code: 400, data: "Action failure : the zone code does not exists in remote datasource.");
                }
                $zone = $this->_localZoneRepository->createOne($zone);
            }

            // Check that user does not exists in database. If it's the case, we just send a 200 response.
            $usersBeLike = $this->_localPersonRepository->findMany(firstname: $params->firstname, lastname: $params->lastname, zonename: $zone->id);
            if (count($usersBeLike) != 0) {
                return new Result(code: 400, data: "Action failure : the person already exists in database.");
            }

            // Insert the new Person to database.
            $this->_localPersonRepository->createOne(
                new Person(
                    id: Uuid::uuid4(),
                    firstName: $params->firstname,
                    lastName: $params->lastname,
                    createdAt: time(),
                    updatedAt: null,
                    zone: $zone
                )
            );

            /* Commit transaction */
            $this->_db->getMysqli()->commit();

            // Success response.
            return new Result(code: 201, data: "Action success : new entry in person database.");
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
