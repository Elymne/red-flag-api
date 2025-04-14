<?php

declare(strict_types=1);

use Domain\Models\Person;
use Domain\Models\Zone;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\LocalZoneRepository;
use Domain\Repositories\RemoteZoneRepository;
use Domain\Repositories\UuidRepository;
use Domain\Usecases\InsertPerson;
use Domain\Usecases\InsertPersonParams;
use Infra\Datasources\DBConnect;
use PHPUnit\Framework\TestCase;

class InsertPersonTest extends TestCase
{
    private $_uuidRepository;
    private $_db;
    private $_mysqli;
    private $_remoteZoneRepository;
    private $_localPersonRepository;
    private $_localZoneRepository;

    protected function setUp(): void
    {
        $this->_uuidRepository = Mockery::mock(UuidRepository::class);
        $this->_db = Mockery::mock(DBConnect::class);
        $this->_mysqli = Mockery::mock(mysqli::class);
        $this->_remoteZoneRepository = Mockery::mock(RemoteZoneRepository::class);
        $this->_localPersonRepository = Mockery::mock(LocalPersonRepository::class);
        $this->_localZoneRepository = Mockery::mock(LocalZoneRepository::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_wrongParams(): void
    {
        $insertPerson = new InsertPerson($this->_uuidRepository, $this->_db, $this->_remoteZoneRepository, $this->_localPersonRepository, $this->_localZoneRepository);
        $result = $insertPerson->perform("");

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the data send from body is not correct. Should be a InsertPersonParams structure.");
    }

    public function test_wrongZone(): void
    {
        $zoneID = "01";
        $params = new InsertPersonParams(
            firstname: "f",
            lastname: "l",
            zoneID: $zoneID
        );

        $this->_mysqli->allows()->begin_transaction()->andReturns(true);
        $this->_db->allows()->getMysqli()->andReturns($this->_mysqli);

        $this->_localZoneRepository->allows()->findUnique($zoneID)->andReturns(null);
        $this->_remoteZoneRepository->allows()->findUnique($zoneID)->andReturns(null);

        $insertPerson = new InsertPerson($this->_uuidRepository, $this->_db, $this->_remoteZoneRepository, $this->_localPersonRepository, $this->_localZoneRepository);
        $result = $insertPerson->perform($params);

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the zone code does not exists in remote datasource.");
    }

    public function test_userExists(): void
    {
        $zoneID = "01";
        $zone = new Zone(
            id: $zoneID,
            name: "z",
        );

        $personID = "01";
        $person = new Person(
            id: $personID,
            firstName: "f",
            lastName: "l",
            zone: $zone,
            createdAt: 0,
        );

        $params = new InsertPersonParams(
            firstname: $person->firstName,
            lastname: $person->lastName,
            zoneID: $zoneID,
        );

        $this->_mysqli->allows()->begin_transaction()->andReturns(true);
        $this->_db->allows()->getMysqli()->andReturns($this->_mysqli);

        $this->_localZoneRepository->allows()->findUnique($zoneID)->andReturns($zone);
        $this->_localPersonRepository->allows()->findMany($params->firstname, $params->lastname, $zone->name)->andReturns([$person, $person]);

        $insertPerson = new InsertPerson($this->_uuidRepository, $this->_db, $this->_remoteZoneRepository, $this->_localPersonRepository, $this->_localZoneRepository);
        $result = $insertPerson->perform($params);

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the person already exists in database.");
    }

    public function test_insertZoneNotExists(): void
    {
        $zoneID = "01";
        $zone = new Zone(
            id: $zoneID,
            name: "z",
        );

        $personID = "01";
        $person = new Person(
            id: $personID,
            firstName: "f",
            lastName: "l",
            zone: $zone,
            createdAt: 0,
        );

        $params = new InsertPersonParams(
            firstname: $person->firstName,
            lastname: $person->lastName,
            zoneID: $zoneID,
        );

        $this->_mysqli->allows()->begin_transaction()->andReturns(true);
        $this->_mysqli->allows()->commit()->andReturns(true);
        $this->_db->allows()->getMysqli()->andReturns($this->_mysqli);

        $this->_localZoneRepository->allows()->findUnique($zoneID)->andReturns(null);
        $this->_remoteZoneRepository->allows()->findUnique($zoneID)->andReturns($zone);
        $this->_localPersonRepository->allows()->findMany($person->firstName, $person->lastName, $zone->name)->andReturns([]);

        // Here, arg is relative to time, it mean when cannot predict this value.
        $this->_localZoneRepository->shouldReceive("createOne")->andReturns(null);

        $this->_uuidRepository->allows()->generate()->andReturns($zoneID);

        // Here, arg is relative to time, it mean when cannot predict this value.
        $this->_localPersonRepository->shouldReceive("createOne")->andReturns(null);

        $insertPerson = new InsertPerson($this->_uuidRepository, $this->_db, $this->_remoteZoneRepository, $this->_localPersonRepository, $this->_localZoneRepository);
        $result = $insertPerson->perform($params);

        $this->assertSame($result->code, 201);
        $this->assertSame($result->data, "Action success : new entry in person database.");
    }

    public function test_insertZoneExists(): void
    {
        $zoneID = "01";
        $zone = new Zone(
            id: $zoneID,
            name: "z",
        );

        $personID = "01";
        $person = new Person(
            id: $personID,
            firstName: "f",
            lastName: "l",
            zone: $zone,
            createdAt: 0,
        );

        $params = new InsertPersonParams(
            firstname: $person->firstName,
            lastname: $person->lastName,
            zoneID: $zone->id,
        );

        $this->_mysqli->allows()->begin_transaction()->andReturns(true);
        $this->_mysqli->allows()->commit()->andReturns(true);
        $this->_db->allows()->getMysqli()->andReturns($this->_mysqli);

        $this->_localZoneRepository->allows()->findUnique($zoneID)->andReturns($zone);
        $this->_localPersonRepository->allows()->findMany($params->firstname, $params->lastname, $zone->name)->andReturns([]);
        $this->_uuidRepository->allows()->generate()->andReturns($zoneID);
        // Here, arg is relative to time, it mean when cannot predict this value.
        $this->_localPersonRepository->shouldReceive("createOne")->andReturns(null);

        $insertPerson = new InsertPerson($this->_uuidRepository, $this->_db, $this->_remoteZoneRepository, $this->_localPersonRepository, $this->_localZoneRepository);
        $result = $insertPerson->perform($params);

        $this->assertSame($result->code, 201);
        $this->assertSame($result->data, "Action success : new entry in person database.");
    }

    public function test_exception(): void
    {
        $params = new InsertPersonParams(
            firstname: "f",
            lastname: "l",
            zoneID: "01",
        );

        $this->_mysqli->allows()->begin_transaction()->andThrow(new \Exception("Repository exception"));
        $this->_db->allows()->getMysqli()->andReturns($this->_mysqli);

        $insertPerson = new InsertPerson($this->_uuidRepository, $this->_db, $this->_remoteZoneRepository, $this->_localPersonRepository, $this->_localZoneRepository);
        $result = $insertPerson->perform($params);

        $this->assertSame($result->code, 500);
        $this->assertSame($result->data, "Action failure : Internal Server Error.");
    }
}
