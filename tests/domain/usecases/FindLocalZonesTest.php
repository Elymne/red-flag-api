<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Domain\Models\Zone;
use Domain\Repositories\LocalZoneRepository;
use Domain\Usecases\FindLocalZones;
use Domain\Usecases\FindZonesParams;

class FindLocalZonesTest extends TestCase
{
    private $_localZoneRepository;

    protected function setUp(): void
    {
        $this->_localZoneRepository = Mockery::mock(LocalZoneRepository::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_findEmpty(): void
    {
        $this->_localZoneRepository->allows()->findMany("random place in Outer world")->andReturns([]);
        $findLocalZones = new FindLocalZones($this->_localZoneRepository);

        $result = $findLocalZones->perform(new FindZonesParams(name: "random place in Outer world"));

        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, []);
    }

    public function test_findMany(): void
    {
        $data = [
            new Zone(id: "00", name: "Somewhere"),
            new Zone(id: "01", name: "Anywhere"),
        ];

        $this->_localZoneRepository->allows()->findMany("where")->andReturns($data);
        $findLocalZones = new FindLocalZones($this->_localZoneRepository);

        $result = $findLocalZones->perform(new FindZonesParams(name: "where"));


        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, $data);
    }

    public function test_findWithEmptyParams(): void
    {
        $data = [
            new Zone(id: "00", name: "Somewhere"),
            new Zone(id: "01", name: "Anywhere"),
        ];
        $this->_localZoneRepository->allows()->findMany(null)->andReturns($data);
        $findLocalZones = new FindLocalZones($this->_localZoneRepository);

        $result = $findLocalZones->perform(new FindZonesParams());


        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, $data);
    }

    public function test_findWithWrongParams(): void
    {
        $this->_localZoneRepository->allows()->findMany(null)->andReturns([]);
        $findLocalZones = new FindLocalZones($this->_localZoneRepository);

        $result = $findLocalZones->perform("String params");


        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the data send from body is not correct. Should be a InsertMessageParams structure.");
    }

    public function test_findButExceptionFromRepo(): void
    {
        $this->_localZoneRepository->allows()->findMany(null)->andThrow(new \Exception("Repository exception"));
        $findLocalZones = new FindLocalZones($this->_localZoneRepository);

        $result = $findLocalZones->perform(new FindZonesParams());

        $this->assertSame($result->code, 500);
        $this->assertSame($result->data, "Action failure : Internal Server Error.");
    }
}
