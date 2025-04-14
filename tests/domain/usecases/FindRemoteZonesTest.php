<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Domain\Models\Zone;
use Domain\Repositories\RemoteZoneRepository;
use Domain\Usecases\FindRemoteZones;
use Domain\Usecases\FindZonesParams;

class FindRemoteZonesTest extends TestCase
{
    private $_remoteZoneRepository;

    protected function setUp(): void
    {
        $this->_remoteZoneRepository = Mockery::mock(RemoteZoneRepository::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_findEmpty(): void
    {
        $this->_remoteZoneRepository->allows()->findMany("random place in Outer world")->andReturns([]);
        $findRemoteZones = new FindRemoteZones($this->_remoteZoneRepository);

        $result = $findRemoteZones->perform(new FindZonesParams(name: "random place in Outer world"));

        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, []);
    }

    public function test_findMany(): void
    {
        $data = [
            new Zone(id: "00", name: "Somewhere"),
            new Zone(id: "01", name: "Anywhere"),
        ];

        $this->_remoteZoneRepository->allows()->findMany("where")->andReturns($data);
        $findRemoteZones = new FindRemoteZones($this->_remoteZoneRepository);

        $result = $findRemoteZones->perform(new FindZonesParams(name: "where"));


        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, $data);
    }

    public function test_findWithEmptyParams(): void
    {
        $data = [
            new Zone(id: "00", name: "Somewhere"),
            new Zone(id: "01", name: "Anywhere"),
        ];
        $this->_remoteZoneRepository->allows()->findMany(null)->andReturns($data);
        $findRemoteZones = new FindRemoteZones($this->_remoteZoneRepository);

        $result = $findRemoteZones->perform(new FindZonesParams());


        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, $data);
    }

    public function test_findWithWrongParams(): void
    {
        $this->_remoteZoneRepository->allows()->findMany(null)->andReturns([]);
        $findRemoteZones = new FindRemoteZones($this->_remoteZoneRepository);

        $result = $findRemoteZones->perform("String params");


        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the data send from body is not correct. Should be a InsertMessageParams structure.");
    }

    public function test_findButExceptionFromRepo(): void
    {
        $this->_remoteZoneRepository->allows()->findMany(null)->andThrow(new \Exception("Repository exception"));
        $findRemoteZones = new FindRemoteZones($this->_remoteZoneRepository);

        $result = $findRemoteZones->perform(new FindZonesParams());

        $this->assertSame($result->code, 500);
        $this->assertSame($result->data, "Action failure : Internal Server Error.");
    }
}
